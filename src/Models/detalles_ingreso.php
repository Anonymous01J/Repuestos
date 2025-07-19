<?php
    namespace Anonymous01j\Repuestos\Models;
    use Anonymous01j\Repuestos\Models\Conexion;

    class Detalles_Ingreso extends Conexion {
        private $id;
        private $id_ingreso;
        private $id_producto;
        private $cantidad;
        private $precio_compra;
        private $subtotal;

        public function __construct($id = null, $id_ingreso = null, $id_producto = null, $cantidad = null, $precio_compra = null, $subtotal = null) {
            parent::__construct();
            $this->id = $id;
            $this->id_ingreso = $id_ingreso;
            $this->id_producto = $id_producto;
            $this->cantidad = $cantidad;
            $this->precio_compra = $precio_compra;
            $this->subtotal = $subtotal;
        }

        public function getId() { return $this->id; }
        public function getIngresoId() { return $this->id_ingreso; }
        public function getProductoId() { return $this->id_producto; }
        public function getCantidad() { return $this->cantidad; }
        public function getPrecio_compra() { return $this->precio_compra; }
        public function getSubtotal() { return $this->subtotal; }

        public function setId($id) { $this->id = $id; return $this; }
        public function setIngresoId($id_ingreso) { $this->id_ingreso = $id_ingreso; return $this; }
        public function setProductoId($id_producto) { $this->id_producto = $id_producto; return $this; }
        public function setCantidad($cantidad) { $this->cantidad = $cantidad; return $this; }
        public function setPrecio_compra($precio_compra) { $this->precio_compra = $precio_compra; return $this; }
        public function setSubtotal($subtotal) { $this->subtotal = $subtotal; return $this; }

        public function insert($id_ingreso, $id_producto, $cantidad, $precio_compra, $subtotal) {
            $sql = "INSERT INTO detalle_ingreso (id_ingreso, id_producto, cantidad, precio_compra, subtotal) VALUES (:id_ingreso, :id_producto, :cantidad, :precio_compra, :subtotal)";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id_ingreso", $id_ingreso);
            $stmt->bindParam(":id_producto", $id_producto);
            $stmt->bindParam(":cantidad", $cantidad);
            $stmt->bindParam(":precio_compra", $precio_compra);
            $stmt->bindParam(":subtotal", $subtotal);
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
        public function searchIngresoId($id_ingreso) {
            $sql = "SELECT * FROM detalle_ingreso WHERE id_ingreso = :id_ingreso";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id_ingreso", $id_ingreso);
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
