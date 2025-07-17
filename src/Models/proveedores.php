<?php
    namespace Anonymous01j\Repuestos\Models;
    use Anonymous01j\Repuestos\Models\Conexion;

    class Proveedores extends Conexion {
        private $id;
        private $cedula_rif;
        private $nombre;
        private $telefono;
        private $direccion;

        public function __construct($id = null, $cedula_rif = null, $nombre = null, $telefono = null, $direccion = null) {
            parent::__construct();
            $this->id = $id;
            $this->cedula_rif = $cedula_rif;
            $this->nombre = $nombre;
            $this->telefono = $telefono;
            $this->direccion = $direccion;
        }

        public function getId() { return $this->id; }
        public function getCedulaRif() { return $this->cedula_rif; }
        public function getNombre() { return $this->nombre; }
        public function getTelefono() { return $this->telefono; }
        public function getDireccion() { return $this->direccion; }

        public function setId($id) { $this->id = $id; return $this; }
        public function setCedulaRif($cedula_rif) { $this->cedula_rif = $cedula_rif; return $this; }
        public function setNombre($nombre) { $this->nombre = $nombre; return $this; }
        public function setTelefono($telefono) { $this->telefono = $telefono; return $this; }
        public function setDireccion($direccion) { $this->direccion = $direccion; return $this; }

        public function insert() {
            $sql = "INSERT INTO proveedores (cedula_rif, nombre, telefono, direccion) VALUES (:cedula_rif, :nombre, :telefono, :direccion)";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":cedula_rif", $this->cedula_rif);
            $stmt->bindParam(":nombre", $this->nombre);
            $stmt->bindParam(":telefono", $this->telefono);
            $stmt->bindParam(":direccion", $this->direccion);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }

        public function search() {
            $sql = "SELECT * FROM proveedores WHERE estado = 1";
            $stmt = $this->prepare($sql);
            if ($stmt->execute()) {
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }
        public function searchInactive() {
            $sql = "SELECT * FROM proveedores where estado = 0";
            $stmt = $this->prepare($sql);
            if ($stmt->execute()) {
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }

        public function searchId($id) {
            $sql = "SELECT * FROM proveedores WHERE id = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id", $id);
            if ($stmt->execute()) {
                return $stmt->fetch(\PDO::FETCH_ASSOC);
            } else {
                return null;
            }
        }

        public function searchCedulaRif($cedula_rif) {
            $sql = "SELECT * FROM proveedores WHERE cedula_rif = :cedula_rif";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":cedula_rif", $cedula_rif);
            if ($stmt->execute()) {
                return $stmt->fetch(\PDO::FETCH_ASSOC);
            } else {
                return null;
            }
        }

        public function update() {
            $sql = "UPDATE proveedores SET cedula_rif = :cedula_rif, nombre = :nombre, telefono = :telefono, direccion = :direccion WHERE id = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id", $this->id);
            $stmt->bindParam(":cedula_rif", $this->cedula_rif);
            $stmt->bindParam(":nombre", $this->nombre);
            $stmt->bindParam(":telefono", $this->telefono);
            $stmt->bindParam(":direccion", $this->direccion);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
        public function delete() {
            $sql = "UPDATE proveedores SET estado = 0 WHERE id = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id", $this->id);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
        public function active() {
            $sql = "UPDATE proveedores SET estado = 1 WHERE id = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id", $this->id);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
    }