<?php
    namespace Anonymous01j\Repuestos\Controllers;
    use Anonymous01j\Repuestos\Models\Proveedores;

    class CProveedores {
        public function view() {
            if (!isset($_SESSION['id'])) {
                if (isset($_COOKIE['id'])) {
                    $_SESSION['id'] = $_COOKIE['id'];
                    $_SESSION['nombre'] = $_COOKIE['nombre'];
                    $_SESSION['rol'] = $_COOKIE['rol'];
                } else {
                    require_once __DIR__ . '/../Views/login.php';
                    exit();
                }
            }
            $title = 'Proveedores';
            $proveedor = new Proveedores();
            $proveedores = $proveedor->search();
            $proveedoresInactive = $proveedor->searchInactive();
            require_once __DIR__ . '/../Views/proveedores.php';
        }
        public function add() {
            if (isset($_POST['cedula_rif']) && isset($_POST['nombre']) && isset($_POST['telefono'])) {
                $proveedor = new Proveedores();
                $proveedor->setCedulaRif($_POST['cedula_rif'])
                        ->setNombre($_POST['nombre'])
                        ->setTelefono($_POST['telefono'])
                        ->setDireccion($_POST['direccion']);
                if ($proveedor->insert()) {
                    header("Location: ?c=CProveedores/view&success=1");
                } else {
                    header("Location: ?c=CProveedores/view&error=1");
                }
            } else {
                header("Location: ?c=CProveedores/view&error=2");
            }
        }
        public function edit() {
            if (isset($_POST['id']) && isset($_POST['telefono']) && isset($_POST['direccion'])) {
                $proveedor = new Proveedores();
                $proveedor->setId($_POST['id'])
                        ->setTelefono($_POST['telefono'])
                        ->setDireccion($_POST['direccion']);
                if ($proveedor->update()) {
                    header("Location: ?c=CProveedores/view&success=1");
                } else {
                    header("Location: ?c=CProveedores/view&error=1");
                }
            } else {
                header("Location: ?c=CProveedores/view&error=2");
            }
        }
        public function delete() {
            if (isset($_POST['id'])) {
                $proveedor = new Proveedores();
                $proveedor->setId($_POST['id']);
                if ($proveedor->delete()) {
                    header("Location: ?c=CProveedores/view&success=1");
                } else {
                    header("Location: ?c=CProveedores/view&error=1");
                }
            } else {
                header("Location: ?c=CProveedores/view&error=2");
            }
        }
        public function active() {
            if (isset($_POST['id'])) {
                $proveedor = new Proveedores();
                $proveedor->setId($_POST['id']);
                if ($proveedor->active()) {
                    header("Location: ?c=CProveedores/view&success=1");
                } else {
                    header("Location: ?c=CProveedores/view&error=1");
                }
            } else {
                header("Location: ?c=CProveedores/view&error=2");
            }
        }
    }