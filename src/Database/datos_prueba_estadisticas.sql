-- Script de prueba para comparar estadísticas por período.
-- Inserta ingresos y ventas distribuidos en distintos meses para validar:
-- MES, ULTIMOS_6_MESES, ANIO, INTERVALO y TODO.
-- Ejecutar sobre la base `repuestos`.

SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;

-- Limpieza opcional para correr el script varias veces en una base de prueba.
DELETE FROM detalle_venta WHERE id BETWEEN 2001 AND 2999;
DELETE FROM ventas WHERE id BETWEEN 2001 AND 2999;
DELETE FROM detalle_ingreso WHERE id BETWEEN 2001 AND 2999;
DELETE FROM ingresos WHERE id BETWEEN 2001 AND 2999;

-- -----------------------------------------------------------------------------
-- 1) Ingresos en varios meses
-- -----------------------------------------------------------------------------
INSERT INTO ingresos (id, id_usuario, id_proveedor, fecha, total, total_bs) VALUES
(2001, 1, 1, '2026-01-12 09:30:00', 120.00, 48279.60),
(2002, 1, 1, '2026-02-15 10:10:00', 170.00, 68493.00),
(2003, 1, 1, '2026-03-18 11:20:00', 210.00, 84500.00),
(2004, 1, 1, '2026-05-20 14:45:00', 210.00, 84500.00),
(2005, 1, 1, '2026-06-28 13:10:00', 140.00, 56300.00),
(2006, 1, 1, '2026-08-03 16:00:00', 195.00, 78453.35),
(2007, 1, 1, '2026-09-18 12:40:00', 260.00, 104605.80);

INSERT INTO detalle_ingreso (id, id_ingreso, id_producto, cantidad, precio_compra, precio_compra_bs, subtotal, subtotal_bs) VALUES
-- Producto 1: Filtro de aceite Toyota
(2001, 2001, 1, 20, 4.80, 1931.18, 96.00, 38623.60),
(2002, 2002, 1, 30, 5.00, 2011.65, 150.00, 60349.50),
(2003, 2003, 1, 20, 5.20, 2092.00, 104.00, 41840.00),
(2004, 2006, 1, 35, 5.50, 2212.82, 192.50, 77498.70),

-- Producto 19: Aceite 20W50 para moto
(2005, 2003, 19, 25, 6.00, 2413.98, 150.00, 60349.50),
(2006, 2004, 19, 30, 6.50, 2615.15, 195.00, 78454.50),
(2007, 2005, 19, 20, 6.80, 2735.94, 136.00, 54718.80),
(2008, 2007, 19, 35, 7.20, 2896.78, 252.00, 101387.30),

-- Producto 2: Filtro de aire Chevrolet Aveo
(2009, 2004, 2, 20, 8.00, 3218.64, 160.00, 64372.80),
(2010, 2005, 2, 15, 8.50, 3419.81, 127.50, 51297.15),
(2011, 2006, 2, 18, 9.00, 3618.00, 162.00, 65129.00),
(2012, 2007, 2, 22, 9.50, 3822.35, 209.00, 84091.70);

-- -----------------------------------------------------------------------------
-- 2) Ventas en distintos periodos para comparar rankings
-- -----------------------------------------------------------------------------
INSERT INTO ventas (id, id_cliente, id_usuario, fecha, total, total_bs) VALUES
(2001, 1, 1, '2026-01-20 10:00:00', 180.00, 72419.40),
(2002, 1, 1, '2026-02-10 11:00:00', 90.00, 36209.70),
(2003, 1, 1, '2026-03-25 16:00:00', 120.00, 48279.60),
(2004, 1, 1, '2026-05-02 12:00:00', 210.00, 84488.00),
(2005, 1, 1, '2026-06-18 15:00:00', 240.00, 96559.20),
(2006, 1, 1, '2026-07-22 09:30:00', 135.00, 54314.55),
(2007, 1, 1, '2026-09-14 18:45:00', 198.00, 79661.34),
(2008, 1, 1, '2026-10-08 19:30:00', 375.00, 150873.75);

-- -----------------------------------------------------------------------------
-- 3) Detalles de venta
-- -----------------------------------------------------------------------------
INSERT INTO detalle_venta (id, id_producto, id_venta, cantidad, precio_unitario, precio_unitario_bs, subtotal, subtotal_bs, precio_al_mayor) VALUES
-- 2026-01-20: Producto 1
(2001, 1, 2001, 12, 15.00, 6034.95, 180.00, 72419.40, 0),

-- 2026-02-10: Producto 19
(2002, 19, 2002, 10, 9.00, 3618.00, 90.00, 36209.70, 1),

-- 2026-03-25: Producto 2
(2003, 2, 2003, 6, 20.00, 8046.60, 120.00, 48279.60, 0),

-- 2026-05-02: Producto 1
(2004, 1, 2004, 15, 14.00, 5632.62, 210.00, 84488.00, 0),

-- 2026-06-18: Producto 19
(2005, 19, 2005, 20, 12.00, 4827.96, 240.00, 96559.20, 1),

-- 2026-07-22: Producto 2
(2006, 2, 2006, 9, 15.00, 6034.95, 135.00, 54314.55, 0),

-- 2026-09-14: Producto 1
(2007, 1, 2007, 18, 11.00, 4425.63, 198.00, 79661.34, 0),

-- 2026-10-08: mezcla de productos
(2008, 19, 2008, 15, 12.00, 4827.96, 180.00, 72419.40, 1),
(2009, 2, 2008, 5, 15.00, 6034.95, 75.00, 30212.25, 0),
(2010, 1, 2008, 10, 12.00, 4827.96, 120.00, 48279.60, 0);

-- -----------------------------------------------------------------------------
-- 4) Cierre
-- -----------------------------------------------------------------------------
COMMIT;
SET FOREIGN_KEY_CHECKS = 1;

-- Nota:
-- Este lote cubre varios meses y permite comparar resultados para:
--   - MES
--   - ULTIMOS_6_MESES
--   - ANIO
--   - INTERVALO
--   - TODO
-- En la pantalla de estadísticas puedes filtrar por fechas y categoría para validar
-- la evolución del rendimiento y la rentabilidad por producto.
