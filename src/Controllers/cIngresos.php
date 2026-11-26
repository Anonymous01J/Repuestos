<?php
    namespace Anonymous01j\Repuestos\Controllers;
    use Anonymous01j\Repuestos\Models\Ingresos;
    use Anonymous01j\Repuestos\Models\Detalles_Ingreso;
    use Anonymous01j\Repuestos\Models\Productos;
    
    class CIngresos {

        private function normalizarMoneda($valor): float {
            if ($valor === null || $valor === '') {
                return 0;
            }
            if (is_numeric($valor)) {
                return floatval($valor);
            }
            $valor = trim(strval($valor));
            if (preg_match('/,\d{1,2}$/', $valor)) {
                $valor = str_replace('.', '', $valor);
                $valor = str_replace(',', '.', $valor);
            } else {
                $valor = str_replace(',', '', $valor);
            }
            return floatval($valor);
        }

        public function add() {
            if (isset($_POST['id_usuario']) && isset($_POST['id_producto']) && isset($_POST['cantidad']) && isset($_POST['precio']) && isset($_POST['precio_bs'])) {
                $precio = $this->normalizarMoneda($_POST['precio']);
                $precio_bs = $this->normalizarMoneda($_POST['precio_bs']);
                $cantidad = floatval($_POST['cantidad']);

                $ingreso = new Ingresos();
                $ingreso->setIdUsuario($_POST['id_usuario'])
                        ->setIdProveedor($_POST['id_proveedor'] ?? null)
                        ->setTotal(round($precio * $cantidad, 2))
                        ->setTotalBs(round($precio_bs * $cantidad, 2));

                try {
                    $ingreso->beginTransaction();

                    if (!$ingreso->insert()) {
                        throw new \Exception("Error al registrar ingreso.");
                    }
                    $detallesIngreso = new Detalles_Ingreso();
                    $detallesIngreso->setIdIngreso($ingreso->getLastID())
                        ->setIdProducto($_POST['id_producto'])
                        ->setCantidad($cantidad)
                        ->setPrecio_compra($precio)
                        ->setPrecio_compra_bs($precio_bs)
                        ->setSubtotal(round($precio * $cantidad, 2))
                        ->setSubtotal_bs(round($precio_bs * $cantidad, 2));

                    if (!$detallesIngreso->insert()) {
                        throw new \Exception("Error al registrar detalle de ingreso.");
                    }

                    $ingreso->commit();
                    header("Location: ?c=CProductos/view&success=1");
                    exit;
                } catch (\Throwable $e) {
                    if ($ingreso->inTransaction()) {
                        $ingreso->rollBack();
                    }
                    header("Location: ?c=CProductos/view&error=1");
                    exit;
                }
            } else {
                header("Location: ?c=CProductos/view&error=2");
                exit;
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
                $productos = $_POST['productos'];
                $cantidades = $_POST['cantidades'];
                $precios = $_POST['precio'];
                $precios_bs = $_POST['precio_bs'];

                $total = 0;
                $totalBs = 0;

                // Calcular totales antes de insertar el ingreso
                for ($i = 0; $i < count($productos); $i++) {
                    if (empty($productos[$i])) continue;
                    $cantidad = floatval($cantidades[$i]);
                    $precio = $this->normalizarMoneda($precios[$i]);
                    $precio_bs = $this->normalizarMoneda($precios_bs[$i]);
                    $subtotal = round($cantidad * $precio, 2);
                    $subtotal_bs = round($cantidad * $precio_bs, 2);
                    $total += $subtotal;
                    $totalBs += $subtotal_bs;
                }

                $ingreso = new Ingresos();
                $ingreso->setIdUsuario($_POST['id_usuario'])
                        ->setIdProveedor($_POST['id_proveedor'] ?? null)
                        ->setTotal($total)
                        ->setTotalBs($totalBs);

                try {
                    $ingreso->beginTransaction();

                    if (!$ingreso->insert()) {
                        throw new \Exception("Error al registrar ingreso.");
                    }

                    $idIngreso = $ingreso->getLastID();

                    for ($i = 0; $i < count($productos); $i++) {
                        if (empty($productos[$i])) continue;
                        $cantidad = floatval($cantidades[$i]);
                        $precio = $this->normalizarMoneda($precios[$i]);
                        $precio_bs = $this->normalizarMoneda($precios_bs[$i]);
                        $subtotal = round($cantidad * $precio, 2);
                        $subtotal_bs = round($cantidad * $precio_bs, 2);

                        $detallesIngreso = new Detalles_Ingreso();
                        $detallesIngreso->setIdIngreso($idIngreso)
                                        ->setIdProducto($productos[$i])
                                        ->setCantidad($cantidad)
                                        ->setPrecio_compra($precio)
                                        ->setPrecio_compra_bs($precio_bs)
                                        ->setSubtotal($subtotal)
                                        ->setSubtotal_bs($subtotal_bs);

                        if (!$detallesIngreso->insert()) {
                            throw new \Exception("Error al registrar detalle de ingreso.");
                        }
                    }

                    $ingreso->commit();
                    header("Location: ?c=CProductos/view&success=1");
                    exit;
                } catch (\Throwable $e) {
                    if ($ingreso->inTransaction()) {
                        $ingreso->rollBack();
                    }
                    header("Location: ?c=CProductos/view&error=1");
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