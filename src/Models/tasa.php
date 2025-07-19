<?php
    namespace Anonymous01j\Repuestos\Models;
    use Anonymous01j\Repuestos\Models\Conexion;

    class Tasa extends Conexion {
        private $id;
        private $valor;

        public function __construct($id = null, $valor = null) {
            parent::__construct();
            $this->id = $id;
            $this->valor = $valor;
        }

        public function getId() { return $this->id; }
        public function getValor() { return $this->valor; }

        public function setId($id) { $this->id = $id; return $this; }
        public function setValor($valor) { $this->valor = $valor; return $this; }

        public function insert() {
            $sql = "INSERT INTO tasa (valor) VALUES (:valor)";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":valor", $this->valor);
            $stmt->execute();
        }

        public function search() {
            $sql = "SELECT * FROM tasa";
            $stmt = $this->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function searchId($id) {
            $sql = "SELECT * FROM tasa WHERE id = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        public function update() {
            $sql = "UPDATE tasa SET valor = :valor, fecha_actualizacion = NOW() WHERE id = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id", $this->id);
            $stmt->bindParam(":valor", $this->valor);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
    }