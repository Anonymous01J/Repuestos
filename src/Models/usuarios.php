<?php
    namespace Anonymous01j\Repuestos\Models;
    use Anonymous01j\Repuestos\Models\Conexion;

    class Usuarios extends Conexion {
        private $id;
        private $nombre;
        private $rol;
        private $usuario;
        private $clave;

        public function __construct($id = null, $nombre = null, $rol=null, $usuario = null, $clave = null) {
            parent::__construct();
            $this->id = $id;
            $this->nombre = $nombre;
            $this->rol = $rol;
            $this->usuario = $usuario;
            $this->clave = $clave;
        }

        public function getId() { return $this->id; }
        public function getNombre() { return $this->nombre; }
        public function getRol() { return $this->rol; }
        public function getUsuario() { return $this->usuario; }
        public function getClave() { return $this->clave; }

        public function setId($id) { $this->id = $id; return $this; }
        public function setNombre($nombre) { $this->nombre = $nombre; return $this; }
        public function setRol($rol) { $this->rol = $rol; return $this; }
        public function setUsuario($usuario) { $this->usuario = $usuario; return $this; }
        public function setClave($clave) { $this->clave = $clave; return $this; }

        public function insert() {
            $sql = "INSERT INTO usuarios (id_rol, nombre, usuario, clave) VALUES (:rol, :nombre, :usuario, :clave)";
            $stmt = $this->prepare($sql);
            $clave = hash('sha256', (string) $this->clave);
            $stmt->bindParam(":rol", $this->rol);
            $stmt->bindParam(":nombre", $this->nombre);
            $stmt->bindParam(":usuario", $this->usuario);
            $stmt->bindParam(":clave", $clave);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
        public function login($usuario, $clave) {
            $sql = "SELECT * FROM usuarios WHERE usuario = :usuario AND estado = 1";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":usuario", $usuario);
            if (!$stmt->execute()) {
                return false;
            }
            $usuarioData = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$usuarioData) {
                return false;
            }

            $claveHash = hash('sha256', (string) $clave);
            $claveGuardada = (string) $usuarioData['clave'];
            if (preg_match('/^[a-f0-9]{64}$/i', $claveGuardada)) {
                return hash_equals(strtolower($claveGuardada), $claveHash) ? $usuarioData : false;
            }

            if (!hash_equals($claveGuardada, (string) $clave)) {
                return false;
            }

            $upgrade = $this->prepare("UPDATE usuarios SET clave = :clave WHERE id = :id");
            $upgrade->bindParam(":clave", $claveHash);
            $upgrade->bindParam(":id", $usuarioData['id']);
            $upgrade->execute();
            $usuarioData['clave'] = $claveHash;
            return $usuarioData;
        }
        public function search() {
            $sql = "SELECT id, nombre, id_rol, (SELECT nombre FROM roles WHERE id=usuarios.id_rol) AS rol, usuario FROM usuarios WHERE estado = 1";
            $stmt = $this->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        public function searchInactive() {
            $sql = "SELECT id, nombre, id_rol, (SELECT nombre FROM roles WHERE id=usuarios.id_rol) AS rol, usuario FROM usuarios WHERE estado = 0";
            $stmt = $this->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function searchId($id) {
            $sql = "SELECT * FROM usuarios WHERE id = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        public function searchRol($usuario) {
            $sql = "SELECT (SELECT nombre FROM roles WHERE id=usuarios.id_rol) AS rol_nombre FROM usuarios WHERE usuario = :usuario";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":usuario", $usuario);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        public function update() {
            $cambiarClave = (string) $this->clave !== '';
            $sql = "UPDATE usuarios SET nombre = :nombre, id_rol = :rol, usuario = :usuario";
            if ($cambiarClave) {
                $sql .= ", clave = :clave";
            }
            $sql .= " WHERE id = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id", $this->id);
            $stmt->bindParam(":nombre", $this->nombre);
            $stmt->bindParam(":rol", $this->rol);
            $stmt->bindParam(":usuario", $this->usuario);
            if ($cambiarClave) {
                $clave = hash('sha256', (string) $this->clave);
                $stmt->bindParam(":clave", $clave);
            }
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
        public function delete() {
            $sql = "UPDATE usuarios SET estado = 0 WHERE id = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id", $this->id);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
        public function active() {
            $sql = "UPDATE usuarios SET estado = 1 WHERE id = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id", $this->id);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
    }
