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
            if (isset($_POST['nombre'])) {
                $categoria = new Categorias();
                $categoria->setNombre($_POST['nombre']);
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
            if (isset($_POST['id']) && isset($_POST['nombre'])) {
                $categoria = new Categorias();
                $categoria->setId($_POST['id'])
                        ->setNombre($_POST['nombre']);
                if ($categoria->update()) {
                    header("Location: ?c=CCategorias/view&success=1");
                } else {
                    header("Location: ?c=CCategorias/view&error=1");
                }
            } else {
                header("Location: ?c=CCategorias/view&error=2");
            }
        }
    }