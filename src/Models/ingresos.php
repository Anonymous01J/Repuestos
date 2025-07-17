<?php
    namespace Anonymous01j\Repuestos\Models;
    use Anonymous01j\Repuestos\Models\Conexion;

    class Ingresos extends Conexion {
        private $id;
        private $id_proveedor;
        private $id_usuario;
        private $fecha;
        private $total;

        public function __construct($id = null, $id_proveedor = null, $id_usuario = null, $fecha = null, $total = null) {
            parent::__construct();
            $this->id = $id;
            $this->id_proveedor = $id_proveedor;
            $this->id_usuario = $id_usuario;
            $this->fecha = $fecha;
            $this->total = $total;
        }

        public function getId() { return $this->id; }
        public function getProveedorId() { return $this->id_proveedor; }
        public function getUsuarioId() { return $this->id_usuario; }
        public function getFecha() { return $this->fecha; }
        public function getTotal() { return $this->total; }

        public function setId($id) { $this->id = $id; return $this; }
        public function setProveedorId($id_proveedor) { $this->id_proveedor = $id_proveedor; return $this; }
        public function setUsuarioId($id_usuario) { $this->id_usuario = $id_usuario; return $this; }
        public function setFecha($fecha) { $this->fecha = $fecha; return $this; }
        public function setTotal($total) { $this->total = $total; return $this; }

        public function insert($id_proveedor, $id_usuario, $fecha, $total) {
            $sql = "INSERT INTO ingresos (id_proveedor, id_usuario, fecha, total) VALUES (:id_proveedor, :id_usuario, :fecha, :total)";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id_proveedor", $id_proveedor);
            $stmt->bindParam(":id_usuario", $id_usuario);
            $stmt->bindParam(":fecha", $fecha);
            $stmt->bindParam(":total", $total);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
        public function search() {
            $sql = "SELECT * FROM ingresos";
            $stmt = $this->prepare($sql);
            if ($stmt->execute()) {
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }
        public function searchId($id) {
            $sql = "SELECT * FROM ingresos WHERE id = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id", $id);
            if ($stmt->execute()) {
                return $stmt->fetch(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }
        public function searchProveedorId($id_proveedor) {
            $sql = "SELECT * FROM ingresos WHERE id_proveedor = :id_proveedor";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id_proveedor", $id_proveedor);
            if ($stmt->execute()) {
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }
        public function searchUsuarioId($id_usuario) {
            $sql = "SELECT * FROM ingresos WHERE id_usuario = :id_usuario";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id_usuario", $id_usuario);
            if ($stmt->execute()) {
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }
        public function searchFecha($fecha) {
            $sql = "SELECT * FROM ingresos WHERE DATE(fecha) = :fecha";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":fecha", $fecha);
            if ($stmt->execute()) {
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }
        
    }