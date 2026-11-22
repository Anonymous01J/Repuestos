<?php
    namespace Anonymous01j\Repuestos\Models;
    use PDOException;
    use PDO;

    class Conexion {
        private static ?PDO $instance = null;

        public static function getInstance(): PDO {
            if (self::$instance === null) {
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "repuestos";
                try {
                    self::$instance = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
                    self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch(PDOException $e) {
                    die("Error en la conexión: " . $e->getMessage());
                }
            }
            return self::$instance;
        }

        public function __construct() {
            // Se asegura de inicializar la instancia compartida de PDO
            self::getInstance();
        }

        public function prepare($query, $options = []) {
            return self::getInstance()->prepare($query, $options);
        }

        public function lastInsertId($name = null) {
            return self::getInstance()->lastInsertId($name);
        }

        public function beginTransaction() {
            return self::getInstance()->beginTransaction();
        }

        public function commit() {
            return self::getInstance()->commit();
        }

        public function rollBack() {
            return self::getInstance()->rollBack();
        }

        public function inTransaction() {
            return self::getInstance()->inTransaction();
        }
    }