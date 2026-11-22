<?php
    namespace Anonymous01j\Repuestos\Models;
    use Anonymous01j\Repuestos\Models\Conexion;

    class Ingresos extends Conexion {
        private $id;
        private $id_usuario;
        private $fecha;
        private $total;
        private $total_bs;

        public function __construct($id = null, $id_usuario = null, $fecha = null, $total = null, $total_bs = null) {
            parent::__construct();
            $this->id = $id;
            $this->id_usuario = $id_usuario;
            $this->fecha = $fecha;
            $this->total = $total;
            $this->total_bs = $total_bs;
        }

        public function getId() { return $this->id; }
        public function getIdUsuario() { return $this->id_usuario; }
        public function getFecha() { return $this->fecha; }
        public function getTotal() { return $this->total; }
        public function getTotalBs() { return $this->total_bs; }

        public function setId($id) { $this->id = $id; return $this; }
        public function setIdUsuario($id_usuario) { $this->id_usuario = $id_usuario; return $this; }
        public function setFecha($fecha) { $this->fecha = $fecha; return $this; }
        public function setTotal($total) { $this->total = $total; return $this; }
        public function setTotalBs($total_bs) { $this->total_bs = $total_bs; return $this; }

        public function insert() {
            $sql = "INSERT INTO ingresos (id_usuario, total, total_bs) VALUES (:id_usuario, :total, :total_bs)";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id_usuario", $this->id_usuario);
            $stmt->bindParam(":total", $this->total);
            $stmt->bindParam(":total_bs", $this->total_bs);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
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
        public function search() {
            $sql = "SELECT id, (SELECT nombre FROM usuarios WHERE id = id_usuario) AS usuario, fecha, total, total_bs FROM ingresos ORDER BY id DESC";
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
            // NOTA: La tabla `ingresos` no posee la columna `id_proveedor`. Si la columna existe en el esquema futuro,
            // descomentar la consulta con id_proveedor.
            try {
                $sql = "SELECT * FROM ingresos WHERE id_proveedor = :id_proveedor";
                $stmt = $this->prepare($sql);
                $stmt->bindParam(":id_proveedor", $id_proveedor);
                if ($stmt->execute()) {
                    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
                }
            } catch (\PDOException $e) {
                // Prevenir excepción si la columna id_proveedor aún no existe en la base de datos
                return [];
            }
            return [];
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