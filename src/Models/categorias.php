<?php
    namespace Anonymous01j\Repuestos\Models;
    use Anonymous01j\Repuestos\Models\Conexion;

    class Categorias extends Conexion {
        private $id;
        private $nombre;
        private $codigo; // Added property for codigo
        private $descripcion; // Added property for descripcion

        public function __construct($id = null, $nombre = null, $codigo = null, $descripcion = null) {
            parent::__construct();
            $this->id = $id;
            $this->nombre = $nombre;
            $this->codigo = $codigo;
            $this->descripcion = $descripcion;
        }

        public function getId() { return $this->id; }
        public function getNombre() { return $this->nombre; }
        public function getCodigo() { return $this->codigo; }
        public function getDescripcion() { return $this->descripcion; }

        public function setId($id) { $this->id = $id; return $this; }
        public function setNombre($nombre) { $this->nombre = $nombre; return $this; }
        public function setCodigo($codigo) { $this->codigo = $codigo; return $this; }
        public function setDescripcion($descripcion) { $this->descripcion = $descripcion; return $this; }

        public function insert() {
            $sql = "INSERT INTO categorias (nombre, codigo, descripcion) VALUES (:nombre, :codigo, :descripcion)";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":nombre", $this->nombre);
            $stmt->bindParam(":codigo", $this->codigo);
            $stmt->bindParam(":descripcion", $this->descripcion);
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
            $sql = "UPDATE categorias SET nombre = :nombre, codigo = :codigo, descripcion = :descripcion WHERE id = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id", $this->id);
            $stmt->bindParam(":nombre", $this->nombre);
            $stmt->bindParam(":codigo", $this->codigo);
            $stmt->bindParam(":descripcion", $this->descripcion);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
    }