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
                echo json_encode(['error' => 'ID de ingreso no proporcionado']);
            }
        }
    }