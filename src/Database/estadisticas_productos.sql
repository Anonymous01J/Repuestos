-- Estadísticas de ventas, rentabilidad y ganancia bruta por producto.
-- Compatible con MariaDB 10.4+ (esquema repuestos).
--
-- Supuesto de costo: costo promedio ponderado de las entradas del producto
-- registradas hasta la fecha/hora de cada venta. Si no hay compras anteriores
-- para una línea, su costo/ganancia se deja como NULL y no participa en los
-- rankings de rentabilidad o ganancia.
--
-- Aplicar conectado a la base `repuestos`.

DROP PROCEDURE IF EXISTS sp_estadisticas_productos;
DROP VIEW IF EXISTS v_ventas_producto_ganancia;
DROP VIEW IF EXISTS v_detalle_venta_costo_promedio;

CREATE VIEW v_detalle_venta_costo_promedio AS
SELECT
    dv.id AS id_detalle_venta,
    dv.id_venta,
    v.fecha AS fecha_venta,
    dv.id_producto,
    p.codigo_producto,
    p.nombre AS producto,
    p.id_categoria,
    c.nombre AS categoria,
    dv.cantidad,
    dv.precio_unitario,
    dv.precio_unitario_bs,
    dv.subtotal AS venta_usd,
    dv.subtotal_bs AS venta_bs,
    dv.precio_al_mayor AS uso_precio_al_mayor,
    (
        SELECT SUM(di.cantidad * di.precio_compra) / NULLIF(SUM(di.cantidad), 0)
        FROM detalle_ingreso AS di
        INNER JOIN ingresos AS i ON i.id = di.id_ingreso
        WHERE di.id_producto = dv.id_producto
          AND i.fecha <= v.fecha
    ) AS costo_promedio_unitario_usd,
    (
        SELECT SUM(di.cantidad * di.precio_compra_bs) / NULLIF(SUM(di.cantidad), 0)
        FROM detalle_ingreso AS di
        INNER JOIN ingresos AS i ON i.id = di.id_ingreso
        WHERE di.id_producto = dv.id_producto
          AND i.fecha <= v.fecha
    ) AS costo_promedio_unitario_bs
FROM detalle_venta AS dv
INNER JOIN ventas AS v ON v.id = dv.id_venta
LEFT JOIN productos AS p ON p.id = dv.id_producto
LEFT JOIN categorias AS c ON c.id = p.id_categoria;

CREATE VIEW v_ventas_producto_ganancia AS
SELECT
    dvc.*,
    CASE
        WHEN dvc.costo_promedio_unitario_usd IS NULL THEN NULL
        ELSE dvc.costo_promedio_unitario_usd * dvc.cantidad
    END AS costo_mercancia_usd,
    CASE
        WHEN dvc.costo_promedio_unitario_bs IS NULL THEN NULL
        ELSE dvc.costo_promedio_unitario_bs * dvc.cantidad
    END AS costo_mercancia_bs,
    CASE
        WHEN dvc.costo_promedio_unitario_usd IS NULL THEN NULL
        ELSE dvc.venta_usd - (dvc.costo_promedio_unitario_usd * dvc.cantidad)
    END AS ganancia_bruta_usd,
    CASE
        WHEN dvc.costo_promedio_unitario_bs IS NULL THEN NULL
        ELSE dvc.venta_bs - (dvc.costo_promedio_unitario_bs * dvc.cantidad)
    END AS ganancia_bruta_bs
FROM v_detalle_venta_costo_promedio AS dvc;

DELIMITER $$

