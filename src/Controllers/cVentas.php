<?php
namespace Anonymous01j\Repuestos\Controllers;
use Anonymous01j\Repuestos\Models\Ventas;
use Anonymous01j\Repuestos\Models\Detalles_Venta;
use Anonymous01j\Repuestos\Models\Productos;

class CVentas {

    /**
     * Normaliza un valor monetario a float
     */
    private function normalizarMoneda($valor): float {
        if ($valor === null || $valor === '') {
            return 0;
        }
        
        // Si ya es numérico, devolverlo
        if (is_numeric($valor)) {
            return floatval($valor);
        }
        
        $valor = trim(strval($valor));
        
        // Formato venezolano: 1.234,56
        if (preg_match('/,\d{1,2}$/', $valor)) {
            $valor = str_replace('.', '', $valor);  // Quitar puntos de miles
            $valor = str_replace(',', '.', $valor); // Cambiar coma decimal a punto
        } else {
            // Formato internacional: 1,234.56
            $valor = str_replace(',', '', $valor);  // Quitar comas de miles
        }
        
        return floatval($valor);
    }

    /**
     * Verifica stock para un array de productos
     */
    private function verificarStock(array $items): array {
        $producto = new Productos();
        $errores  = [];

        foreach ($items as $item) {
            $id       = intval($item['id_producto']);
            $cantidad = floatval($item['cantidad']);

            $producto->setId($id);
            $data = $producto->searchId();

            if (!$data) {
                $errores[] = "Producto con ID {$id} no encontrado.";
                continue;
            }

            if ($data['stock'] < $cantidad) {
                $errores[] = sprintf(
                    "Stock insuficiente para <strong>%s</strong>: disponible <strong>%s</strong>, solicitado <strong>%s</strong>.",
                    $data['nombre'],
                    number_format($data['stock'], 2, ',', '.'),
                    number_format($cantidad, 2, ',', '.')
                );
            }
        }

        return $errores;
    }

    public function searchDetails() {
        if (isset($_POST['id_venta'])) {
            $detallesVenta = new Detalles_Venta();
            $detallesVenta->setIdVenta($_POST['id_venta']);
            $result = $detallesVenta->searchIdVenta();
            echo json_encode($result ?: []);
        } else {
            echo json_encode(['error' => 'ID de venta no proporcionado']);
        }
    }

    public function addMany() {
        if (
            isset($_POST['id_usuario']) &&
            isset($_POST['productos'])  && is_array($_POST['productos']) &&
            isset($_POST['cantidades']) && is_array($_POST['cantidades']) &&
            isset($_POST['precio'])     && is_array($_POST['precio']) &&
            isset($_POST['precio_bs'])  && is_array($_POST['precio_bs'])
        ) {
            $productos  = $_POST['productos'];
            $cantidades = $_POST['cantidades'];
            $precios    = $_POST['precio'];
            $precios_bs = $_POST['precio_bs'];

            // ── Validación de stock ──────────────────────────────
            $items = [];
            foreach ($productos as $i => $id_producto) {
                if (empty($id_producto)) continue;
                
                $items[] = [
                    'id_producto' => $id_producto,
                    'cantidad'    => floatval($cantidades[$i]),
                ];
            }

            $errores = $this->verificarStock($items);
            if (!empty($errores)) {
                $msg = implode('<br>', $errores);
                
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['ok' => false, 'message' => $msg]);
                    exit;
                }
                
                $_SESSION['stock_error'] = $msg;
                header("Location: ?c=CProductos/view&stock_error=1");
                exit;
            }
            // ────────────────────────────────────────────────────

            // Calcular totales
            $total    = 0;
            $total_bs = 0;
            foreach ($productos as $i => $id_producto) {
                if (empty($id_producto)) continue;
                
                $cantidad   = floatval($cantidades[$i]);
                $precio     = $this->normalizarMoneda($precios[$i]);
                $precio_bs  = $this->normalizarMoneda($precios_bs[$i]);
                
                $total    += round($cantidad * $precio, 2);
                $total_bs += round($cantidad * $precio_bs, 2);
            }

