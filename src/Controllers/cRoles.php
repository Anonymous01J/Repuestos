<?php
    namespace Anonymous01j\Repuestos\Controllers;
    use Anonymous01j\Repuestos\Models\Roles;

    class CRoles {
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
            $title = 'Roles';
            $role = new Roles();
            $roles = $role->search();
            require_once __DIR__ . '/../Views/Roles.php';
        }

        public function add() {
            if (isset($_POST['nombre'])) {
                $role = new Roles();
                $role->setNombre($_POST['nombre']);
                if ($role->insert()) {
                    header("Location: ?c=CRoles/view&success=1");
                } else {
                    header("Location: ?c=CRoles/view&error=1");
                }
            } else {
                header("Location: ?c=CRoles/view&error=2");
            }
        }

        public function edit() {
            if (isset($_POST['id']) && isset($_POST['nombre'])) {
                $role = new Roles();
                $role->setId($_POST['id'])
                    ->setNombre($_POST['nombre']);
                if ($role->update()) {
                    header("Location: ?c=CRoles/view&success=1");
                } else {
                    header("Location: ?c=CRoles/view&error=1");
                }
            } else {
                header("Location: ?c=CRoles/view&error=2");
            }
        }
    }