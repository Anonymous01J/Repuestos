<?php
    namespace Anonymous01j\Repuestos\Controllers;
    use Anonymous01j\Repuestos\Models\Clientes;

    class CClientes {
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
            $title = 'Clientes';
            $cliente = new Clientes();
            $clientes = $cliente->search();
            $clientesInactive = $cliente->searchInactive();
            require_once __DIR__ . '/../Views/clientes.php';
        }
        public function add() {
            if (isset($_POST['nombre']) && isset($_POST['telefono']) && isset($_POST['direccion']) && isset($_POST['cedula_rif'])) {
                $cliente = new Clientes();
                $cliente->setNombre($_POST['nombre'])
                        ->setTelefono($_POST['telefono'])
                        ->setDireccion($_POST['direccion'])
                        ->setCedulaRif($_POST['cedula_rif']);
                if ($cliente->insert()) {
                    header("Location: ?c=CClientes/view&success=1");
                } else {
                    header("Location: ?c=CClientes/view&error=1");
                }
            } else {
                header("Location: ?c=CClientes/view&error=2");
            }
        }
        public function edit() {
            if (isset($_POST['id']) && isset($_POST['telefono']) && isset($_POST['direccion'])) {
                $cliente = new Clientes();
                $cliente->setId($_POST['id'])
                        ->setTelefono($_POST['telefono'])
                        ->setDireccion($_POST['direccion']);
                if ($cliente->update()) {
                    header("Location: ?c=CClientes/view&success=1");
                } else {
                    header("Location: ?c=CClientes/view&error=1");
                }
            } else {
                header("Location: ?c=CClientes/view&error=2");
            }
        }
        public function delete() {
            if (isset($_POST['id'])) {
                $cliente = new Clientes();
                $cliente->setId($_POST['id']);
                if ($cliente->delete()) {
                    header("Location: ?c=CClientes/view&success=1");
                } else {
                    header("Location: ?c=CClientes/view&error=1");
                }
            } else {
                header("Location: ?c=CClientes/view&error=2");
            }
        }
        public function active() {
            if (isset($_POST['id'])) {
                $cliente = new Clientes();
                $cliente->setId($_POST['id']);
                if ($cliente->active()) {
                    header("Location: ?c=CClientes/view&success=1");
                } else {
                    header("Location: ?c=CClientes/view&error=1");
                }
            } else {
                header("Location: ?c=CClientes/view&error=2");
            }
        }
    }