CREATE PROCEDURE sp_estadisticas_productos(
    IN p_periodo VARCHAR(20),
    IN p_fecha_desde DATE,
    IN p_fecha_hasta DATE,
    IN p_categoria_id INT
)
BEGIN
    DECLARE v_fecha_desde DATE DEFAULT NULL;
    DECLARE v_fecha_hasta DATE DEFAULT CURDATE();
    DECLARE v_periodo VARCHAR(20);

    SET v_periodo = UPPER(TRIM(COALESCE(p_periodo, 'TODO')));

    IF v_periodo = 'MES' THEN
        SET v_fecha_desde = DATE_SUB(CURDATE(), INTERVAL DAYOFMONTH(CURDATE()) - 1 DAY);
        SET v_fecha_hasta = CURDATE();
    ELSEIF v_periodo = 'ULTIMOS_6_MESES' THEN
        SET v_fecha_desde = DATE_SUB(CURDATE(), INTERVAL 6 MONTH);
        SET v_fecha_hasta = CURDATE();
    ELSEIF v_periodo = 'ANIO' THEN
        SET v_fecha_desde = MAKEDATE(YEAR(CURDATE()), 1);
        SET v_fecha_hasta = CURDATE();
    ELSEIF v_periodo = 'INTERVALO' THEN
        IF p_fecha_desde IS NULL OR p_fecha_hasta IS NULL THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Para INTERVALO se requieren p_fecha_desde y p_fecha_hasta.';
        END IF;
        IF p_fecha_desde > p_fecha_hasta THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'La fecha inicial no puede ser posterior a la fecha final.';
        END IF;
        SET v_fecha_desde = p_fecha_desde;
        SET v_fecha_hasta = p_fecha_hasta;
    ELSEIF v_periodo = 'TODO' THEN
        SET v_fecha_desde = NULL;
        SET v_fecha_hasta = CURDATE();
    ELSE
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Periodo no válido. Use MES, ULTIMOS_6_MESES, ANIO, INTERVALO o TODO.';
    END IF;

    DROP TEMPORARY TABLE IF EXISTS tmp_estadisticas_productos;

    CREATE TEMPORARY TABLE tmp_estadisticas_productos ENGINE=InnoDB AS
    SELECT
        p.id AS id_producto,
        p.codigo_producto,
        p.nombre AS producto,
        p.id_categoria,
        c.nombre AS categoria,
        COALESCE(v.unidades_vendidas, 0) AS unidades_vendidas,
        COALESCE(v.ventas_usd, 0.00) AS ventas_usd,
        COALESCE(v.ventas_bs, 0.00) AS ventas_bs,
        v.costo_vendido_usd,
        v.costo_vendido_bs,
        v.ganancia_bruta_usd,
        v.ganancia_bruta_bs,
        CASE
            WHEN COALESCE(v.unidades_vendidas, 0) = 0
              OR COALESCE(v.lineas_sin_costo, 0) > 0
              OR COALESCE(v.costo_vendido_usd, 0) <= 0
            THEN NULL
            ELSE (v.ganancia_bruta_usd / v.costo_vendido_usd) * 100
        END AS rentabilidad_pct,
        COALESCE(v.lineas_venta, 0) AS lineas_venta,
        COALESCE(v.lineas_sin_costo, 0) AS lineas_sin_costo
    FROM productos AS p
    LEFT JOIN categorias AS c ON c.id = p.id_categoria
    LEFT JOIN (
        SELECT
            vv.id_producto,
            SUM(vv.cantidad) AS unidades_vendidas,
            SUM(vv.venta_usd) AS ventas_usd,
            SUM(vv.venta_bs) AS ventas_bs,
            SUM(CASE WHEN vv.costo_mercancia_usd IS NOT NULL THEN vv.costo_mercancia_usd ELSE 0 END) AS costo_vendido_usd,
            SUM(CASE WHEN vv.costo_mercancia_bs IS NOT NULL THEN vv.costo_mercancia_bs ELSE 0 END) AS costo_vendido_bs,
            CASE
                WHEN SUM(vv.costo_mercancia_usd IS NULL) > 0 THEN NULL
                ELSE SUM(vv.ganancia_bruta_usd)
            END AS ganancia_bruta_usd,
            CASE
                WHEN SUM(vv.costo_mercancia_bs IS NULL) > 0 THEN NULL
                ELSE SUM(vv.ganancia_bruta_bs)
            END AS ganancia_bruta_bs,
            COUNT(*) AS lineas_venta,
            SUM(vv.costo_mercancia_usd IS NULL) AS lineas_sin_costo
        FROM v_ventas_producto_ganancia AS vv
        WHERE (v_fecha_desde IS NULL OR vv.fecha_venta >= v_fecha_desde)
          AND vv.fecha_venta < DATE_ADD(v_fecha_hasta, INTERVAL 1 DAY)
          AND (p_categoria_id IS NULL OR vv.id_categoria = p_categoria_id)
        GROUP BY vv.id_producto
    ) AS v ON v.id_producto = p.id
    WHERE p_categoria_id IS NULL OR p.id_categoria = p_categoria_id;

    -- 1. Cinco productos con más unidades vendidas.
    SELECT id_producto, codigo_producto, producto, categoria, unidades_vendidas, ventas_usd, ventas_bs
    FROM tmp_estadisticas_productos
    WHERE unidades_vendidas > 0
    ORDER BY unidades_vendidas DESC, ventas_usd DESC, producto ASC
    LIMIT 5;

    -- 2. Cinco productos con menos unidades vendidas (solo productos vendidos).
    SELECT id_producto, codigo_producto, producto, categoria, unidades_vendidas, ventas_usd, ventas_bs
    FROM tmp_estadisticas_productos
    WHERE unidades_vendidas > 0
    ORDER BY unidades_vendidas ASC, ventas_usd ASC, producto ASC
    LIMIT 5;

    -- 3. Cinco productos con mayor rentabilidad porcentual.
    -- Rentabilidad = ganancia bruta / costo de mercancía * 100.
    SELECT id_producto, codigo_producto, producto, categoria, unidades_vendidas,
           costo_vendido_usd, ventas_usd, ganancia_bruta_usd, rentabilidad_pct
    FROM tmp_estadisticas_productos
    WHERE rentabilidad_pct IS NOT NULL
    ORDER BY rentabilidad_pct DESC, ganancia_bruta_usd DESC, producto ASC
    LIMIT 5;

    -- 4. Cinco productos con menor rentabilidad porcentual.
    SELECT id_producto, codigo_producto, producto, categoria, unidades_vendidas,
           costo_vendido_usd, ventas_usd, ganancia_bruta_usd, rentabilidad_pct
    FROM tmp_estadisticas_productos
    WHERE rentabilidad_pct IS NOT NULL
    ORDER BY rentabilidad_pct ASC, ganancia_bruta_usd ASC, producto ASC
    LIMIT 5;

    -- 5. Cinco productos con mayor ganancia bruta acumulada.
    SELECT id_producto, codigo_producto, producto, categoria, unidades_vendidas,
           ventas_usd, ventas_bs, costo_vendido_usd, ganancia_bruta_usd, ganancia_bruta_bs
    FROM tmp_estadisticas_productos
    WHERE ganancia_bruta_usd IS NOT NULL
    ORDER BY ganancia_bruta_usd DESC, producto ASC
    LIMIT 5;

    -- 6. Cinco productos con menor ganancia bruta acumulada.
    SELECT id_producto, codigo_producto, producto, categoria, unidades_vendidas,
           ventas_usd, ventas_bs, costo_vendido_usd, ganancia_bruta_usd, ganancia_bruta_bs
    FROM tmp_estadisticas_productos
    WHERE ganancia_bruta_usd IS NOT NULL
    ORDER BY ganancia_bruta_usd ASC, producto ASC
    LIMIT 5;

    DROP TEMPORARY TABLE IF EXISTS tmp_estadisticas_productos;
END$$

DELIMITER ;

-- Ejemplos (ejecutar aparte después de importar el script):
-- CALL sp_estadisticas_productos('MES', NULL, NULL, NULL);
-- CALL sp_estadisticas_productos('ULTIMOS_6_MESES', NULL, NULL, NULL);
-- CALL sp_estadisticas_productos('ANIO', NULL, NULL, 3); -- categoría id=3
-- CALL sp_estadisticas_productos('INTERVALO', '2026-01-01', '2026-03-31', NULL);
-- CALL sp_estadisticas_productos('TODO', NULL, NULL, NULL);
