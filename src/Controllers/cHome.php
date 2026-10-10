<?php
    namespace Anonymous01j\Repuestos\Controllers;
    use Anonymous01j\Repuestos\Models\Usuarios;
    use Anonymous01j\Repuestos\Models\Tasa;
    use Anonymous01j\Repuestos\Models\StockBajo;
    use Anonymous01j\Repuestos\Models\Estadistica;
    use Anonymous01j\Repuestos\Models\Categorias;

    class CHome {
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
            $title = 'Inicio';

            // Cargar productos con stock bajo para el dashboard
            $stockBajoModel   = new StockBajo();
            $productosAlerta  = $stockBajoModel->search();

            $estadisticaModel = new Estadistica();
            $resumenEstadistico = $estadisticaModel->obtenerResumenMensual();

            require_once __DIR__ . '/../Views/Home.php';
        }

        public function estadisticas() {
            if (!isset($_SESSION['id'])) {
                if (isset($_COOKIE['id'])) {
                    $_SESSION['id'] = $_COOKIE['id'];
                    $_SESSION['nombre'] = $_COOKIE['nombre'] ?? '';
                    $_SESSION['rol'] = $_COOKIE['rol'] ?? '';
                } else {
                    require_once __DIR__ . '/../Views/Login.php';
                    exit();
                }
            }

            $title = 'Estadísticas de ventas';
            $periodosPermitidos = ['MES', 'ULTIMOS_6_MESES', 'ANIO', 'INTERVALO', 'TODO'];
            $periodo = strtoupper(trim($_GET['periodo'] ?? 'MES'));
            if (!in_array($periodo, $periodosPermitidos, true)) {
                $periodo = 'MES';
            }

            $fechaDesde = $_GET['fecha_desde'] ?? null;
            $fechaHasta = $_GET['fecha_hasta'] ?? null;
            $fechaDesde = is_string($fechaDesde) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaDesde) ? $fechaDesde : null;
            $fechaHasta = is_string($fechaHasta) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaHasta) ? $fechaHasta : null;
            $categoriaId = isset($_GET['categoria_id']) && $_GET['categoria_id'] !== ''
                ? filter_var($_GET['categoria_id'], FILTER_VALIDATE_INT)
                : null;
            if ($categoriaId === false || ($categoriaId !== null && $categoriaId <= 0)) {
                $categoriaId = null;
            }

            $estadisticaModel = new Estadistica();
            try {
                $rankings = $estadisticaModel->obtenerRankings(
                    $periodo,
                    $periodo === 'INTERVALO' ? $fechaDesde : null,
                    $periodo === 'INTERVALO' ? $fechaHasta : null,
                    $categoriaId
                );
                $estadisticaError = null;
            } catch (\Throwable $exception) {
                $rankings = array_fill_keys([
                    'mas_vendidos', 'menos_vendidos', 'mas_rentables',
                    'menos_rentables', 'mayor_ganancia', 'menor_ganancia',
                ], []);
                $estadisticaError = $exception->getMessage();
            }

            $categoriasModel = new Categorias();
            $categorias = $categoriasModel->search();
            require_once __DIR__ . '/../Views/estadisticas.php';
        }

        public function login() {
            $usuario = new Usuarios();

            if (isset($_POST['usuario']) && isset($_POST['clave'])) {
                $login = $usuario->login($_POST['usuario'], $_POST['clave']);
                if ($login) {
                    session_start();
                    $_SESSION['id'] = $login['id'];
                    $_SESSION['nombre'] = $login['nombre'];
                    $rolData = $usuario->searchRol($_POST['usuario']);
                    $_SESSION['rol'] = $rolData ? $rolData['rol_nombre'] : '';

                    if (isset($_POST['recordar'])) {
                        setcookie("id", $login['id'], time() + 3600 * 24 * 30, "/");
                        setcookie("nombre", $login['nombre'], time() + 3600 * 24 * 30, "/");
                        setcookie("rol", $rolData ? $rolData['rol_nombre'] : '', time() + 3600 * 24 * 30, "/");
                    }
                    header("Location: ?c=CHome/view");
                } else {
                    header("Location: ?c=CHome/view&error=1");
                }
            }
        }

        public function logout() {
            session_destroy();
            setcookie("id", "", time() - 3600, "/");
            setcookie("nombre", "", time() - 3600, "/");
            setcookie("rol", "", time() - 3600, "/");
            require_once __DIR__ . '/../Views/Login.php';
            exit();
        }

        public function tasa() {
            $tasa = new Tasa();
            $tasaData = $tasa->search();
            if (count($tasaData) > 0) {
                echo json_encode([
                    'id'                 => $tasaData[0]['id'],
                    'valor'              => $tasaData[0]['valor'],
                    'fecha_actualizacion'=> $tasaData[0]['fecha_actualizacion'],
                ]);
            } else {
                echo json_encode(['error' => 'No se encontró la tasa']);
            }
        }

        public function updateTasa() {
            if (isset($_POST['valor']) && isset($_POST['id'])) {
                $tasa = new Tasa();
                $tasa->setId($_POST['id']);
                $tasa->setValor(str_replace(',', '.', $_POST['valor']));
                $tasa->update();
                header("Location: ?c=CHome/view");
            } else {
                header("Location: ?c=CHome/view&error=3");
            }
        }
    }