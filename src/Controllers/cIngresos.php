<?php
    namespace Anonymous01j\Repuestos\Controllers;
    use Anonymous01j\Repuestos\Models\Ingresos;
    use Anonymous01j\Repuestos\Models\Detalles_Ingreso;
    use Anonymous01j\Repuestos\Models\Productos;
    
    class CIngresos {

        public function add() {
            if (isset($_POST['id_usuario'])) {
                function normalizarMoneda($valor) {
                    return floatval(str_replace(',', '.', str_replace('.', '', $valor)));
                }
                $ingreso = new Ingresos();
                $ingreso->setIdUsuario($_POST['id_usuario'])
                        ->setIdProveedor($_POST['id_proveedor'] ?? null)
                        ->setTotal(normalizarMoneda($_POST['precio']))
                        ->setTotalBs(normalizarMoneda($_POST['precio_bs']));
                if ($ingreso->insert() && isset($_POST['id_producto']) && isset($_POST['cantidad']) && isset($_POST['precio']) && isset($_POST['precio_bs'])) {
                    $detallesIngreso = new Detalles_Ingreso();
                    $detallesIngreso->setIdIngreso($ingreso->getLastID())
                        ->setIdProducto($_POST['id_producto'])
                        ->setCantidad($_POST['cantidad'])
                        ->setPrecio_compra($_POST['precio'])
                        ->setPrecio_compra_bs(normalizarMoneda($_POST['precio_bs']))
                        ->setSubtotal(normalizarMoneda($_POST['precio']))
                        ->setSubtotal_bs(normalizarMoneda($_POST['precio_bs']));
                    if ($detallesIngreso->insert()) {
                        header("Location: ?c=CProductos/view&success=1");
                    } else {
                        header("Location: ?c=CProductos/view&error=1");
                    }
                } else {
                    header(header: "Location: ?c=CProductos/view&error=2");
                }
            } else {
                // header("Location: ?c=CProductos/view&error=3");
                echo "Error: Faltan campos obligatorios.";
            }
        }

        public function addMany(){
            if (
                isset($_POST['id_usuario']) &&
                isset($_POST['productos']) &&
                isset($_POST['cantidades']) &&
                isset($_POST['precio']) &&
                isset($_POST['precio_bs'])
            ) {
                function normalizarMoneda($valor)
                {
                    return floatval(str_replace(',', '.', str_replace('.', '', $valor)));
                }

                $productos = $_POST['productos'];
                $cantidades = $_POST['cantidades'];
                $precios = $_POST['precio'];
                $precios_bs = $_POST['precio_bs'];

                $total = 0;
                $totalBs = 0;

                // Calcular totales antes de insertar el ingreso
                for ($i = 0; $i < count($productos); $i++) {
                    $cantidad = intval($cantidades[$i]);
                    $precio = normalizarMoneda($precios[$i]);
                    $precio_bs = normalizarMoneda($precios_bs[$i]);
                    $subtotal = $cantidad * $precio;
                    $subtotal_bs = $cantidad * $precio_bs;
                    $total += $subtotal;
                    $totalBs += $subtotal_bs;
                }

                $ingreso = new Ingresos();
                $ingreso->setIdUsuario($_POST['id_usuario'])
                        ->setIdProveedor($_POST['id_proveedor'] ?? null)
                        ->setTotal($total)
                        ->setTotalBs($totalBs);

                if ($ingreso->insert()) {
                    $idIngreso = $ingreso->getLastID();

                    for ($i = 0; $i < count($productos); $i++) {
                        $cantidad = intval($cantidades[$i]);
                        $precio = normalizarMoneda($precios[$i]);
                        $precio_bs = normalizarMoneda($precios_bs[$i]);
                        $subtotal = $cantidad * $precio;
                        $subtotal_bs = $cantidad * $precio_bs;

                        $detallesIngreso = new Detalles_Ingreso();
                        $detallesIngreso->setIdIngreso($idIngreso)
                                        ->setIdProducto($productos[$i])
                                        ->setCantidad($cantidad)
                                        ->setPrecio_compra($precio)
                                        ->setPrecio_compra_bs($precio_bs)
                                        ->setSubtotal($subtotal)
                                        ->setSubtotal_bs($subtotal_bs);

                        $detallesIngreso->insert();
                    }

                    header("Location: ?c=CProductos&success=1");
                    exit;
                } else {
                    header("Location: ?c=CProductos&error=1");
                    exit;
                }
            } else {
                echo "Error: Faltan campos obligatorios.";
            }
        }

        public function searchDetails() {
            if (isset($_POST['id_ingreso'])) {
                $detallesIngreso = new Detalles_Ingreso();
                $detallesIngreso->setIdIngreso($_POST['id_ingreso']);
                $result = $detallesIngreso->searchIngresoId();
                if ($result) {
                    echo json_encode($result);
                } else {
                    echo json_encode([]);
                }
            } else {
                echo json_encode(['error' => 'ID de ingreso no proporcionado']);
            }
        }
    }