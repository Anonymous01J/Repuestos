<?php
namespace Anonymous01j\Repuestos;
	if (session_status() === PHP_SESSION_NONE) {
    session_start();
	}	
	require 'vendor/autoload.php';

	$ruta = isset($_GET['c']) ? $_GET['c'] : "cHome/view";
	$partes = explode("/", $ruta);
	// Normalizar el nombre del archivo del controlador: si viene "CHome", buscar "cHome" o "CHome"
	$controladorArchivo = lcfirst($partes[0]);
	$nomClase = "Anonymous01j\\Repuestos\\Controllers\\" . ucfirst($controladorArchivo);
	$metodo = isset($partes[1]) ? $partes[1] : "view";
	$url = __DIR__ . "/src/Controllers/" . $controladorArchivo . ".php";
	if (!file_exists($url)) {
		// Fallback por si el archivo físico estuviese con primera mayúscula
		$url = __DIR__ . "/src/Controllers/" . ucfirst($partes[0]) . ".php";
	}
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