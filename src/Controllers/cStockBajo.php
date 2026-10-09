<?php
namespace Anonymous01j\Repuestos\Controllers;

use Anonymous01j\Repuestos\Models\StockBajo;

class CStockBajo {
    public function view() {
        if (!isset($_SESSION['id'])) {
            if (isset($_COOKIE['id'])) {
                $_SESSION['id'] = $_COOKIE['id'];
                $_SESSION['nombre'] = $_COOKIE['nombre'] ?? '';
                $_SESSION['rol'] = $_COOKIE['rol'] ?? '';
            } else {
                require_once __DIR__ . '/../Views/login.php';
                exit();
            }
        }

        $title = 'Alertas de inventario';
        $stockBajoModel = new StockBajo();
        $productosAlerta = $stockBajoModel->search();

        require_once __DIR__ . '/../Views/stockBajo.php';
    }
}