<?php
    namespace Anonymous01j\Repuestos\Models;
    use Anonymous01j\Repuestos\Models\Conexion;

    class Detalles_Venta extends Conexion {
        private $id;
        private $id_venta;
        private $id_producto;
        private $cantidad;
        private $precio_unitario;
        private $precio_unitario_bs;
        private $subtotal;
        private $subtotal_bs;
        private $aplico_precio_al_mayor;

        public function __construct($id = null, $id_venta = null, $id_producto = null, $cantidad = null, $precio_unitario = null, $precio_unitario_bs = null, $subtotal = null, $subtotal_bs = null, $aplico_precio_al_mayor = false) {
            parent::__construct();
            $this->id = $id;
            $this->id_venta = $id_venta;
            $this->id_producto = $id_producto;
            $this->cantidad = $cantidad;
            $this->precio_unitario = $precio_unitario;
            $this->precio_unitario_bs = $precio_unitario_bs;
            $this->subtotal = $subtotal;
            $this->subtotal_bs = $subtotal_bs;
            $this->aplico_precio_al_mayor = $aplico_precio_al_mayor;
        }

        public function getId() { return $this->id; }
        public function getIdVenta() { return $this->id_venta; }
        public function getIdProducto() { return $this->id_producto; }
        public function getCantidad() { return $this->cantidad; }
        public function getPrecioUnitario() { return $this->precio_unitario; }
        public function getPrecioUnitarioBs() { return $this->precio_unitario_bs; }
        public function getSubtotal() { return $this->subtotal; }
        public function getSubtotalBs() { return $this->subtotal_bs; }
        public function getAplicoPrecioAlMayor() { return $this->aplico_precio_al_mayor; }
        public function setId($id) { $this->id = $id; return $this; }
        public function setIdVenta($id_venta) { $this->id_venta = $id_venta; return $this; }
        public function setIdProducto($id_producto) { $this->id_producto = $id_producto; return $this; }
        public function setCantidad($cantidad) { $this->cantidad = $cantidad; return $this; }
        public function setPrecioUnitario($precio_unitario) { $this->precio_unitario = $precio_unitario; return $this; }
        public function setPrecioUnitarioBs($precio_unitario_bs) { $this->precio_unitario_bs = $precio_unitario_bs; return $this; }
        public function setSubtotal($subtotal) { $this->subtotal = $subtotal; return $this; }
        public function setSubtotalBs($subtotal_bs) { $this->subtotal_bs = $subtotal_bs; return $this; }
        public function setAplicoPrecioAlMayor($aplico_precio_al_mayor) { $this->aplico_precio_al_mayor = $aplico_precio_al_mayor; return $this; }

        public function insert() {
            $sql = "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unitario, precio_unitario_bs, subtotal, subtotal_bs, precio_al_mayor) VALUES (:id_venta, :id_producto, :cantidad, :precio_unitario, :precio_unitario_bs, :subtotal, :subtotal_bs, :precio_al_mayor)";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id_venta", $this->id_venta);
            $stmt->bindParam(":id_producto", $this->id_producto);
            $stmt->bindParam(":cantidad", $this->cantidad);
            $stmt->bindParam(":precio_unitario", $this->precio_unitario);
            $stmt->bindParam(":precio_unitario_bs", $this->precio_unitario_bs);
            $stmt->bindParam(":subtotal", $this->subtotal);
            $stmt->bindParam(":subtotal_bs", $this->subtotal_bs);
            $stmt->bindValue(":precio_al_mayor", (int) $this->aplico_precio_al_mayor, \PDO::PARAM_INT);
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
        public function searchId() {
            $sql = "SELECT * FROM detalle_venta WHERE id = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id", $this->id);
            if ($stmt->execute()) {
                return $stmt->fetch(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }
        public function searchIdVenta() {
            $sql = "SELECT p.nombre AS nombre_producto, p.codigo_producto, detalle_venta.cantidad, detalle_venta.precio_unitario, detalle_venta.precio_unitario_bs, detalle_venta.subtotal, detalle_venta.subtotal_bs, detalle_venta.precio_al_mayor, ventas.total AS total_venta, ventas.total_bs AS total_venta_bs FROM detalle_venta INNER JOIN productos p ON p.id = detalle_venta.id_producto INNER JOIN ventas ON ventas.id = detalle_venta.id_venta WHERE detalle_venta.id_venta = :id_venta";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id_venta", $this->id_venta);
            if ($stmt->execute()) {
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }
        public function searchIdProducto($id_producto) {
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