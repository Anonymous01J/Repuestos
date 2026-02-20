<?php
    namespace Anonymous01j\Repuestos\Models;
    use Anonymous01j\Repuestos\Models\Conexion;

    class StockBajo extends Conexion {

        public function __construct() {
            parent::__construct();
        }

        public function search(): array {
            $sql = "SELECT * FROM v_productos_stock_bajo";
            $stmt = $this->prepare($sql);
            if ($stmt->execute()) {
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
            return [];
        }

        public function count(): int {
            $sql = "SELECT COUNT(*) FROM v_productos_stock_bajo";
            $stmt = $this->prepare($sql);
            if ($stmt->execute()) {
                return (int) $stmt->fetchColumn();
            }
            return 0;
        }
    }