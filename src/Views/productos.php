<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos - Sistema de Repuestos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & DataTables -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/assets/css/style.css">
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 py-2">
        <div class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between">
            <div class="d-flex align-items-center mb-2 mb-md-0">
                <img class="img-bcv me-2" src="./public/assets/img/bcv2.png" alt="BCV" width="40">
                <span class="text-white fw-semibold me-2">Dólar BCV:</span>
                <span class="text-info fw-bold" id="Dolar_bcv">0.00 Bs</span>
            </div>
            <div class="d-flex align-items-center">
                <a class="navbar-brand me-4" href="#">RepuestosApp</a>
                <span class="text-white me-3">Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?></span>
                <a href="?c=CHome/logout" class="btn btn-sm btn-outline-light">Cerrar sesión</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-4">Productos</h2>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalNuevo">+ Nuevo Producto</button>

        <table class="table table-striped" id="tablaProductos">
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Marca</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Stock Min</th>
                    <th>Stock Max</th>
                    <th>Categoría</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $p): ?>
                    <tr>
                        <td>
                            <img src="public/assets/img/products/<?= htmlspecialchars($p['imagen']) ?>" 
                                alt="img" class="img-thumbnail" width="50" style="cursor:pointer"
                                data-bs-toggle="modal" data-bs-target="#modalImagen" 
                                onclick="verImagen('<?= htmlspecialchars($p['imagen']) ?>')">
                        </td>
                        <td><?= htmlspecialchars($p['nombre']) ?></td>
                        <td><?= htmlspecialchars($p['marca']) ?></td>
                        <td><?= htmlspecialchars($p['precio']) ?> Bs</td>
                        <td><?= htmlspecialchars($p['stock']) ?></td>
                        <td><?= htmlspecialchars($p['stock_minimo']) ?></td>
                        <td><?= htmlspecialchars($p['stock_maximo']) ?></td>
                        <td><?= htmlspecialchars($p['categoria']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" 
                                    onclick="editarProducto(<?= htmlspecialchars(json_encode($p)) ?>)">
                                Editar
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Nueva Imagen -->
    <div class="modal fade" id="modalImagen" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <img id="imgGrande" src="" alt="Imagen producto" class="img-fluid rounded">
            </div>
        </div>
    </div>

    <!-- Modal Nuevo Producto -->
    <div class="modal fade" id="modalNuevo" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="?c=CProductos/add" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Nuevo Producto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <div class="col-md-6">
                            <label>Nombre</label>
                            <input name="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Marca</label>
                            <input name="marca" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>Precio</label>
                            <input type="number" step="0.01" name="precio" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Stock Mínimo</label>
                            <input type="number" name="stock_minimo" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Stock Máximo</label>
                            <input type="number" name="stock_maximo" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Categoría</label>
                            <select name="id_categoria" class="form-select" required>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Imagen</label>
                            <input type="file" name="imagen" class="form-control" onchange="previewImagen(this, 'previewNuevo')">
                            <img id="previewNuevo" src="" alt="Preview" class="img-thumbnail mt-2" width="100" style="display:none">
                        </div>
                        <div class="col-12">
                            <label>Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Guardar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Producto -->
    <div class="modal fade" id="modalEditar" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="?c=CProductos/edit" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="editId">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Producto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <!-- igual al anterior pero con campos llenables por JS -->
                        <div class="col-md-6">
                            <label>Nombre</label>
                            <input name="nombre" id="editNombre" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Marca</label>
                            <input name="marca" id="editMarca" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>Precio</label>
                            <input type="number" step="0.01" name="precio" id="editPrecio" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Stock Mínimo</label>
                            <input type="number" name="stock_minimo" id="editStockMinimo" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Stock Máximo</label>
                            <input type="number" name="stock_maximo" id="editStockMaximo" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Categoría</label>
                            <select name="id_categoria" id="editCategoria" class="form-select">
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Imagen</label>
                            <input type="file" name="imagen" class="form-control" onchange="previewImagen(this, 'previewEditar')">
                            <input type="hidden" name="imagen" id="editImagenHidden">
                            <img id="previewEditar" src="" alt="Preview" class="img-thumbnail mt-2" width="100">
                        </div>
                        <div class="col-12">
                            <label>Descripción</label>
                            <textarea name="descripcion" id="editDescripcion" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning">Actualizar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#tablaProductos').DataTable();
        });

        function verImagen(nombre) {
            document.getElementById('imgGrande').src = 'public/assets/img/products/' + nombre;
        }

        function previewImagen(input, idPreview) {
            const file = input.files[0];
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = document.getElementById(idPreview);
                img.src = e.target.result;
                img.style.display = 'block';
            };
            if (file) reader.readAsDataURL(file);
        }

        function editarProducto(data) {
            const modal = new bootstrap.Modal(document.getElementById('modalEditar'));
            document.getElementById('editId').value = data.id;
            document.getElementById('editNombre').value = data.nombre;
            document.getElementById('editMarca').value = data.marca;
            document.getElementById('editPrecio').value = data.precio;
            document.getElementById('editStockMinimo').value = data.stock_minimo;
            document.getElementById('editStockMaximo').value = data.stock_maximo;
            document.getElementById('editDescripcion').value = data.descripcion;
            document.getElementById('editImagenHidden').value = data.imagen;
            document.getElementById('previewEditar').src = 'public/assets/img/products/' + data.imagen;

            document.getElementById('editCategoria').value = data.id_categoria;

            modal.show();
        }
    </script>
</body>
</html>