            $venta = new Ventas();
            $venta->setUsuarioId($_POST['id_usuario'])
                  ->setTotal($total)
                  ->setTotalBs($total_bs);

            try {
                $venta->beginTransaction();

                if (!$venta->insert()) {
                    throw new \Exception("Error al insertar venta.");
                }

                $id_venta = $venta->getLastID();

                foreach ($productos as $i => $id_producto) {
                    if (empty($id_producto)) continue;
                    
                    $cantidad  = floatval($cantidades[$i]);
                    $precio    = $this->normalizarMoneda($precios[$i]);
                    $precio_bs = $this->normalizarMoneda($precios_bs[$i]);

                    $detallesVenta = new Detalles_Venta();
                    $detallesVenta->setIdVenta($id_venta)
                        ->setIdProducto($id_producto)
                        ->setCantidad($cantidad)
                        ->setPrecioUnitario($precio)
                        ->setPrecioUnitarioBs($precio_bs)
                        ->setSubtotal(round($precio * $cantidad, 2))
                        ->setSubtotalBs(round($precio_bs * $cantidad, 2));

                    if (!$detallesVenta->insert()) {
                        throw new \Exception("Error al insertar detalle de venta.");
                    }
                }

                $venta->commit();
                
                header("Location: ?c=CProductos/view&success=1");
                exit;
            } catch (\Throwable $e) {
                if ($venta->inTransaction()) {
                    $venta->rollBack();
                }
                header("Location: ?c=CProductos/view&error=1");
                exit;
            }

        } else {
            header("Location: ?c=CProductos/view&error=2");
            exit;
        }
    }

    public function add() {
        if (
            isset($_POST['id_producto']) &&
            isset($_POST['cantidad'])    &&
            isset($_POST['precio'])      &&
            isset($_POST['precio_bs'])
        ) {
            $cantidad = floatval($_POST['cantidad']);
            
            // ── Validación de stock ──────────────────────────────
            $errores = $this->verificarStock([[
                'id_producto' => $_POST['id_producto'],
                'cantidad'    => $cantidad,
            ]]);

            if (!empty($errores)) {
                $_SESSION['stock_error'] = implode('<br>', $errores);
                header("Location: ?c=CProductos/view&stock_error=1");
                exit;
            }
            // ────────────────────────────────────────────────────

            $precio    = $this->normalizarMoneda($_POST['precio']);
            $precio_bs = $this->normalizarMoneda($_POST['precio_bs']);

            $venta = new Ventas();
            $venta->setUsuarioId($_POST['id_usuario'])
                  ->setTotal(round($precio * $cantidad, 2))
                  ->setTotalBs(round($precio_bs * $cantidad, 2));

            try {
                $venta->beginTransaction();

                if (!$venta->insert()) {
                    throw new \Exception("Error al insertar venta.");
                }

                $id_venta = $venta->getLastID();

                $detallesVenta = new Detalles_Venta();
                $detallesVenta->setIdVenta($id_venta)
                    ->setIdProducto($_POST['id_producto'])
                    ->setCantidad($cantidad)
                    ->setPrecioUnitario($precio)
                    ->setPrecioUnitarioBs($precio_bs)
                    ->setSubtotal(round($precio * $cantidad, 2))
                    ->setSubtotalBs(round($precio_bs * $cantidad, 2));

                if (!$detallesVenta->insert()) {
                    throw new \Exception("Error al insertar detalle de venta.");
                }

                $venta->commit();
                header("Location: ?c=CProductos/view&success=1");
                exit;
            } catch (\Throwable $e) {
                if ($venta->inTransaction()) {
                    $venta->rollBack();
                }
                header("Location: ?c=CProductos/view&error=1");
                exit;
            }
        } else {
            echo "Error: Faltan campos obligatorios.";
        }
    }
}