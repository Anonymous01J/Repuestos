<?php
    namespace Anonymous01j\Repuestos\Models;
    use Anonymous01j\Repuestos\Models\Conexion;

    class Categorias extends Conexion {
        private $id;
        private $nombre;

        public function __construct($id = null, $nombre = null) {
            parent::__construct();
            $this->id = $id;
            $this->nombre = $nombre;
        }

        public function getId() { return $this->id; }
        public function getNombre() { return $this->nombre; }

        public function setId($id) { $this->id = $id; return $this; }
        public function setNombre($nombre) { $this->nombre = $nombre; return $this; }

        public function insert() {
            $sql = "INSERT INTO categorias (nombre) VALUES (:nombre)";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":nombre", $this->nombre);
            $stmt->execute();
        }

        public function search() {
            $sql = "SELECT * FROM categorias";
            $stmt = $this->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function searchId($id) {
            $sql = "SELECT * FROM categorias WHERE id = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        public function update() {
            $sql = "UPDATE categorias SET nombre = :nombre WHERE id = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id", $this->id);
            $stmt->bindParam(":nombre", $this->nombre);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
    }