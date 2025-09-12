<?php
    namespace Anonymous01j\Repuestos\Controllers;
    use Anonymous01j\Repuestos\Models\Categorias;

    class CCategorias {
        public function view() {
            if (!isset($_SESSION['id'])) {
                if (isset($_COOKIE['id'])) {
                    $_SESSION['id'] = $_COOKIE['id'];
                    $_SESSION['nombre'] = $_COOKIE['nombre'];
                    $_SESSION['rol'] = $_COOKIE['rol'];
                } else {
                    require_once __DIR__ . '/../Views/Login.php';
                    exit();
                }
            }
            $title = 'Categorias';
            $categoria = new Categorias();
            $categorias = $categoria->search();
            require_once __DIR__ . '/../Views/Categorias.php';
        }

        public function add() {
            if (isset($_POST['nombre'], $_POST['codigo'], $_POST['descripcion'])) {
                $categoria = new Categorias();
                $categoria->setNombre($_POST['nombre'])
                          ->setCodigo($_POST['codigo'])
                          ->setDescripcion($_POST['descripcion']);
                if ($categoria->insert()) {
                    header("Location: ?c=CCategorias/view&success=1");
                } else {
                    header("Location: ?c=CCategorias/view&error=1");
                }
            } else {
                header("Location: ?c=CCategorias/view&error=2");
            }
        }

        public function edit() {
            if (isset($_POST['id'], $_POST['nombre'], $_POST['codigo'], $_POST['descripcion'])) {
                $categoria = new Categorias();
                $categoria->setId($_POST['id'])
                          ->setNombre($_POST['nombre'])
                          ->setCodigo($_POST['codigo'])
                          ->setDescripcion($_POST['descripcion']);
                if ($categoria->update()) {
                    header("Location: ?c=CCategorias/view&success=1");
                } else {
                    header("Location: ?c=CCategorias/view&error=1");
                }
            } else {
                header("Location: ?c=CCategorias/view&error=2");
            }
        }
        public function delete() {
            if (isset($_POST['id'])) {
                $categoria = new Categorias();
                $categoria->setId($_POST['id']);
                $sql = "DELETE FROM categorias WHERE id = :id";
                $stmt = $categoria->prepare($sql);
                $stmt->bindParam(":id", $categoria->getId());
                if ($stmt->execute()) {
                    header("Location: ?c=CCategorias/view&success=1");
                } else {
                    header("Location: ?c=CCategorias/view&error=1");
                }
            } else {
                header("Location: ?c=CCategorias/view&error=2");
            }
        }
    }