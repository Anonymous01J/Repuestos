<?php
namespace Anonymous01j\Repuestos\Controllers;
use Anonymous01j\Repuestos\Models\Ventas;
use Anonymous01j\Repuestos\Models\Detalles_Venta;
use Anonymous01j\Repuestos\Models\Productos;

class CVentas {
    public function searchDetails() {
        if (isset($_POST['id_venta'])) {
            $detallesVenta = new Detalles_Venta();
            $detallesVenta->setIdVenta($_POST['id_venta']);
            $result = $detallesVenta->searchIdVenta();
            if ($result) {
                echo json_encode($result);
            } else {
                echo json_encode([]);
            }
        } else {
            echo json_encode(['error' => 'ID de venta no proporcionado']);
        }
    }

    // Método para registrar venta rápida (varios productos)
    public function addMany() {
        if (
            isset($_POST['id_usuario']) &&
            isset($_POST['productos']) && is_array($_POST['productos']) &&
            isset($_POST['cantidades']) && is_array($_POST['cantidades']) &&
            isset($_POST['precio']) && is_array($_POST['precio']) &&
            isset($_POST['precio_bs']) && is_array($_POST['precio_bs'])
        ) {
            function normalizarMoneda($valor) {
                return floatval(str_replace(',', '.', str_replace('.', '', $valor)));
            }

            $productos = $_POST['productos'];
            $cantidades = $_POST['cantidades'];
            $precios = $_POST['precio'];
            $precios_bs = $_POST['precio_bs'];

            $total = 0;
            $total_bs = 0;
            foreach ($productos as $i => $id_producto) {
                $cantidad = floatval($cantidades[$i]);
                $precio = normalizarMoneda($precios[$i]);
                $precio_bs = normalizarMoneda($precios_bs[$i]);
                $total += $cantidad * $precio;
                $total_bs += $cantidad * $precio_bs;
            }

            $venta = new Ventas();
            $venta->setUsuarioId($_POST['id_usuario'])
                  ->setTotal($total)
                  ->setTotalBs($total_bs);

            if ($venta->insert()) {
                $id_venta = $venta->getLastID();
                foreach ($productos as $i => $id_producto) {
                    $cantidad = floatval($cantidades[$i]);
                    $precio = normalizarMoneda($precios[$i]);
                    $precio_bs = normalizarMoneda($precios_bs[$i]);

                    $detallesVenta = new Detalles_Venta();
                    $detallesVenta->setIdVenta($id_venta)
                        ->setIdProducto($id_producto)
                        ->setCantidad($cantidad)
                        ->setPrecioUnitario($precio)
                        ->setPrecioUnitarioBs($precio_bs)
                        ->setSubtotal($precio * $cantidad)
                        ->setSubtotalBs($precio_bs * $cantidad);
                    $detallesVenta->insert();

                    // Actualizar stock
                    $producto = new Productos();
                    $producto->setId($id_producto);
                    $productoData = $producto->searchId();
                    if ($productoData) {
                        $nuevoStock = $productoData['stock'] - $cantidad;
                        if ($nuevoStock < 0) $nuevoStock = 0;
                        $producto->setStock($nuevoStock);
                        $producto->updateStock();
                    }
                }
                header("Location: ?c=CProductos/view&success=1");
                exit;
            } else {
                header("Location: ?c=CProductos/view&error=1");
                exit;
            }
        } else {
            header("Location: ?c=CProductos/view&error=2");
            exit;
        }
    }

    // Método para registrar venta simple (un solo producto)
    public function add() {
        if (
            isset($_POST['id_producto']) &&
            isset($_POST['cantidad']) &&
            isset($_POST['precio']) &&
            isset($_POST['precio_bs'])
        ) {
            function normalizarMoneda($valor) {
                return floatval(str_replace(',', '.', str_replace('.', '', $valor)));
            }
            $venta = new Ventas();
            $venta->setUsuarioId($_POST['id_usuario'])
                  ->setTotal(normalizarMoneda($_POST['precio']) * $_POST['cantidad'])
                  ->setTotalBs(normalizarMoneda($_POST['precio_bs']) * $_POST['cantidad']);
            if ($venta->insert()) {
                $id_venta = $venta->getLastID();
                $detallesVenta = new Detalles_Venta();
                $detallesVenta->setIdVenta($id_venta)
                    ->setIdProducto($_POST['id_producto'])
                    ->setCantidad($_POST['cantidad'])
                    ->setPrecioUnitario(normalizarMoneda($_POST['precio']))
                    ->setPrecioUnitarioBs(normalizarMoneda($_POST['precio_bs']))
                    ->setSubtotal(normalizarMoneda($_POST['precio']) * $_POST['cantidad'])
                    ->setSubtotalBs(normalizarMoneda($_POST['precio_bs']) * $_POST['cantidad']);
                if ($detallesVenta->insert()) {
                    // Actualizar el stock del producto
                    $producto = new Productos();
                    $producto->setId($_POST['id_producto']);
                    $productoData = $producto->searchId();
                    if ($productoData) {
                        $nuevoStock = $productoData['stock'] - $_POST['cantidad'];
                        if ($nuevoStock < 0) $nuevoStock = 0;
                        $producto->setStock($nuevoStock);
                        $producto->updateStock();
                    }
                    header("Location: ?c=CProductos/view&success=1");
                } else {
                    header("Location: ?c=CProductos/view&error=1");
                }
            } else {
                header("Location: ?c=CProductos/view&error=2");
            }
        } else {
            echo "Error: Faltan campos obligatorios.";
        }
    }
}