<?php
    namespace Anonymous01j\Repuestos\Models;
    use Anonymous01j\Repuestos\Models\Conexion;

    class Ventas extends Conexion {
        private $id;
        private $id_cliente;
        private $id_usuario;
        private $fecha;
        private $total;
        private $total_bs;

        public function __construct($id = null, $id_cliente = null, $id_usuario = null, $fecha = null, $total = null, $total_bs = null) {
            parent::__construct();
            $this->id = $id;
            $this->id_cliente = $id_cliente;
            $this->id_usuario = $id_usuario;
            $this->fecha = $fecha;
            $this->total = $total;
            $this->total_bs = $total_bs;
        }

        public function getId() { return $this->id; }
        public function getClienteId() { return $this->id_cliente; }
        public function getUsuarioId() { return $this->id_usuario; }
        public function getFecha() { return $this->fecha; }
        public function getTotal() { return $this->total; }
        public function getTotalBs() { return $this->total_bs; }

        public function setId($id) { $this->id = $id; return $this; }
        public function setClienteId($id_cliente) { $this->id_cliente = $id_cliente; return $this; }
        public function setUsuarioId($id_usuario) { $this->id_usuario = $id_usuario; return $this; }
        public function setFecha($fecha) { $this->fecha = $fecha; return $this; }
        public function setTotal($total) { $this->total = $total; return $this; }
        public function setTotalBs($total_bs) { $this->total_bs = $total_bs; return $this; }

        public function insert() {
            $sql = "INSERT INTO ventas (id_cliente, id_usuario, total, total_bs) VALUES (:id_cliente, :id_usuario, :total, :total_bs)";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id_cliente", $this->id_cliente);
            $stmt->bindParam(":id_usuario", $this->id_usuario);
            $stmt->bindParam(":total", $this->total);
            $stmt->bindParam(":total_bs", $this->total_bs);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
        public function search() {
            $sql = "SELECT * FROM ventas";
            $stmt = $this->prepare($sql);
            if ($stmt->execute()) {
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }
        public function searchId($id) {
            $sql = "SELECT * FROM ventas WHERE id = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id", $id);
            if ($stmt->execute()) {
                return $stmt->fetch(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }
        public function searchClienteId($id_cliente) {
            $sql = "SELECT * FROM ventas WHERE id_cliente = :id_cliente";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id_cliente", $id_cliente);
            if ($stmt->execute()) {
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }
        public function searchUsuarioId($id_usuario) {
            $sql = "SELECT * FROM ventas WHERE id_usuario = :id_usuario";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id_usuario", $id_usuario);
            if ($stmt->execute()) {
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }
        public function getLastID() {
            $sql = "SELECT LAST_INSERT_ID() AS last_id";
            $stmt = $this->prepare($sql);
            if ($stmt->execute()) {
                return $stmt->fetchColumn();
            } else {
                return null;
            }
        }
    }
