<?php
    namespace Anonymous01j\Repuestos\Models;
    use Anonymous01j\Repuestos\Models\Conexion;

    class Productos extends Conexion {
        private $id;
        private $id_categoria;
        private $codigo_producto;
        private $nombre;
        private $marca;
        private $descripcion;
        private $precio;
        private $precio_al_mayor;
        private $stock;
        private $stock_minimo;
        private $stock_maximo;
        private $cantidad_al_mayor;
        private $imagen;
        private $estado;
        private $disponibilidad;

        public function __construct($id = null, $id_categoria = null, $codigo_producto = null, $nombre = null, $marca = null, $descripcion = null, $precio = null, $precio_al_mayor = null, $stock = null, $stock_minimo = null, $stock_maximo = null, $cantidad_al_mayor = null, $imagen = null, $estado = null, $disponibilidad = null) {
            parent::__construct();
            $this->id = $id;
            $this->id_categoria = $id_categoria;
            $this->codigo_producto = $codigo_producto;
            $this->nombre = $nombre;
            $this->marca = $marca;
            $this->descripcion = $descripcion;
            $this->precio = $precio;
            $this->precio_al_mayor = $precio_al_mayor;
            $this->stock = $stock;
            $this->stock_minimo = $stock_minimo;
            $this->stock_maximo = $stock_maximo;
            $this->cantidad_al_mayor = $cantidad_al_mayor;
            $this->imagen = $imagen;
            $this->estado = $estado;
            $this->disponibilidad = $disponibilidad;
        }
        public function getId() { return $this->id; }
        public function getCategoriaId() { return $this->id_categoria; }
        public function getCodigoProducto() { return $this->codigo_producto; }
        public function getNombre() { return $this->nombre; }
        public function getMarca() { return $this->marca; }
        public function getDescripcion() { return $this->descripcion; }
        public function getPrecio() { return $this->precio; }
        public function getPrecioAlMayor() { return $this->precio_al_mayor; }   
        public function getStock() { return $this->stock; }
        public function getStockMinimo() { return $this->stock_minimo; }
        public function getStockMaximo() { return $this->stock_maximo; }
        public function getCantidadAlMayor() { return $this->cantidad_al_mayor; }
        public function getImagen() { return $this->imagen; }
        public function getEstado() { return $this->estado; }
        public function getDisponibilidad() { return $this->disponibilidad; }
        public function setId($id) { $this->id = $id; return $this; }
        public function setCategoriaId($id_categoria) { $this->id_categoria = $id_categoria; return $this; }
        public function setCodigoProducto($codigo_producto) { $this->codigo_producto = $codigo_producto; return $this; }
        public function setNombre($nombre) { $this->nombre = $nombre; return $this; }
        public function setMarca($marca) { $this->marca = $marca; return $this; }
        public function setDescripcion($descripcion) { $this->descripcion = $descripcion; return $this; }
        public function setPrecio($precio) { $this->precio = $precio; return $this; }
        public function setPrecioAlMayor($precio_al_mayor) { $this->precio_al_mayor = $precio_al_mayor; return $this; }
        public function setStock($stock) { $this->stock = $stock; return $this; }
        public function setStockMinimo($stock_minimo) { $this->stock_minimo = $stock_minimo; return $this; }
        public function setStockMaximo($stock_maximo) { $this->stock_maximo = $stock_maximo; return $this; }
        public function setCantidadAlMayor($cantidad_al_mayor) { $this->cantidad_al_mayor = $cantidad_al_mayor; return $this; }
        public function setImagen($imagen) { $this->imagen = $imagen; return $this; }
        public function setEstado($estado) { $this->estado = $estado; return $this; }
        public function setDisponibilidad($disponibilidad) { $this->disponibilidad = $disponibilidad; return $this; }
        public function insert() {
            $sql = "INSERT INTO productos (id_categoria, codigo_producto, nombre, marca, descripcion, precio, precio_al_mayor, stock, stock_minimo, stock_maximo, cantidad_al_mayor, imagen, estado, disponibilidad) VALUES (:id_categoria, :codigo_producto, :nombre, :marca, :descripcion, :precio, :precio_al_mayor, :stock, :stock_minimo, :stock_maximo, :cantidad_al_mayor, :imagen, :estado, :disponibilidad)";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id_categoria", $this->id_categoria);
            $stmt->bindParam(":codigo_producto", $this->codigo_producto);
            $stmt->bindParam(":nombre", $this->nombre);
            $stmt->bindParam(":marca", $this->marca);
            $stmt->bindParam(":descripcion", $this->descripcion);
            $stmt->bindParam(":precio", $this->precio);
            $stmt->bindParam(":precio_al_mayor", $this->precio_al_mayor);
            $stmt->bindParam(":stock", $this->stock);
            $stmt->bindParam(":stock_minimo", $this->stock_minimo);
            $stmt->bindParam(":stock_maximo", $this->stock_maximo);
            $stmt->bindParam(":cantidad_al_mayor", $this->cantidad_al_mayor);
            $stmt->bindParam(":imagen", $this->imagen);
            $stmt->bindParam(":estado", $this->estado);
            $stmt->bindParam(":disponibilidad", $this->disponibilidad);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
        public function search() {
            $sql = "SELECT *, (SELECT nombre FROM categorias WHERE id = id_categoria) AS categoria FROM productos";
            $stmt = $this->prepare($sql);
            if ($stmt->execute()) {
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }
        public function searchId() {
            $sql = "SELECT * FROM productos WHERE id = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id", $this->id);
            if ($stmt->execute()) {
                return $stmt->fetch(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }
        public function searchCategoriaId($id_categoria) {
            $sql = "SELECT * FROM productos WHERE id_categoria = :id_categoria";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id_categoria", $id_categoria);
            if ($stmt->execute()) {
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }
        public function searchNombre($nombre) {
            $sql = "SELECT * FROM productos WHERE nombre LIKE :nombre";
            $stmt = $this->prepare($sql);
            $nombre = "%$nombre%";
            $stmt->bindParam(":nombre", $nombre);
            if ($stmt->execute()) {
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                return [];
            }
        }
        public function update() {
            $sql = "UPDATE productos SET id_categoria = :id_categoria, codigo_producto = :codigo_producto, nombre = :nombre, marca = :marca, descripcion = :descripcion, precio = :precio, precio_al_mayor = :precio_al_mayor, stock = :stock, stock_minimo = :stock_minimo, stock_maximo = :stock_maximo, cantidad_al_mayor = :cantidad_al_mayor, imagen = :imagen, estado = :estado, disponibilidad = :disponibilidad WHERE id = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id", $this->id);
            $stmt->bindParam(":id_categoria", $this->id_categoria);
            $stmt->bindParam(":codigo_producto", $this->codigo_producto);
            $stmt->bindParam(":nombre", $this->nombre);
            $stmt->bindParam(":marca", $this->marca);
            $stmt->bindParam(":descripcion", $this->descripcion);
            $stmt->bindParam(":precio", $this->precio);
            $stmt->bindParam(":precio_al_mayor", $this->precio_al_mayor);
            $stmt->bindParam(":stock", $this->stock);
            $stmt->bindParam(":stock_minimo", $this->stock_minimo);
            $stmt->bindParam(":stock_maximo", $this->stock_maximo);
            $stmt->bindParam(":cantidad_al_mayor", $this->cantidad_al_mayor);
            $stmt->bindParam(":imagen", $this->imagen);
            $stmt->bindParam(":estado", $this->estado);
            $stmt->bindParam(":disponibilidad", $this->disponibilidad);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
        public function updateStock() {
            $sql = "UPDATE productos SET stock = :stock WHERE id = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id", $this->id);
            $stmt->bindParam(":stock", $this->stock);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
        public function decreaseStock($quantity, ?\PDO $connection = null) {
            $sql = "UPDATE productos SET stock = stock - :quantity WHERE id = :id AND stock >= :minimum_stock";
            $connection = $connection ?? $this;
            $stmt = $connection->prepare($sql);
            $stmt->bindParam(":id", $this->id);
            $stmt->bindParam(":quantity", $quantity);
            $stmt->bindParam(":minimum_stock", $quantity);
            return $stmt->execute() && $stmt->rowCount() === 1;
        }
        public function delete() {
            $sql = "UPDATE productos SET estado = 0 WHERE id = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id", $this->id);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
        public function active() {
            $sql = "UPDATE productos SET estado = 1 WHERE id = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(":id", $this->id);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
    }
