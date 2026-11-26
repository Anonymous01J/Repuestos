<?php
    namespace Anonymous01j\Repuestos\Models;
    use Anonymous01j\Repuestos\Models\Conexion;

    class Detalles_Ingreso extends Conexion {
        private $id;
        private $id_ingreso;
        private $id_producto;
        private $cantidad;
        private $precio_compra;
        private $precio_compra_bs;
        private $subtotal;
        private $subtotal_bs;

        public function __construct($id = null, $id_ingreso = null, $id_producto = null, $cantidad = null, $precio_compra = null, $precio_compra_bs = null, $subtotal = null, $subtotal_bs = null) {
            parent::__construct();
            $this->id = $id;
            $this->id_ingreso = $id_ingreso;
            $this->id_producto = $id_producto;
            $this->cantidad = $cantidad;
            $this->precio_compra = $precio_compra;
            $this->precio_compra_bs = $precio_compra_bs;
            $this->subtotal = $subtotal;
            $this->subtotal_bs = $subtotal_bs;
        }

        public function getId() { return $this->id; }
        public function getIdIngreso() { return $this->id_ingreso; }
        public function getIdProducto() { return $this->id_producto; }
        public function getCantidad() { return $this->cantidad; }
        public function getPrecio_compra() { return $this->precio_compra; }
        public function getPrecio_compra_bs() { return $this->precio_compra_bs; }
        public function getSubtotal() { return $this->subtotal; }
        public function getSubtotal_bs() { return $this->subtotal_bs; }

        public function setId($id) { $this->id = $id; return $this; }
        public function setIdIngreso($id_ingreso) { $this->id_ingreso = $id_ingreso; return $this; }
        public function setIdProducto($id_producto) { $this->id_producto = $id_producto; return $this; }
        public function setCantidad($cantidad) { $this->cantidad = $cantidad; return $this; }
        public function setPrecio_compra($precio_compra) { $this->precio_compra = $precio_compra; return $this; }
        public function setPrecio_compra_bs($precio_compra_bs) { $this->precio_compra_bs = $precio_compra_bs; return $this; }
        public function setSubtotal($subtotal) { $this->subtotal = $subtotal; return $this; }
        public function setSubtotal_bs($subtotal_bs) { $this->subtotal_bs = $subtotal_bs; return $this; }

        public function insert(?\PDO $connection = null) {
            $sql = "INSERT INTO detalle_ingreso (id_ingreso, id_producto, cantidad, precio_compra, subtotal, precio_compra_bs, subtotal_bs) VALUES (:id_ingreso, :id_producto, :cantidad, :precio_compra, :subtotal, :precio_compra_bs, :subtotal_bs)";
            $connection = $connection ?? $this;
            $stmt = $connection->prepare($sql);
            $stmt->bindParam(":id_ingreso", $this->id_ingreso);
            $stmt->bindParam(":id_producto", $this->id_producto);
            $stmt->bindParam(":cantidad", $this->cantidad);
            $stmt->bindParam(":precio_compra", $this->precio_compra);
            $stmt->bindParam(":precio_compra_bs", $this->precio_compra_bs);
            $stmt->bindParam(":subtotal", $this->subtotal);
            $stmt->bindParam(":subtotal_bs", $this->subtotal_bs);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }

        public function search() {
            $sql = "SELECT * FROM detalle_ingreso";
            $stmt = $this->prepare($sql);
            if ($stmt->execute()) {
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }

        public function searchId($id) {
            $sql = "SELECT * FROM detalle_ingreso WHERE id = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id", $id);
            if ($stmt->execute()) {
                return $stmt->fetch(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }
        public function searchIngresoId() {
            $sql = "SELECT p.nombre AS nombre_producto, p.codigo_producto, detalle_ingreso.cantidad, detalle_ingreso.precio_compra, detalle_ingreso.precio_compra_bs, detalle_ingreso.subtotal, detalle_ingreso.subtotal_bs, ingresos.total AS total_ingreso, ingresos.total_bs AS total_ingreso_bs FROM detalle_ingreso INNER JOIN productos p ON p.id = detalle_ingreso.id_producto INNER JOIN ingresos ON ingresos.id = detalle_ingreso.id_ingreso WHERE detalle_ingreso.id_ingreso = :id_ingreso";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id_ingreso", $this->id_ingreso);
            if ($stmt->execute()) {
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }
        public function searchProductoId($id_producto) {
            $sql = "SELECT * FROM detalle_ingreso WHERE id_producto = :id_producto";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id_producto", $id_producto);
            if ($stmt->execute()) {
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }
    }
