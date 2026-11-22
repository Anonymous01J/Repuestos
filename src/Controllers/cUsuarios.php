<?php
    namespace Anonymous01j\Repuestos\Controllers;
    use Anonymous01j\Repuestos\Models\Usuarios;
    use Anonymous01j\Repuestos\Models\Roles;
    class CUsuarios {
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
            $title = 'Usuarios';
            $usuario = new Usuarios();
            $rol = new Roles();
            $roles = $rol->search();
            $usuarios = $usuario->search();
            $usuariosInactive = $usuario->searchInactive();
            require_once __DIR__ . '/../Views/usuarios.php';
        }
        public function add() {
            if (isset($_POST['nombre']) && isset($_POST['rol']) && isset($_POST['usuario']) && isset($_POST['clave'])) {
                $usuario = new Usuarios();
                $usuario->setNombre($_POST['nombre'])
                        ->setRol($_POST['rol'])
                        ->setUsuario($_POST['usuario'])
                        ->setClave($_POST['clave']);
                if ($usuario->insert()) {
                    header("Location: ?c=CUsuarios/view&success=1");
                } else {
                    header("Location: ?c=CUsuarios/view&error=1");
                }
            } else {
                header("Location: ?c=CUsuarios/view&error=2");
            }
        }
        public function edit() {
            if (isset($_POST['id']) && isset($_POST['rol']) && isset($_POST['nombre']) && isset($_POST['usuario']) && isset($_POST['clave'])) {
                $usuario = new Usuarios();
                $usuario->setId($_POST['id'])
                        ->setRol($_POST['rol'])
                        ->setNombre($_POST['nombre'])
                        ->setUsuario($_POST['usuario'])
                        ->setClave($_POST['clave']);
                if ($usuario->update()) {
                    header("Location: ?c=CUsuarios/view&success=1");
                } else {
                    header("Location: ?c=CUsuarios/view&error=1");
                }
            } else {
                header("Location: ?c=CUsuarios/view&error=2");
            }
        }
        public function delete() {
            if (isset($_POST['id'])) {
                $usuario = new Usuarios();
                $usuario->setId($_POST['id']);
                if ($usuario->delete()) {
                    header("Location: ?c=CUsuarios/view&success=1");
                } else {
                    header("Location: ?c=CUsuarios/view&error=1");
                }
            } else {
                header("Location: ?c=CUsuarios/view&error=2");
            }
        }
        public function active() {
            if (isset($_POST['id'])) {
                $usuario = new Usuarios();
                $usuario->setId($_POST['id']);
                if ($usuario->active()) {
                    header("Location: ?c=CUsuarios/view&success=1");
                } else {
                    header("Location: ?c=CUsuarios/view&error=1");
                }
            } else {
                header("Location: ?c=CUsuarios/view&error=2");
            }
        }
    }