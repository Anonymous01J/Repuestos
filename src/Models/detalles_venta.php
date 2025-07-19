<?php
    namespace Anonymous01j\Repuestos\Models;
    use Anonymous01j\Repuestos\Models\Conexion;

    class Detalles_Venta extends Conexion {
        private $id;
        private $id_venta;
        private $id_producto;
        private $cantidad;
        private $precio_unitario;
        private $subtotal;

        public function __construct($id = null, $id_venta = null, $id_producto = null, $cantidad = null, $precio_unitario = null, $subtotal = null) {
            parent::__construct();
            $this->id = $id;
            $this->id_venta = $id_venta;
            $this->id_producto = $id_producto;
            $this->cantidad = $cantidad;
            $this->precio_unitario = $precio_unitario;
            $this->subtotal = $subtotal;
        }

        public function getId() { return $this->id; }
        public function getVentaId() { return $this->id_venta; }
        public function getProductoId() { return $this->id_producto; }
        public function getCantidad() { return $this->cantidad; }
        public function getPrecioUnitario() { return $this->precio_unitario; }
        public function getSubtotal() { return $this->subtotal; }

        public function setId($id) { $this->id = $id; return $this; }
        public function setVentaId($id_venta) { $this->id_venta = $id_venta; return $this; }
        public function setProductoId($id_producto) { $this->id_producto = $id_producto; return $this; }
        public function setCantidad($cantidad) { $this->cantidad = $cantidad; return $this; }
        public function setPrecioUnitario($precio_unitario) { $this->precio_unitario = $precio_unitario; return $this; }
        public function setSubtotal($subtotal) { $this->subtotal = $subtotal; return $this; }

        public function insert($id_venta, $id_producto, $cantidad, $precio_unitario, $subtotal) {
            $sql = "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unitario, subtotal) VALUES (:id_venta, :id_producto, :cantidad, :precio_unitario, :subtotal)";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id_venta", $id_venta);
            $stmt->bindParam(":id_producto", $id_producto);
            $stmt->bindParam(":cantidad", $cantidad);
            $stmt->bindParam(":precio_unitario", $precio_unitario);
            $stmt->bindParam(":subtotal", $subtotal);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
        public function search() {
            $sql = "SELECT * FROM detalle_venta";
            $stmt = $this->prepare($sql);
            if ($stmt->execute()) {
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }
        public function searchId($id) {
            $sql = "SELECT * FROM detalle_venta WHERE id = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id", $id);
            if ($stmt->execute()) {
                return $stmt->fetch(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }
        public function searchVentaId($id_venta) {
            $sql = "SELECT * FROM detalle_venta WHERE id_venta = :id_venta";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id_venta", $id_venta);
            if ($stmt->execute()) {
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }
        public function searchProductoId($id_producto) {
            $sql = "SELECT * FROM detalle_venta WHERE id_producto = :id_producto";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id_producto", $id_producto);
            if ($stmt->execute()) {
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }
    }