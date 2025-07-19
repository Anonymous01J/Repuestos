<?php
    namespace Anonymous01j\Repuestos\Controllers;
    use Anonymous01j\Repuestos\Models\Productos;
    use Anonymous01j\Repuestos\Models\Categorias;
    use Anonymous01j\Repuestos\Models\Ingresos;
    use Anonymous01j\Repuestos\Models\Detalles_Ingreso;
    use Anonymous01j\Repuestos\Models\Proveedores;
    class CProductos {
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
            $title = 'Productos';
            $producto = new Productos();
            $categoria = new Categorias();
            $ingreso = new Ingresos();
            $detalleIngreso = new Detalles_Ingreso();
            $proveedor = new Proveedores();
            $proveedores = $proveedor->search();
            $ingresos = $ingreso->search();
            $detallesIngresos = $detalleIngreso->search();
            $categorias = $categoria->search();
            $productos = $producto->search();
            $productosJSON = json_encode($productos);
            require_once __DIR__ . '/../Views/Productos.php';
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
            $producto->setNombre($_POST['nombre'])
                ->setStockMinimo($_POST['stock_minimo'])
                ->setStockMaximo($_POST['stock_maximo'])
                ->setCategoriaId($_POST['id_categoria'])
                ->setPrecio($_POST['precio']);
            $producto->setMarca($_POST['marca'] ?? null)
                    ->setDescripcion($_POST['descripcion'] ?? null)
                    ->setImagen($imagenNombre);
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

            // Procesar imagen si se subió una nueva
            $imagenNombre = $_POST['imagen']; // Valor por defecto: la imagen anterior
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $nombreProducto = str_replace(' ', '_', $_POST['nombre']);
                $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $imagenNombre = $nombreProducto . '.' . $extension;
                $rutaDestino = __DIR__ . '/../../public/assets/img/products/' . $imagenNombre;
                move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino);
            }

            $producto->setId($_POST['id'])
                ->setNombre($_POST['nombre'])
                ->setStockMinimo($_POST['stock_minimo'])
                ->setStockMaximo($_POST['stock_maximo'])
                ->setCategoriaId($_POST['id_categoria'])
                ->setDescripcion($_POST['descripcion'])
                ->setPrecio($_POST['precio'])
                ->setMarca($_POST['marca'])
                ->setImagen($imagenNombre)
                ->setEstado($_POST['estado']);

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