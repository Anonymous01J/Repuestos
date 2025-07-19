<!DOCTYPE html>
<html lang="es">
<?php
include_once 'src/Views/complements/head.php';
include_once 'src/Views/complements/header.php';
?>
    <div class="container mt-4">
        <h2 class="mb-4">Productos</h2>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalNuevo">+ Nuevo Producto</button>
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalEntrada">
            + Registrar Entrada
        </button>
        <div class="container bg-white p-4 rounded shadow-sm">
            <!-- Tabs de Bootstrap -->
            <ul class="nav nav-tabs mb-3" id="clienteTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="productos-tab" data-bs-toggle="tab" data-bs-target="#productos" type="button">Productos</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="entradas-tab" data-bs-toggle="tab" data-bs-target="#entradas" type="button">Entradas</button>
                </li>
            </ul>
            <div class="tab-content">
                <!-- Productos -->
                <div class="tab-pane fade show active" id="productos" role="tabpanel" aria-labelledby="productos-tab">
                    <div class="table-responsive">
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
                                            <button class="btn btn-sm btn-success"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalIngresoRapido_<?= $p['id'] ?>"
                                                    data-idProducto="<?= $p['id'] ?>">
                                                Ingresar
                                            </button>

                                        </td>
                                    </tr>
                                    <!-- Modal Ingreso Rápido -->
                                    <div class="modal fade" id="modalIngresoRapido_<?= $p['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                            <form method="POST" action="?c=CIngresos/add">
                                                <input type="hidden" name="id_producto" value="<?= $p['id'] ?>">
                                                <div class="modal-header">
                                                <h5 class="modal-title">Ingreso Rápido - <?= htmlspecialchars($p['nombre']) ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body row g-3">
                                                <div class="mb-3">
                                                    <label for="cantidad_ingreso_<?= $p['id'] ?>" class="form-label">Cantidad</label>
                                                    <input type="number" name="cantidad" id="cantidad_ingreso_<?= $p['id'] ?>" class="form-control cantidad-input" min="1" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="precio_compra_ingreso_<?= $p['id'] ?>" class="form-label">Precio de Compra ($)</label>
                                                    <input type="text" step="0.01" name="precio_compra" id="precio_compra_ingreso_<?= $p['id'] ?>" class="form-control  precio-usd" min="0.01" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="precio_compra_ingreso_bs_<?= $p['id'] ?>" class="form-label">Precio de Compra (Bs)</label>
                                                    <input type="text" step="0.01" name="precio_compra_bs" id="precio_compra_ingreso_bs_<?= $p['id'] ?>" class="form-control input-price precio-bs">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="subtotal_ingreso_<?= $p['id'] ?>" class="form-label">Subtotal</label>
                                                    <h3 class="" id="subtotal_ingreso_<?= $p['id'] ?>" readonly></h3>
                                                </div>

                                                <div class="alert alert-info">
                                                    <strong>Nota:</strong> Este ingreso se registrará con el usuario actual y la fecha actual.
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">Registrar</button>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                </div>
                                                </div>
                                            </form>
                                            </div>
                                        </div>
                                    </div>

                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Entradas -->
                <div class="tab-pane fade" id="entradas" role="tabpanel" aria-labelledby="entradas-tab">
                    <div class="d-flex justify-content-between mb-3">
                        <h5>Ingresos Registrados</h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoIngreso">+ Nuevo Ingreso</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped" id="tablaEntradas">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Proveedor</th>
                                    <th>Usuario</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Detalles</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ingresos as $i): ?>
                                    <tr>
                                        <td><?= $i['id'] ?></td>
                                        <td><?= htmlspecialchars($i['id_proveedor']) ?></td>
                                        <td><?= htmlspecialchars($i['id_usuario']) ?></td>
                                        <td><?= $i['fecha'] ?></td>
                                        <td><?= $i['total'] ?> Bs</td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick='verDetallesIngreso(<?= json_encode($i["detalles"]) ?>)'>Ver</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nueva Imagen -->
    <div class="modal fade" id="modalImagen" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <img id="imgGrande" src="" alt="Imagen producto" class="img-fluid rounded">
            </div>
        </div>
    </div>

    <!-- Modal Nuevo Ingreso -->
    <div class="modal fade" id="modalNuevoIngreso" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form method="POST" action="?c=CIngresos/add">
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar Nuevo Ingreso</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <div class="col-md-6">
                            <label>Proveedor</label>
                            <select name="id_proveedor" class="form-select" required>
                                <option value="" disabled selected>Seleccione un proveedor</option>
                                <?php foreach ($proveedores as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Fecha</label>
                            <input type="datetime-local" id="fecha" name="fecha" class="form-control" required>
                        </div>

                        <div class="col-12">
                            <h6>Productos a Ingresar</h6>
                            <table class="table table-bordered" id="tablaProductosIngreso">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Precio Compra</th>
                                        <th>Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyIngreso">
                                    <!-- Se agregan filas dinámicamente -->
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="agregarFilaIngreso()">+ Añadir Producto</button>
                        </div>
                        <div class="col-md-6 offset-md-6 text-end">
                            <strong>Total:</strong>
                            <span id="totalIngreso">0.00</span> Bs
                            <input type="hidden" name="total" id="inputTotalIngreso">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Guardar Ingreso</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Ver Detalles -->
    <div class="modal fade" id="modalDetallesIngreso" tabindex="-1" aria-labelledby="modalDetallesIngresoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalDetallesIngresoLabel">Detalles del Ingreso</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <table class="table table-bordered">
            <thead>
                <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Compra</th>
                <th>Subtotal</th>
                </tr>
            </thead>
            <tbody id="tbodyDetallesIngreso">
                <!-- Aquí se cargan dinámicamente los detalles -->
            </tbody>
            </table>
            <div class="text-end">
            <strong>Total: <span id="totalDetalleIngreso">0.00</span> Bs</strong>
            </div>
        </div>
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

    <?php include_once 'src/Views/complements/footer.php'; ?>
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
        let productos = <?= json_encode($productosJSON); ?>; // Asegúrate de pasarlos desde PHP

        function agregarFilaIngreso() {
            const tbody = document.getElementById('tbodyIngreso');
            const tr = document.createElement('tr');

            const selectOptions = productos.map(p => 
                `<option value="${productos.id}">${productos.nombre}</option>`
            ).join('');

            tr.innerHTML = `
                <td>
                    <select name="productos[]" class="form-select" required>
                        ${selectOptions}
                    </select>
                </td>
                <td>
                    <input type="number" name="cantidades[]" class="form-control cantidad" min="1" value="1" required>
                </td>
                <td>
                    <input type="number" name="precios[]" class="form-control precio" min="0.01" step="0.01" value="0.00" required>
                </td>
                <td class="subtotal">0.00</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarFila(this)">X</button>
                </td>
            `;

            tbody.appendChild(tr);
            actualizarTotales();
            tr.querySelectorAll('.cantidad, .precio').forEach(input => {
                input.addEventListener('input', actualizarTotales);
            });
        }

        function eliminarFila(btn) {
            btn.closest('tr').remove();
            actualizarTotales();
        }

        function actualizarTotales() {
        const filas = document.querySelectorAll('#tbodyIngreso tr');
        let total = 0;

        filas.forEach(fila => {
            const cantidad = parseFloat(fila.querySelector('.cantidad').value) || 0;
            const precio = parseFloat(fila.querySelector('.precio').value) || 0;
            const subtotal = cantidad * precio;
            fila.querySelector('.subtotal').innerText = subtotal.toFixed(2);
            total += subtotal;
        });

        document.getElementById('totalIngreso').innerText = total.toFixed(2);
        document.getElementById('inputTotalIngreso').value = total.toFixed(2);
    }
        function verDetallesIngreso(detalles) {
            const tbody = document.getElementById('tbodyDetallesIngreso');
            const totalSpan = document.getElementById('totalDetalleIngreso');
            tbody.innerHTML = '';
            let total = 0;

            detalles.forEach(d => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${d.producto}</td>
                    <td>${d.cantidad}</td>
                    <td>${parseFloat(d.precio_compra).toFixed(2)}</td>
                    <td>${parseFloat(d.subtotal).toFixed(2)}</td>
                `;
                tbody.appendChild(tr);
                total += parseFloat(d.subtotal);
            });

            totalSpan.innerText = total.toFixed(2);
            const modal = new bootstrap.Modal(document.getElementById('modalDetallesIngreso'));
            modal.show();
        }
        const tasaValor = <?php echo json_encode($tasaData[0]['valor']); ?>; // reemplaza por el valor real de la tasa

document.addEventListener("input", function (e) {
    if (e.target.classList.contains("precio-usd") || e.target.classList.contains("cantidad-input")) {
        const modal = e.target.closest(".modal");
        const id = modal.id.split("_").pop();

        const cantidad = parseFloat(document.getElementById(`cantidad_ingreso_${id}`).value.replace(/[^0-9.]/g, "")) || 0;
        const precioUSD = parseFloat(document.getElementById(`precio_compra_ingreso_${id}`).value.replace(/[^0-9.]/g, "")) || 0;
        const precioBS = precioUSD * tasaValor;
        const subtotalBS = cantidad * precioBS;
        const subtotalUSD = cantidad * precioUSD;

        document.getElementById(`precio_compra_ingreso_bs_${id}`).value = precioBS.toFixed(2);
        document.getElementById(`subtotal_ingreso_${id}`).textContent = 
        `${subtotalBS.toLocaleString('es-VE', { style: 'currency', currency: 'VES' })} / ${subtotalUSD.toLocaleString('en-US', { style: 'currency', currency: 'USD' })}`;
    }
});

    </script>
</body>
</html>
