-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-10-2026 a las 00:18:30
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
CREATE DATABASE IF NOT EXISTS `repuestos` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci;
USE `repuestos`;

DELIMITER $$
--
-- Procedimientos
--
DROP PROCEDURE IF EXISTS `sp_estadisticas_productos`$$
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

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `cedula_rif` varchar(20) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ingreso`
--

DROP TABLE IF EXISTS `detalle_ingreso`;
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
-- Disparadores `detalle_ingreso`
--
DROP TRIGGER IF EXISTS `tr_ActualizarStock`;
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

DROP TABLE IF EXISTS `detalle_venta`;
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


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingresos`
--

DROP TABLE IF EXISTS `ingresos`;
CREATE TABLE `ingresos` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_proveedor` int(11) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL,
  `total_bs` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `licencia_sistema`
--

DROP TABLE IF EXISTS `licencia_sistema`;
CREATE TABLE `licencia_sistema` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `token` mediumtext DEFAULT NULL,
  `ultima_fecha_vista_utc` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

DROP TABLE IF EXISTS `productos`;
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `cedula_rif` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `direccion` text DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

DROP TABLE IF EXISTS `roles`;
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

DROP TABLE IF EXISTS `tasa`;
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

DROP TABLE IF EXISTS `usuarios`;
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
(1, 1, 'Administrador', 'admin', 'c775e7b757ede630cd0aa1113bd102661ab38829ca52a6422ab782862f268646', 1),
(2, 2, 'Vendedor', 'ventas1', 'c775e7b757ede630cd0aa1113bd102661ab38829ca52a6422ab782862f268646', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

DROP TABLE IF EXISTS `ventas`;
CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL,
  `total_bs` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_detalle_venta_costo_promedio`
-- (Véase abajo para la vista actual)
--
DROP VIEW IF EXISTS `v_detalle_venta_costo_promedio`;
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
DROP VIEW IF EXISTS `v_productos_stock_bajo`;
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
DROP VIEW IF EXISTS `v_ventas_producto_ganancia`;
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

DROP VIEW IF EXISTS `v_detalle_venta_costo_promedio`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_detalle_venta_costo_promedio`  AS SELECT `dv`.`id` AS `id_detalle_venta`, `dv`.`id_venta` AS `id_venta`, `v`.`fecha` AS `fecha_venta`, `dv`.`id_producto` AS `id_producto`, `p`.`codigo_producto` AS `codigo_producto`, `p`.`nombre` AS `producto`, `p`.`id_categoria` AS `id_categoria`, `c`.`nombre` AS `categoria`, `dv`.`cantidad` AS `cantidad`, `dv`.`precio_unitario` AS `precio_unitario`, `dv`.`precio_unitario_bs` AS `precio_unitario_bs`, `dv`.`subtotal` AS `venta_usd`, `dv`.`subtotal_bs` AS `venta_bs`, `dv`.`precio_al_mayor` AS `uso_precio_al_mayor`, (select sum(`di`.`cantidad` * `di`.`precio_compra`) / nullif(sum(`di`.`cantidad`),0) from (`detalle_ingreso` `di` join `ingresos` `i` on(`i`.`id` = `di`.`id_ingreso`)) where `di`.`id_producto` = `dv`.`id_producto` and `i`.`fecha` <= `v`.`fecha`) AS `costo_promedio_unitario_usd`, (select sum(`di`.`cantidad` * `di`.`precio_compra_bs`) / nullif(sum(`di`.`cantidad`),0) from (`detalle_ingreso` `di` join `ingresos` `i` on(`i`.`id` = `di`.`id_ingreso`)) where `di`.`id_producto` = `dv`.`id_producto` and `i`.`fecha` <= `v`.`fecha`) AS `costo_promedio_unitario_bs` FROM (((`detalle_venta` `dv` join `ventas` `v` on(`v`.`id` = `dv`.`id_venta`)) left join `productos` `p` on(`p`.`id` = `dv`.`id_producto`)) left join `categorias` `c` on(`c`.`id` = `p`.`id_categoria`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_productos_stock_bajo`
--
DROP TABLE IF EXISTS `v_productos_stock_bajo`;

DROP VIEW IF EXISTS `v_productos_stock_bajo`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_productos_stock_bajo`  AS SELECT `p`.`id` AS `id`, `p`.`nombre` AS `nombre`, `p`.`marca` AS `marca`, `p`.`precio` AS `precio`, `p`.`stock` AS `stock_actual`, `p`.`stock_minimo` AS `stock_minimo`, `p`.`stock_maximo` AS `stock_maximo`, `c`.`nombre` AS `categoria`, CASE WHEN `p`.`stock` = 0 THEN 'SIN_STOCK' WHEN `p`.`stock` <= `p`.`stock_minimo` THEN 'CRITICO' ELSE 'BAJO' END AS `nivel_alerta` FROM (`productos` `p` left join `categorias` `c` on(`c`.`id` = `p`.`id_categoria`)) WHERE `p`.`stock` <= `p`.`stock_minimo` ORDER BY `p`.`stock` ASC, `p`.`nombre` ASC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_ventas_producto_ganancia`
--
DROP TABLE IF EXISTS `v_ventas_producto_ganancia`;

DROP VIEW IF EXISTS `v_ventas_producto_ganancia`;
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
-- Indices de la tabla `licencia_sistema`
--
ALTER TABLE `licencia_sistema`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_ingreso`
--
ALTER TABLE `detalle_ingreso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
