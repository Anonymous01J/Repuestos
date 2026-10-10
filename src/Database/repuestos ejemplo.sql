-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-10-2026 a las 03:58:47
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `repuestos`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_estadisticas_productos` (IN `p_periodo` VARCHAR(20), IN `p_fecha_desde` DATE, IN `p_fecha_hasta` DATE, IN `p_categoria_id` INT)   BEGIN
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`) VALUES
(24, 'Aceites'),
(7, 'Amortiguadores'),
(21, 'Amortiguadores moto'),
(8, 'Baterías'),
(10, 'Bombas de agua'),
(4, 'Bujías'),
(20, 'Bujías moto'),
(16, 'Cadenas'),
(22, 'Carburadores'),
(9, 'Correas'),
(6, 'Discos de freno'),
(25, 'Espejos'),
(1, 'Filtros de aceite'),
(2, 'Filtros de aire'),
(23, 'Filtros de aire deportivos'),
(3, 'Filtros de combustible'),
(17, 'Kits de arrastre'),
(19, 'Neumáticos'),
(5, 'Pastillas de freno'),
(18, 'Pastillas de freno moto');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `cedula_rif` varchar(20) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `cedula_rif`, `telefono`, `direccion`, `estado`) VALUES
(1, 'Prueba', '30218990', '04125272511', '....', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ingreso`
--

CREATE TABLE `detalle_ingreso` (
  `id` int(11) NOT NULL,
  `id_ingreso` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_compra` decimal(10,2) NOT NULL,
  `precio_compra_bs` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `subtotal_bs` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `detalle_ingreso`
--

INSERT INTO `detalle_ingreso` (`id`, `id_ingreso`, `id_producto`, `cantidad`, `precio_compra`, `precio_compra_bs`, `subtotal`, `subtotal_bs`) VALUES
(1, 1, 1, 50, 19.00, 8000.00, 19.88, 8000.00),
(2, 2, 19, 20, 1.00, 402.33, 1.00, 402.33),
(2001, 2001, 1, 20, 4.80, 1931.18, 96.00, 38623.60),
(2002, 2002, 1, 30, 5.00, 2011.65, 150.00, 60349.50),
(2003, 2003, 1, 20, 5.20, 2092.00, 104.00, 41840.00),
(2004, 2006, 1, 35, 5.50, 2212.82, 192.50, 77498.70),
(2005, 2003, 19, 25, 6.00, 2413.98, 150.00, 60349.50),
(2006, 2004, 19, 30, 6.50, 2615.15, 195.00, 78454.50),
(2007, 2005, 19, 20, 6.80, 2735.94, 136.00, 54718.80),
(2008, 2007, 19, 35, 7.20, 2896.78, 252.00, 101387.30),
(2009, 2004, 2, 20, 8.00, 3218.64, 160.00, 64372.80),
(2010, 2005, 2, 15, 8.50, 3419.81, 127.50, 51297.15),
(2011, 2006, 2, 18, 9.00, 3618.00, 162.00, 65129.00),
(2012, 2007, 2, 22, 9.50, 3822.35, 209.00, 84091.70),
(3001, 3001, 1, 25, 4.80, 1931.18, 120.00, 48279.60),
(3002, 3002, 1, 35, 5.00, 2011.65, 175.00, 70408.75),
(3003, 3003, 1, 30, 5.20, 2092.00, 156.00, 62760.00),
(3004, 3007, 1, 45, 5.60, 2253.00, 252.00, 101385.00),
(3005, 3002, 19, 40, 6.20, 2494.00, 248.00, 99760.00),
(3006, 3004, 19, 50, 6.50, 2615.15, 325.00, 130757.50),
(3007, 3005, 19, 35, 6.80, 2735.94, 238.00, 95757.90),
(3008, 3008, 19, 60, 7.10, 2856.00, 426.00, 171360.00),
(3009, 3003, 2, 18, 8.00, 3218.64, 144.00, 57935.52),
(3010, 3004, 2, 22, 8.50, 3419.81, 187.00, 75236.20),
(3011, 3006, 2, 20, 9.00, 3618.00, 180.00, 72360.00),
(3012, 3009, 2, 26, 9.60, 3860.00, 249.60, 100360.00),
(3013, 3005, 15, 60, 2.00, 804.66, 120.00, 48279.60),
(3014, 3008, 15, 80, 2.20, 885.00, 176.00, 70800.00),
(3015, 3006, 3, 15, 7.00, 2815.00, 105.00, 42225.00),
(3016, 3009, 3, 20, 7.80, 3136.00, 156.00, 62720.00),
(3017, 3007, 8, 10, 45.00, 18100.00, 450.00, 181000.00),
(3018, 3009, 8, 12, 48.00, 19300.00, 576.00, 231600.00),
(3019, 3001, 13, 35, 2.80, 1126.00, 98.00, 39410.00),
(3020, 3009, 13, 40, 3.10, 1246.00, 124.00, 49840.00);

--
-- Disparadores `detalle_ingreso`
--
DELIMITER $$
CREATE TRIGGER `tr_ActualizarStock` AFTER INSERT ON `detalle_ingreso` FOR EACH ROW BEGIN
  UPDATE productos SET stock = stock + NEW.cantidad WHERE id = NEW.id_producto;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_venta`
--

CREATE TABLE `detalle_venta` (
  `id` int(11) NOT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `id_venta` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `precio_unitario_bs` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `subtotal_bs` decimal(10,2) NOT NULL,
  `precio_al_mayor` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `detalle_venta`
--

INSERT INTO `detalle_venta` (`id`, `id_producto`, `id_venta`, `cantidad`, `precio_unitario`, `precio_unitario_bs`, `subtotal`, `subtotal_bs`, `precio_al_mayor`) VALUES
(1, 1, 1, 30, 300.00, 120699.00, 9000.00, 3620970.00, 0),
(2, 19, 2, 6, 5.00, 2011.65, 30.00, 12069.90, 1),
(2001, 1, 2001, 12, 15.00, 6034.95, 180.00, 72419.40, 0),
(2002, 19, 2002, 10, 9.00, 3618.00, 90.00, 36209.70, 1),
(2003, 2, 2003, 6, 20.00, 8046.60, 120.00, 48279.60, 0),
(2004, 1, 2004, 15, 14.00, 5632.62, 210.00, 84488.00, 0),
(2005, 19, 2005, 20, 12.00, 4827.96, 240.00, 96559.20, 1),
(2006, 2, 2006, 9, 15.00, 6034.95, 135.00, 54314.55, 0),
(2007, 1, 2007, 18, 11.00, 4425.63, 198.00, 79661.34, 0),
(2008, 19, 2008, 15, 12.00, 4827.96, 180.00, 72419.40, 1),
(2009, 2, 2008, 5, 15.00, 6034.95, 75.00, 30212.25, 0),
(2010, 1, 2008, 10, 12.00, 4827.96, 120.00, 48279.60, 0),
(3001, 1, 3001, 10, 12.00, 4827.96, 120.00, 48279.60, 0),
(3002, 19, 3001, 10, 10.00, 4023.30, 100.00, 40233.00, 1),
(3003, 2, 3002, 6, 18.00, 7241.94, 108.00, 43451.64, 0),
(3004, 15, 3002, 20, 3.50, 1408.16, 70.00, 28163.20, 0),
(3005, 3, 3003, 8, 20.00, 8046.60, 160.00, 64372.80, 0),
(3006, 19, 3003, 5, 10.00, 4023.30, 50.00, 20116.50, 1),
(3007, 1, 3004, 12, 12.50, 5031.00, 150.00, 60349.50, 0),
(3008, 13, 3004, 18, 5.00, 2011.65, 90.00, 36209.70, 0),
(3009, 2, 3005, 8, 19.00, 7644.00, 152.00, 61152.00, 0),
(3010, 15, 3005, 25, 4.00, 1609.32, 100.00, 40233.00, 0),
(3011, 19, 3005, 12, 11.00, 4425.63, 132.00, 53119.00, 1),
(3012, 1, 3006, 18, 13.00, 5230.29, 234.00, 94145.22, 0),
(3013, 8, 3006, 2, 43.00, 17284.00, 86.00, 34568.00, 0),
(3014, 19, 3007, 25, 12.00, 4827.96, 300.00, 120698.90, 1),
(3015, 2, 3007, 10, 20.00, 8046.60, 200.00, 80466.00, 0),
(3016, 3, 3007, 12, 22.00, 8851.26, 264.00, 106215.12, 0),
(3017, 1, 3008, 20, 14.00, 5632.62, 280.00, 112646.40, 0),
(3018, 19, 3008, 15, 12.00, 4827.96, 180.00, 72419.40, 1),
(3019, 15, 3008, 30, 4.00, 1609.32, 120.00, 48279.60, 0),
(3020, 1, 3009, 22, 15.00, 6034.95, 330.00, 132748.90, 0),
(3021, 19, 3009, 18, 13.00, 5230.29, 234.00, 94145.22, 1),
(3022, 2, 3009, 11, 22.00, 8851.26, 242.00, 97379.86, 0),
(3023, 15, 3009, 35, 5.00, 2011.65, 175.00, 70408.75, 0);

--
-- Disparadores `detalle_venta`
--
DELIMITER $$
CREATE TRIGGER `tr_ActualizarStock2` AFTER INSERT ON `detalle_venta` FOR EACH ROW BEGIN
  UPDATE productos SET stock = stock - NEW.cantidad WHERE id = NEW.id_producto;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingresos`
--

CREATE TABLE `ingresos` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_proveedor` int(11) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL,
  `total_bs` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `ingresos`
--

INSERT INTO `ingresos` (`id`, `id_usuario`, `id_proveedor`, `fecha`, `total`, `total_bs`) VALUES
(1, 1, NULL, '2026-10-08 20:52:12', 19.88, 8000.00),
(2, 1, 1, NULL, 1.00, 402.33),
(2001, 1, 1, '2026-01-12 09:30:00', 120.00, 48279.60),
(2002, 1, 1, '2026-02-15 10:10:00', 170.00, 68493.00),
(2003, 1, 1, '2026-03-18 11:20:00', 210.00, 84500.00),
(2004, 1, 1, '2026-05-20 14:45:00', 210.00, 84500.00),
(2005, 1, 1, '2026-06-28 13:10:00', 140.00, 56300.00),
(2006, 1, 1, '2026-08-03 16:00:00', 195.00, 78453.35),
(2007, 1, 1, '2026-09-18 12:40:00', 260.00, 104605.80),
(3001, 1, 1, '2026-01-08 09:00:00', 300.00, 120699.00),
(3002, 1, 1, '2026-02-15 11:15:00', 470.00, 189060.00),
(3003, 1, 1, '2026-03-18 12:30:00', 520.00, 209000.00),
(3004, 1, 1, '2026-04-21 14:00:00', 640.00, 257000.00),
(3005, 1, 1, '2026-06-03 08:45:00', 420.00, 168000.00),
(3006, 1, 1, '2026-07-20 10:00:00', 600.00, 241000.00),
(3007, 1, 1, '2026-08-29 15:40:00', 740.00, 297000.00),
(3008, 1, 1, '2026-09-25 16:10:00', 820.00, 329000.00),
(3009, 1, 1, '2026-10-03 12:00:00', 930.00, 374000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `codigo_producto` varchar(10) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `precio_al_mayor` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `stock_minimo` int(11) NOT NULL DEFAULT 0,
  `stock_maximo` int(11) DEFAULT NULL,
  `cantidad_al_mayor` int(11) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `disponibilidad` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `id_categoria`, `codigo_producto`, `nombre`, `descripcion`, `marca`, `precio`, `precio_al_mayor`, `stock`, `stock_minimo`, `stock_maximo`, `cantidad_al_mayor`, `imagen`, `estado`, `disponibilidad`) VALUES
(1, 1, '', 'Filtro de aceite Toyota', 'Filtro de aceite para motores Toyota 1.6/1.8', 'Toyota', 10.00, 0.00, 103, 3, 20, NULL, '', 1, 1),
(2, 2, '', 'Filtro de aire Chevrolet Aveo', 'Filtro de aire para Aveo y Optra', 'ACDelco', 15.00, 0.00, 106, 2, 15, NULL, NULL, 1, 1),
(3, 3, '', 'Filtro de combustible Ford Fiesta', 'Filtro de gasolina de alta eficiencia', 'Motorcraft', 18.00, 0.00, 15, 2, 10, NULL, NULL, 1, 1),
(4, 4, '', 'Bujías Iridium NGK', 'Juego de 4 bujías Iridium', 'NGK', 28.00, 0.00, 0, 4, 25, NULL, NULL, 1, 1),
(5, 5, '', 'Pastillas de freno Corolla', 'Pastillas delanteras para Toyota Corolla', 'TRW', 35.00, 0.00, 0, 2, 10, NULL, NULL, 1, 1),
(6, 6, '', 'Discos de freno Hyundai Elantra', 'Par de discos ventilados', 'Hyundai', 75.00, 0.00, 0, 1, 6, NULL, NULL, 1, 1),
(7, 7, '', 'Amortiguador Delantero Hilux', 'Amortiguador reforzado', 'KYB', 95.00, 0.00, 0, 1, 5, NULL, NULL, 1, 1),
(8, 8, '', 'Batería 600CCA', 'Batería automotriz libre de mantenimiento', 'Titanium', 85.00, 0.00, 20, 2, 10, NULL, NULL, 1, 1),
(9, 9, '', 'Correa única Chevrolet', 'Correa de accesorios para Chevrolet', 'Gates', 22.00, 0.00, 0, 3, 15, NULL, NULL, 1, 1),
(10, 10, '', 'Bomba de agua Mitsubishi', 'Bomba de agua para motores 2.0', 'Mitsubishi', 60.00, 0.00, 0, 1, 5, NULL, NULL, 1, 1),
(11, 16, '', 'Cadena reforzada 428H', 'Cadena para motos 150cc', 'DID', 20.00, 0.00, 0, 5, 30, NULL, NULL, 1, 1),
(12, 17, '', 'Kit de arrastre Empire 150', 'Kit completo: piñón, corona y cadena', 'Empire', 35.00, 0.00, 0, 3, 20, NULL, NULL, 1, 1),
(13, 18, '', 'Pastillas de freno para moto', 'Pastillas delanteras universales', 'Venezolana', 8.00, 0.00, 57, 5, 40, NULL, NULL, 1, 1),
(14, 19, '', 'Neumático 90/90-17', 'Caucho delantero para motos 150cc', 'Pirelli', 45.00, 0.00, 0, 2, 10, NULL, NULL, 1, 1),
(15, 20, '', 'Bujía NGK CR7HSA', 'Bujía estándar para motos 125-150cc', 'NGK', 6.00, 0.00, 30, 10, 50, NULL, NULL, 1, 1),
(16, 21, '', 'Amortiguador trasero universal', 'Amortiguador para motos tipo paseo', 'RacingBoy', 30.00, 0.00, 0, 2, 15, NULL, NULL, 1, 1),
(17, 22, '', 'Carburador para moto 150cc', 'Carburador tipo PZ27', 'Keihin', 38.00, 0.00, 0, 1, 8, NULL, NULL, 1, 1),
(18, 23, '', 'Filtro de aire deportivo', 'Filtro cónico para motos', 'ProFilter', 12.00, 0.00, 0, 5, 30, NULL, NULL, 1, 1),
(19, 24, '2063', 'Aceite 20W50 para moto', 'Aceite mineral 20W50', 'Venoco', 7.00, 5.00, 173, 10, 60, 6, '', 1, 1),
(20, 25, '', 'Espejos retrovisores universales', 'Par de espejos cromados', 'Universal', 10.00, 0.00, 0, 4, 25, NULL, NULL, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `cedula_rif` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `direccion` text DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id`, `nombre`, `cedula_rif`, `telefono`, `direccion`, `estado`) VALUES
(1, 'Prueba', '30218990', '04125272511', 'pass', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`) VALUES
(1, 'admin'),
(2, 'ventas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tasa`
--

CREATE TABLE `tasa` (
  `id` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `tasa`
--

INSERT INTO `tasa` (`id`, `valor`, `fecha_actualizacion`) VALUES
(1, 402.33, '2026-02-20 14:31:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `id_rol` int(11) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `id_rol`, `nombre`, `usuario`, `clave`, `estado`) VALUES
(1, 1, 'Administrador', 'admin', '1234567890', 1),
(2, 2, 'Vendedor', 'ventas1', '30218990', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL,
  `total_bs` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `id_cliente`, `id_usuario`, `fecha`, `total`, `total_bs`) VALUES
(1, NULL, 1, '2026-10-08 20:52:26', 9000.00, 3620970.00),
(2, NULL, 1, '2026-10-09 19:13:32', 30.00, 12069.90),
(2001, 1, 1, '2026-01-20 10:00:00', 180.00, 72419.40),
(2002, 1, 1, '2026-02-10 11:00:00', 90.00, 36209.70),
(2003, 1, 1, '2026-03-25 16:00:00', 120.00, 48279.60),
(2004, 1, 1, '2026-05-02 12:00:00', 210.00, 84488.00),
(2005, 1, 1, '2026-06-18 15:00:00', 240.00, 96559.20),
(2006, 1, 1, '2026-07-22 09:30:00', 135.00, 54314.55),
(2007, 1, 1, '2026-09-14 18:45:00', 198.00, 79661.34),
(2008, 1, 1, '2026-10-08 19:30:00', 375.00, 150873.75),
(3001, 1, 1, '2026-01-12 10:30:00', 220.00, 88500.00),
(3002, 1, 1, '2026-02-16 11:40:00', 185.00, 74400.00),
(3003, 1, 1, '2026-03-22 08:15:00', 210.00, 84500.00),
(3004, 1, 1, '2026-04-25 18:20:00', 240.00, 96400.00),
(3005, 1, 1, '2026-06-06 13:45:00', 280.00, 112500.00),
(3006, 1, 1, '2026-07-14 16:00:00', 320.00, 128000.00),
(3007, 1, 1, '2026-08-20 17:10:00', 410.00, 164500.00),
(3008, 1, 1, '2026-09-27 19:35:00', 480.00, 192800.00),
(3009, 1, 1, '2026-10-05 20:15:00', 540.00, 217000.00);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_detalle_venta_costo_promedio`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_detalle_venta_costo_promedio` (
`id_detalle_venta` int(11)
,`id_venta` int(11)
,`fecha_venta` datetime
,`id_producto` int(11)
,`codigo_producto` varchar(10)
,`producto` varchar(100)
,`id_categoria` int(11)
,`categoria` varchar(100)
,`cantidad` int(11)
,`precio_unitario` decimal(10,2)
,`precio_unitario_bs` decimal(10,2)
,`venta_usd` decimal(10,2)
,`venta_bs` decimal(10,2)
,`uso_precio_al_mayor` tinyint(1)
,`costo_promedio_unitario_usd` decimal(46,6)
,`costo_promedio_unitario_bs` decimal(46,6)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_productos_stock_bajo`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_productos_stock_bajo` (
`id` int(11)
,`nombre` varchar(100)
,`marca` varchar(50)
,`precio` decimal(10,2)
,`stock_actual` int(11)
,`stock_minimo` int(11)
,`stock_maximo` int(11)
,`categoria` varchar(100)
,`nivel_alerta` varchar(9)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_ventas_producto_ganancia`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_ventas_producto_ganancia` (
`id_detalle_venta` int(11)
,`id_venta` int(11)
,`fecha_venta` datetime
,`id_producto` int(11)
,`codigo_producto` varchar(10)
,`producto` varchar(100)
,`id_categoria` int(11)
,`categoria` varchar(100)
,`cantidad` int(11)
,`precio_unitario` decimal(10,2)
,`precio_unitario_bs` decimal(10,2)
,`venta_usd` decimal(10,2)
,`venta_bs` decimal(10,2)
,`uso_precio_al_mayor` tinyint(1)
,`costo_promedio_unitario_usd` decimal(46,6)
,`costo_promedio_unitario_bs` decimal(46,6)
,`costo_mercancia_usd` decimal(56,6)
,`costo_mercancia_bs` decimal(56,6)
,`ganancia_bruta_usd` decimal(57,6)
,`ganancia_bruta_bs` decimal(57,6)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `v_detalle_venta_costo_promedio`
--
DROP TABLE IF EXISTS `v_detalle_venta_costo_promedio`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_detalle_venta_costo_promedio`  AS SELECT `dv`.`id` AS `id_detalle_venta`, `dv`.`id_venta` AS `id_venta`, `v`.`fecha` AS `fecha_venta`, `dv`.`id_producto` AS `id_producto`, `p`.`codigo_producto` AS `codigo_producto`, `p`.`nombre` AS `producto`, `p`.`id_categoria` AS `id_categoria`, `c`.`nombre` AS `categoria`, `dv`.`cantidad` AS `cantidad`, `dv`.`precio_unitario` AS `precio_unitario`, `dv`.`precio_unitario_bs` AS `precio_unitario_bs`, `dv`.`subtotal` AS `venta_usd`, `dv`.`subtotal_bs` AS `venta_bs`, `dv`.`precio_al_mayor` AS `uso_precio_al_mayor`, (select sum(`di`.`cantidad` * `di`.`precio_compra`) / nullif(sum(`di`.`cantidad`),0) from (`detalle_ingreso` `di` join `ingresos` `i` on(`i`.`id` = `di`.`id_ingreso`)) where `di`.`id_producto` = `dv`.`id_producto` and `i`.`fecha` <= `v`.`fecha`) AS `costo_promedio_unitario_usd`, (select sum(`di`.`cantidad` * `di`.`precio_compra_bs`) / nullif(sum(`di`.`cantidad`),0) from (`detalle_ingreso` `di` join `ingresos` `i` on(`i`.`id` = `di`.`id_ingreso`)) where `di`.`id_producto` = `dv`.`id_producto` and `i`.`fecha` <= `v`.`fecha`) AS `costo_promedio_unitario_bs` FROM (((`detalle_venta` `dv` join `ventas` `v` on(`v`.`id` = `dv`.`id_venta`)) left join `productos` `p` on(`p`.`id` = `dv`.`id_producto`)) left join `categorias` `c` on(`c`.`id` = `p`.`id_categoria`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_productos_stock_bajo`
--
DROP TABLE IF EXISTS `v_productos_stock_bajo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_productos_stock_bajo`  AS SELECT `p`.`id` AS `id`, `p`.`nombre` AS `nombre`, `p`.`marca` AS `marca`, `p`.`precio` AS `precio`, `p`.`stock` AS `stock_actual`, `p`.`stock_minimo` AS `stock_minimo`, `p`.`stock_maximo` AS `stock_maximo`, `c`.`nombre` AS `categoria`, CASE WHEN `p`.`stock` = 0 THEN 'SIN_STOCK' WHEN `p`.`stock` <= `p`.`stock_minimo` THEN 'CRITICO' ELSE 'BAJO' END AS `nivel_alerta` FROM (`productos` `p` left join `categorias` `c` on(`c`.`id` = `p`.`id_categoria`)) WHERE `p`.`stock` <= `p`.`stock_minimo` ORDER BY `p`.`stock` ASC, `p`.`nombre` ASC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_ventas_producto_ganancia`
--
DROP TABLE IF EXISTS `v_ventas_producto_ganancia`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_ventas_producto_ganancia`  AS SELECT `dvc`.`id_detalle_venta` AS `id_detalle_venta`, `dvc`.`id_venta` AS `id_venta`, `dvc`.`fecha_venta` AS `fecha_venta`, `dvc`.`id_producto` AS `id_producto`, `dvc`.`codigo_producto` AS `codigo_producto`, `dvc`.`producto` AS `producto`, `dvc`.`id_categoria` AS `id_categoria`, `dvc`.`categoria` AS `categoria`, `dvc`.`cantidad` AS `cantidad`, `dvc`.`precio_unitario` AS `precio_unitario`, `dvc`.`precio_unitario_bs` AS `precio_unitario_bs`, `dvc`.`venta_usd` AS `venta_usd`, `dvc`.`venta_bs` AS `venta_bs`, `dvc`.`uso_precio_al_mayor` AS `uso_precio_al_mayor`, `dvc`.`costo_promedio_unitario_usd` AS `costo_promedio_unitario_usd`, `dvc`.`costo_promedio_unitario_bs` AS `costo_promedio_unitario_bs`, CASE WHEN `dvc`.`costo_promedio_unitario_usd` is null THEN NULL ELSE `dvc`.`costo_promedio_unitario_usd`* `dvc`.`cantidad` END AS `costo_mercancia_usd`, CASE WHEN `dvc`.`costo_promedio_unitario_bs` is null THEN NULL ELSE `dvc`.`costo_promedio_unitario_bs`* `dvc`.`cantidad` END AS `costo_mercancia_bs`, CASE WHEN `dvc`.`costo_promedio_unitario_usd` is null THEN NULL ELSE `dvc`.`venta_usd`- `dvc`.`costo_promedio_unitario_usd` * `dvc`.`cantidad` END AS `ganancia_bruta_usd`, CASE WHEN `dvc`.`costo_promedio_unitario_bs` is null THEN NULL ELSE `dvc`.`venta_bs`- `dvc`.`costo_promedio_unitario_bs` * `dvc`.`cantidad` END AS `ganancia_bruta_bs` FROM `v_detalle_venta_costo_promedio` AS `dvc` ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cedula_rif` (`cedula_rif`,`telefono`);

--
-- Indices de la tabla `detalle_ingreso`
--
ALTER TABLE `detalle_ingreso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detalle_ingreso_ibfk_1` (`id_ingreso`),
  ADD KEY `detalle_ingreso_ibfk_2` (`id_producto`);

--
-- Indices de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detalle_venta_ibfk_1` (`id_venta`),
  ADD KEY `detalle_venta_ibfk_2` (`id_producto`);

--
-- Indices de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ingresos_ibfk_2` (`id_usuario`),
  ADD KEY `id_proveedor` (`id_proveedor`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `productos_ibfk_1` (`id_categoria`),
  ADD KEY `codigo_producto` (`codigo_producto`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cedula_rif` (`cedula_rif`,`telefono`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tasa`
--
ALTER TABLE `tasa`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD KEY `usuarios_ibfk_1` (`id_rol`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ventas_ibfk_1` (`id_cliente`),
  ADD KEY `ventas_ibfk_2` (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `detalle_ingreso`
--
ALTER TABLE `detalle_ingreso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3021;

--
-- AUTO_INCREMENT de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3024;

--
-- AUTO_INCREMENT de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3010;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tasa`
--
ALTER TABLE `tasa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3010;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_ingreso`
--
ALTER TABLE `detalle_ingreso`
  ADD CONSTRAINT `detalle_ingreso_ibfk_1` FOREIGN KEY (`id_ingreso`) REFERENCES `ingresos` (`id`),
  ADD CONSTRAINT `detalle_ingreso_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD CONSTRAINT `detalle_venta_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id`),
  ADD CONSTRAINT `detalle_venta_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `ingresos`
--
ALTER TABLE `ingresos`
  ADD CONSTRAINT `ingresos_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `ingresos_ibfk_3` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;