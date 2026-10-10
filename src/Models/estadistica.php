<?php
namespace Anonymous01j\Repuestos\Models;

use PDO;

class Estadistica extends Conexion {
    private const RANKING_KEYS = [
        'mas_vendidos',
        'menos_vendidos',
        'mas_rentables',
        'menos_rentables',
        'mayor_ganancia',
        'menor_ganancia',
    ];

    /**
     * Ejecuta el procedimiento de rankings y devuelve cada conjunto con una clave descriptiva.
     * Los parámetros se alinean con sp_estadisticas_productos.
     */
    public function obtenerRankings(
        string $periodo = 'MES',
        ?string $fechaDesde = null,
        ?string $fechaHasta = null,
        ?int $categoriaId = null
    ): array {
        $sql = 'CALL sp_estadisticas_productos(:periodo, :fecha_desde, :fecha_hasta, :categoria_id)';
        $stmt = $this->prepare($sql);
        $stmt->bindValue(':periodo', strtoupper(trim($periodo)), PDO::PARAM_STR);
        $stmt->bindValue(':fecha_desde', $fechaDesde, $fechaDesde === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':fecha_hasta', $fechaHasta, $fechaHasta === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':categoria_id', $categoriaId, $categoriaId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->execute();

        $rankings = [];
        foreach (self::RANKING_KEYS as $index => $key) {
            $rankings[$key] = $stmt->columnCount() > 0
                ? $stmt->fetchAll(PDO::FETCH_ASSOC)
                : [];

            if ($index < count(self::RANKING_KEYS) - 1) {
                $stmt->nextRowset();
            }
        }

        $stmt->closeCursor();
        return $rankings;
    }

    /**
     * Resumen de ventas del mes actual para las tarjetas del dashboard.
     */
    public function obtenerResumenMensual(): array {
                $sql = "SELECT
                                        (SELECT COALESCE(SUM(v.total), 0)
                                         FROM ventas AS v
                                         WHERE v.fecha >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                                             AND v.fecha < DATE_ADD(CURDATE(), INTERVAL 1 DAY)) AS ventas_usd,
                                        (SELECT COALESCE(SUM(v.total_bs), 0)
                                         FROM ventas AS v
                                         WHERE v.fecha >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                                             AND v.fecha < DATE_ADD(CURDATE(), INTERVAL 1 DAY)) AS ventas_bs,
                                        (SELECT COALESCE(SUM(dv.cantidad), 0)
                                         FROM detalle_venta AS dv
                                         INNER JOIN ventas AS v ON v.id = dv.id_venta
                                         WHERE v.fecha >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                                             AND v.fecha < DATE_ADD(CURDATE(), INTERVAL 1 DAY)) AS unidades_vendidas,
                                        (SELECT CASE
                                                                WHEN SUM(vg.costo_mercancia_usd IS NULL) > 0 THEN NULL
                                                                ELSE SUM(vg.ganancia_bruta_usd)
                                                        END
                                         FROM v_ventas_producto_ganancia AS vg
                                         WHERE vg.fecha_venta >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                                             AND vg.fecha_venta < DATE_ADD(CURDATE(), INTERVAL 1 DAY)) AS ganancia_bruta_usd,
                                        (SELECT COALESCE(SUM(vg.costo_mercancia_usd IS NULL), 0)
                                         FROM v_ventas_producto_ganancia AS vg
                                         WHERE vg.fecha_venta >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                                             AND vg.fecha_venta < DATE_ADD(CURDATE(), INTERVAL 1 DAY)) AS lineas_sin_costo";
        $stmt = $this->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'ventas_usd' => 0,
            'ventas_bs' => 0,
            'unidades_vendidas' => 0,
            'ganancia_bruta_usd' => null,
            'lineas_sin_costo' => 0,
        ];
    }
}
