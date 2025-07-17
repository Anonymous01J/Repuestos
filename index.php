<?php
namespace Anonymous01j\Repuestos;
	if (session_status() === PHP_SESSION_NONE) {
    session_start();
	}	
	require 'vendor/autoload.php';

	$ruta = isset($_GET['c']) ? $_GET['c'] : "CHome/view";
	$partes = explode("/", $ruta);
	$nomClase = "Anonymous01j\\Repuestos\\Controllers\\" . ucfirst($partes[0]);
	$metodo = isset($partes[1]) ? $partes[1] : "view";
	$url = __DIR__ . "/src/Controllers/" . $partes[0] . ".php";
	if (file_exists($url)) {
		require_once $url;
		if (class_exists($nomClase)) {
			$instancia = new $nomClase();
			if (method_exists($instancia, $metodo)) {
				$instancia->$metodo();
			} else {
				echo "NO EXISTE EL METODO";
			}
		} else {
			echo "NO EXISTE LA CLASE";
		}
	} else {
		echo "NO EXISTE EL CONTROLADOR";
	}
?>