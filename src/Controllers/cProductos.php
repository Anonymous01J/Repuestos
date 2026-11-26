<?php
    namespace Anonymous01j\Repuestos\Controllers;
    use Anonymous01j\Repuestos\Models\Productos;
    use Anonymous01j\Repuestos\Models\Categorias;
    use Anonymous01j\Repuestos\Models\Ingresos;
    use Anonymous01j\Repuestos\Models\Detalles_Ingreso;
    use Anonymous01j\Repuestos\Models\Ventas;
    use Anonymous01j\Repuestos\Models\Detalles_Venta;
    use Anonymous01j\Repuestos\Models\Proveedores;

    class CProductos {
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
            $title = 'Productos';
            $producto = new Productos();
            $categoria = new Categorias();
            $ingreso = new Ingresos();
            $detalleIngreso = new Detalles_Ingreso();
            $venta = new Ventas();
            $detalleVenta = new Detalles_Venta();
            $proveedor = new Proveedores();
            $proveedores = $proveedor->search();
            $ingresos = $ingreso->search();
            $detallesIngresos = $detalleIngreso->search();
            $ventas = $venta->search();
            $categorias = $categoria->search();
            $productos = $producto->search();
            require_once __DIR__ . '/../Views/productos.php';
        }

        /**
         * Endpoint AJAX: verifica si hay stock suficiente para uno o varios productos.
         * POST: items = [{"id": 1, "cantidad": 5}, ...]
         * Responde JSON: { ok: true } o { ok: false, errores: [{nombre, disponible, solicitado}, ...] }
         */
        public function checkStock() {
            header('Content-Type: application/json');

            $items = [];
            if (isset($_POST['items'])) {
                $items = json_decode($_POST['items'], true) ?? [];
            }

            if (empty($items)) {
                echo json_encode(['ok' => false, 'errores' => [], 'message' => 'Sin datos']);
                exit;
            }

            $producto = new Productos();
            $errores = [];

            foreach ($items as $item) {
                $id       = intval($item['id'] ?? 0);
                $cantidad = floatval($item['cantidad'] ?? 0);

                if ($id <= 0 || $cantidad <= 0) continue;

                $producto->setId($id);
                $data = $producto->searchId();

                if (!$data) continue;

                if ($data['stock'] < $cantidad) {
                    $errores[] = [
                        'id'          => $id,
                        'nombre'      => $data['nombre'],
                        'disponible'  => (int) $data['stock'],
                        'solicitado'  => (int) $cantidad,
                    ];
                }
            }

            if (empty($errores)) {
                echo json_encode(['ok' => true]);
            } else {
                echo json_encode(['ok' => false, 'errores' => $errores]);
            }
            exit;
        }

        public function add() {
            if (
            isset($_POST['id_categoria']) &&
            isset($_POST['nombre']) &&
            isset($_POST['precio']) &&
            isset($_POST['stock_minimo']) &&
            isset($_POST['stock_maximo'])
            ) {
            $imagenNombre = null;
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $nombreProducto = str_replace(' ', '_', $_POST['nombre']);
                $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $imagenNombre = $nombreProducto . '.' . $extension;
                $rutaDestino = __DIR__ . '/../../public/assets/img/products/' . $imagenNombre;
                move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino);
            }

            $producto = new Productos();
            $precioAlMayor = (isset($_POST['precio_al_mayor']) && $_POST['precio_al_mayor'] !== '') ? $_POST['precio_al_mayor'] : null;
            $cantidadAlMayor = (isset($_POST['cantidad_al_mayor']) && $_POST['cantidad_al_mayor'] !== '') ? $_POST['cantidad_al_mayor'] : null;

            $producto->setNombre($_POST['nombre'])
                ->setCodigoProducto($_POST['codigo_producto'] ?? null)
                ->setCategoriaId($_POST['id_categoria'])
                ->setPrecio($_POST['precio'])
                ->setPrecioAlMayor($precioAlMayor)
                ->setStock($_POST['stock'] ?? 0)
                ->setStockMinimo($_POST['stock_minimo'])
                ->setStockMaximo($_POST['stock_maximo'])
                ->setCantidadAlMayor($cantidadAlMayor)
                ->setMarca($_POST['marca'] ?? null)
                ->setDescripcion($_POST['descripcion'] ?? null)
                ->setImagen($imagenNombre)
                ->setEstado(1)
                ->setDisponibilidad(isset($_POST['disponibilidad']) ? intval($_POST['disponibilidad']) : 1);
            if ($producto->insert()) {
                header("Location: ?c=CProductos/view&success=1");
            } else {
                header("Location: ?c=CProductos/view&error=1");
            }
            } else {
            header("Location: ?c=CProductos/view&error=2");
            }
        }

        public function edit() {
            if (isset($_POST['id'])) {
            $producto = new Productos();

            $imagenNombre = $_POST['imagen'];
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $nombreProducto = str_replace(' ', '_', $_POST['nombre']);
                $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $imagenNombre = $nombreProducto . '.' . $extension;
                $rutaDestino = __DIR__ . '/../../public/assets/img/products/' . $imagenNombre;
                move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino);
            }

            $precioAlMayor = (isset($_POST['precio_al_mayor']) && $_POST['precio_al_mayor'] !== '') ? $_POST['precio_al_mayor'] : null;
            $cantidadAlMayor = (isset($_POST['cantidad_al_mayor']) && $_POST['cantidad_al_mayor'] !== '') ? $_POST['cantidad_al_mayor'] : null;

            $producto->setId($_POST['id'])
                ->setNombre($_POST['nombre'])
                ->setCodigoProducto($_POST['codigo_producto'] ?? null)
                ->setCategoriaId($_POST['id_categoria'])
                ->setMarca($_POST['marca'] ?? null)
                ->setDescripcion($_POST['descripcion'])
                ->setPrecio($_POST['precio'])
                ->setPrecioAlMayor($precioAlMayor)
                ->setStock($_POST['stock'] ?? 0)
                ->setStockMinimo($_POST['stock_minimo'])
                ->setStockMaximo($_POST['stock_maximo'])
                ->setCantidadAlMayor($cantidadAlMayor)
                ->setImagen($imagenNombre)
                ->setEstado($_POST['estado'])
                ->setDisponibilidad(isset($_POST['disponibilidad']) ? intval($_POST['disponibilidad']) : 1);

            if ($producto->update()) {
                header("Location: ?c=CProductos/view&success=1");
            } else {
                header("Location: ?c=CProductos/view&error=1");
            }
            } else {
            header("Location: ?c=CProductos/view&error=2");
            }
        }

        public function delete() {
            if (isset($_POST['id'])) {
                $producto = new Productos();
                $producto->setId($_POST['id']);
                if ($producto->delete()) {
                    header("Location: ?c=CProductos/view&success=1");
                } else {
                    header("Location: ?c=CProductos/view&error=1");
                }
            } else {
                header("Location: ?c=CProductos/view&error=2");
            }
        }

        public function active() {
            if (isset($_POST['id'])) {
                $producto = new Productos();
                $producto->setId($_POST['id']);
                if ($producto->active()) {
                    header("Location: ?c=CProductos/view&success=1");
                } else {
                    header("Location: ?c=CProductos/view&error=1");
                }
            } else {
                header("Location: ?c=CProductos/view&error=2");
            }
        }
    }