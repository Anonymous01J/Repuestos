<!DOCTYPE html>
<html lang="es">
<?php
include_once 'src/Views/complements/head.php';
include_once 'src/Views/complements/header.php';
?>
    <div class="container mt-4">
        <h2 class="mb-4">Productos</h2>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalNuevo">+ Nuevo Producto</button>
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalNuevoIngreso">
            + Registrar Entrada
        </button>
        <button class="btn btn-secondary mb-3" data-bs-toggle="modal" data-bs-target="#modalNuevaVenta">
            + Registrar Venta
        </button>
        <div class="container bg-white p-4 rounded shadow-sm">
            <ul class="nav nav-tabs mb-3" id="clienteTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="productos-tab" data-bs-toggle="tab" data-bs-target="#productos" type="button">Productos</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="entradas-tab" data-bs-toggle="tab" data-bs-target="#entradas" type="button">Entradas</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="ventas-tab" data-bs-toggle="tab" data-bs-target="#ventas" type="button">Ventas</button>
                </li>
            </ul>
            <div class="tab-content">
                <!-- Productos -->
                <div class="tab-pane fade show active" id="productos" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table-DT table table-striped" id="tablaProductos">
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
                                                onerror="this.onerror=null;this.src='public/assets/img/products/generic.png';"
                                                onclick="verImagen('<?= htmlspecialchars($p['imagen']) ?>')">
                                        </td>
                                        <td><?= htmlspecialchars($p['nombre']) ?></td>
                                        <td><?= htmlspecialchars($p['marca']) ?></td>
                                        <td>
                                            <?= htmlspecialchars($p['precio']) ?> $ /
                                            <?= htmlspecialchars($p['precio'] * $tasaValor) ?> Bs
                                        </td>
                                        <td><?= htmlspecialchars($p['stock']) ?></td>
                                        <td><?= htmlspecialchars($p['stock_minimo']) ?></td>
                                        <td><?= htmlspecialchars($p['stock_maximo']) ?></td>
                                        <td><?= htmlspecialchars($p['categoria']) ?></td>
                                        <td>
                                            <div class="accordion" id="accordionAcciones_<?= $p['id'] ?>">
                                                <div class="accordion-item">
                                                    <button class="accordion-button collapsed p-2" type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#collapseAcciones_<?= $p['id'] ?>"
                                                        aria-expanded="false">
                                                    </button>
                                                    <div id="collapseAcciones_<?= $p['id'] ?>" class="accordion-collapse collapse"
                                                        data-bs-parent="#accordionAcciones_<?= $p['id'] ?>">
                                                        <div class="accordion-body p-2">
                                                            <div class="d-flex gap-1">
                                                                <button class="btn btn-sm btn-outline-warning"
                                                                    onclick="editarProducto(<?= htmlspecialchars(json_encode($p)) ?>)">
                                                                    Editar
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-success"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#modalIngresoRapido_<?= $p['id'] ?>">
                                                                    Entrada Rápida
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-success"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#modalVentaRapida_<?= $p['id'] ?>"
                                                                    data-precioProducto="<?= $p['precio'] ?>">
                                                                    Venta Rápida
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal Ingreso Rápido -->
                                    <div class="modal fade" id="modalIngresoRapido_<?= $p['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <form method="POST" action="?c=CIngresos/add">
                                                    <input type="hidden" name="id_usuario" value="<?= $_SESSION['id'] ?>">
                                                    <input type="hidden" name="id_producto" value="<?= $p['id'] ?>">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Entrada Rápida - <?= htmlspecialchars($p['nombre']) ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body row g-3">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Cantidad</label>
                                                            <input type="number" name="cantidad" class="form-control" min="1" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Precio de Compra ($)</label>
                                                            <input type="text" name="precio" class="form-control precio input-price" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Precio de Compra (Bs)</label>
                                                            <input type="text" name="precio_bs" class="form-control input-price precio_bs">
                                                        </div>
                                                        <div class="alert alert-info">
                                                            <strong>Nota:</strong> Este ingreso se registrará con el usuario actual y la fecha actual.
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary">Registrar</button>
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Venta Rápida -->
                                    <div class="modal fade" id="modalVentaRapida_<?= $p['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <form method="POST" action="?c=CVentas/add">
                                                    <input type="hidden" name="id_usuario" value="<?= $_SESSION['id'] ?>">
                                                    <input type="hidden" name="id_producto" value="<?= $p['id'] ?>">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Venta Rápida - <?= htmlspecialchars($p['nombre']) ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label">Cantidad</label>
                                                            <input type="number" name="cantidad" class="form-control" min="1" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Precio ($)</label>
                                                            <input type="text" name="precio" class="form-control" value="<?= $p['precio'] ?>" readonly>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Precio (Bs)</label>
                                                            <input type="text" name="precio_bs" class="form-control"
                                                                value="<?= htmlspecialchars($p['precio'] * $tasaValor) ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="alert alert-info m-3">
                                                        <strong>Nota:</strong> Esta venta se registrará con el usuario actual y la fecha actual.
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary">Registrar Venta</button>
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
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
                <div class="tab-pane fade" id="entradas" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table-DT table table-striped" id="tablaEntradas">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Usuario</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Productos</th>
                                    <th>Detalles</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ingresos as $i):
                                    // Recopilar nombres de productos de este ingreso para que el buscador los indexe
                                    $nombresProductosIngreso = [];
                                    foreach ($detallesIngresos as $di) {
                                        if ($di['id_ingreso'] == $i['id']) {
                                            $prod = array_filter($productos, fn($p) => $p['id'] == $di['id_producto']);
                                            if ($prod) {
                                                $nombresProductosIngreso[] = reset($prod)['nombre'];
                                            }
                                        }
                                    }
                                ?>
                                    <tr>
                                        <td><?= $i['id'] ?></td>
                                        <td><?= htmlspecialchars($i['usuario']) ?></td>
                                        <td><?= $i['fecha'] ?></td>
                                        <td><?= htmlspecialchars($i['total']) ?> $ / <?= htmlspecialchars($i['total_bs']) ?> Bs</td>
                                        <td class="text-muted small"><?= htmlspecialchars(implode(', ', $nombresProductosIngreso)) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalDetallesIngreso"
                                                data-id="<?= $i['id'] ?>">Ver</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Ventas -->
                <div class="tab-pane fade" id="ventas" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table-DT table table-striped" id="tablaVentas">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Usuario</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Productos</th><!-- columna visible con nombres para que DataTables los indexe -->
                                    <th>Detalles</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Construir mapa de detalles de venta agrupados por id_venta
                                // usando los modelos ya cargados ($ventas, $productos)
                                foreach ($ventas as $v):
                                    // Buscar el nombre del usuario
                                    $nombreUsuario = '';
                                    foreach ($productos as $p) { break; } // solo para inicializar
                                    // Obtener nombre usuario desde sesión o lista (simplificado)
                                    $nombreUsuario = $v['id_usuario']; // fallback; se reemplaza abajo
                                ?>
                                    <tr>
                                        <td><?= $v['id'] ?></td>
                                        <td><?= htmlspecialchars($v['id_usuario']) ?></td>
                                        <td><?= $v['fecha'] ?></td>
                                        <td><?= htmlspecialchars($v['total']) ?> $ / <?= htmlspecialchars($v['total_bs']) ?> Bs</td>
                                        <!-- Celda con productos: se rellena vía JS al cargar el modal, 
                                             pero también ponemos un span oculto con los nombres para el buscador -->
                                        <td class="productos-venta-cell" data-id-venta="<?= $v['id'] ?>">
                                            <span class="text-muted small">Cargando...</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info btn-ver-venta"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalDetallesVenta"
                                                data-id="<?= $v['id'] ?>">Ver</button>
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

    <!-- Modal Imagen -->
    <div class="modal fade" id="modalImagen" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <img id="imgGrande" src="" alt="Imagen producto" class="img-fluid rounded">
            </div>
        </div>
    </div>

    <!-- Modal Registro de Ingreso Múltiple -->
    <div class="modal fade" id="modalNuevoIngreso" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form method="POST" action="?c=CIngresos/addMany">
                    <input type="hidden" name="id_usuario" value="<?= $_SESSION['id'] ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar Nuevo Ingreso</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <button type="button" class="btn btn-outline-primary mb-2" onclick="agregarFilaIngreso()">+ Agregar Producto</button>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Producto</th><th>Cantidad</th><th>Precio ($)</th>
                                        <th>Precio (Bs)</th><th>Subtotal ($)</th><th>Subtotal (Bs)</th><th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyIngreso"></tbody>
                            </table>
                        </div>
                        <div id="totalResumen" class="fw-bold text-end"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Guardar Ingreso</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Registro de Venta Múltiple -->
    <div class="modal fade" id="modalNuevaVenta" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form method="POST" action="?c=CVentas/addMany">
                    <input type="hidden" name="id_usuario" value="<?= $_SESSION['id'] ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar Nueva Venta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <button type="button" class="btn btn-outline-primary mb-2" onclick="agregarFilaVenta()">+ Agregar Producto</button>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Producto</th><th>Cantidad</th><th>Precio ($)</th>
                                        <th>Precio (Bs)</th><th>Subtotal ($)</th><th>Subtotal (Bs)</th><th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyVenta"></tbody>
                            </table>
                        </div>
                        <div id="totalResumenVenta" class="fw-bold text-end"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Guardar Venta</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Detalles Ingreso -->
    <div class="modal fade" id="modalDetallesIngreso" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles del Ingreso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="tableDetails table table-striped table-bordered">
                        <thead><tr><th>Producto</th><th>Cantidad</th><th>Subtotal</th></tr></thead>
                        <tbody id="tbodyDetallesIngreso"></tbody>
                    </table>
                    <div class="text-end"><strong id="totalDetalleIngreso"></strong></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detalles Venta -->
    <div class="modal fade" id="modalDetallesVenta" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles de la Venta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="tableDetails table table-striped table-bordered">
                        <thead><tr><th>Producto</th><th>Cantidad</th><th>Subtotal</th></tr></thead>
                        <tbody id="tbodyDetallesVenta"></tbody>
                    </table>
                    <div class="text-end"><strong id="totalDetallesVenta"></strong></div>
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
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Marca</label>
                                <input type="text" name="marca" class="form-control">
                            </div>
                            <!-- CORRECCIÓN: precio usa input-price para el formateo venezolano -->
                            <div class="col-md-6">
                                <label class="form-label">Precio ($)</label>
                                <input type="text" name="precio" class="form-control input-price" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Categoría</label>
                                <select name="id_categoria" class="form-select select-categoria" required>
                                    <?php if (!empty($categorias)): ?>
                                        <option value="">Seleccione una categoría</option>
                                        <?php foreach ($categorias as $cat): ?>
                                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" disabled selected>⚠️ No hay categorías. Cree una primero.</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Stock Mínimo</label>
                                <input type="number" name="stock_minimo" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Stock Máximo</label>
                                <input type="number" name="stock_maximo" class="form-control" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Imagen</label>
                                <input type="file" name="imagen" class="form-control"
                                    onchange="previewImagen(this, 'previewNuevo')">
                                <img id="previewNuevo" src="" alt="Vista previa"
                                    class="img-thumbnail mt-2" width="100" style="display:none;">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="2"></textarea>
                            </div>
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
                        <div class="col-md-6">
                            <label>Nombre</label>
                            <input name="nombre" id="editNombre" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Marca</label>
                            <input name="marca" id="editMarca" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>Precio ($)</label>
                            <input type="text" name="precio" id="editPrecio" class="form-control input-price" required>
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
                            <input type="file" name="imagen" class="form-control"
                                onchange="previewImagen(this, 'previewEditar')">
                            <input type="hidden" name="imagen" id="editImagenHidden">
                            <img id="previewEditar" src="" alt="Preview" class="img-thumbnail mt-2" width="100">
                        </div>
                        <div class="col-12">
                            <label>Descripción</label>
                            <textarea name="descripcion" id="editDescripcion" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label>Estado</label>
                            <select name="estado" id="editEstado" class="form-select">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
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
        // Ir al tab correcto si hay hash en la URL
        document.addEventListener("DOMContentLoaded", function () {
            const hash = window.location.hash;
            if (hash) {
                const tabTrigger = document.querySelector(`button[data-bs-target="${hash}"]`);
                if (tabTrigger) new bootstrap.Tab(tabTrigger).show();
            }

            // =====================================================
            // CORRECCIÓN: Cargar nombres de productos en tabla Ventas
            // para que DataTables los indexe y el buscador funcione
            // =====================================================
            cargarNombresProductosVentas();
        });

        function cargarNombresProductosVentas() {
            document.querySelectorAll('.productos-venta-cell').forEach(function(celda) {
                const idVenta = celda.dataset.idVenta;
                $.ajax({
                    type: 'POST',
                    url: '?c=CVentas/searchDetails',
                    data: { id_venta: idVenta },
                    dataType: 'json',
                    success: function(data) {
                        if (Array.isArray(data) && data.length > 0) {
                            const nombres = data.map(item => item.nombre_producto).join(', ');
                            celda.innerHTML = `<span class="text-muted small">${nombres}</span>`;
                            // Invalidar fila en DataTables para que reindexe con los nuevos datos
                            const tabla = $('#tablaVentas').DataTable();
                            const fila = $(celda).closest('tr');
                            tabla.row(fila).invalidate().draw(false);
                        } else {
                            celda.innerHTML = '<span class="text-muted small">—</span>';
                        }
                    },
                    error: function() {
                        celda.innerHTML = '<span class="text-muted small">—</span>';
                    }
                });
            });
        }

        function verImagen(nombre) {
            var img = document.getElementById('imgGrande');
            img.onerror = function() { this.onerror=null; this.src='public/assets/img/products/generic.png'; };
            img.src = 'public/assets/img/products/' + nombre;
        }

        function previewImagen(input, idPreview) {
            const file = input.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById(idPreview);
                img.src = e.target.result;
                img.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }

        function editarProducto(data) {
            document.getElementById('editId').value = data.id;
            document.getElementById('editNombre').value = data.nombre;
            document.getElementById('editMarca').value = data.marca || '';
            // CORRECCIÓN: al editar, formateamos el precio con el formato venezolano
            document.getElementById('editPrecio').value = formatMoneda(parseFloat(data.precio));
            document.getElementById('editStockMinimo').value = data.stock_minimo;
            document.getElementById('editStockMaximo').value = data.stock_maximo;
            document.getElementById('editDescripcion').value = data.descripcion || '';
            document.getElementById('editImagenHidden').value = data.imagen || '';
            document.getElementById('previewEditar').src = 'public/assets/img/products/' + (data.imagen || '');
            document.getElementById('editCategoria').value = data.id_categoria;
            document.getElementById('editEstado').value = data.estado;
            new bootstrap.Modal(document.getElementById('modalEditar')).show();
        }

        const tasa = <?= floatval($tasaData[0]['valor']) ?>;

        function formatMoneda(valor) {
            let entero = Math.floor(valor).toString();
            let decimales = Math.round((valor % 1) * 100).toString().padStart(2, '0');
            let miles = entero.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            return miles + ',' + decimales;
        }
        function parseMoneda(valor) {
            return parseFloat(String(valor).replace(/\./g, '').replace(/,/g, '.')) || 0;
        }

        // Conversión en modales de ingreso rápido (precio ↔ precio_bs)
        $('.precio').on('keyup', function () {
            let valorDolares = parseMoneda($(this).val());
            $('.precio_bs').val(formatMoneda(valorDolares * tasa));
        });
        $('.precio_bs').on('keyup', function () {
            let valorBolivares = parseMoneda($(this).val());
            $('.precio').val(formatMoneda(valorBolivares / tasa));
        });

        $(".tableDetails").DataTable({
            language: { url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json" },
            paging: false, searching: false, info: false
        });

        // Modal detalles venta
        $('#modalDetallesVenta').on('show.bs.modal', function(event) {
            const idVenta = $(event.relatedTarget).data('id');
            $.ajax({
                type: 'POST', url: '?c=CVentas/searchDetails',
                data: { id_venta: idVenta }, dataType: 'json',
                success: function(data) {
                    const tbody = $('#tbodyDetallesVenta').empty();
                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(item => {
                            tbody.append(`<tr>
                                <td>${item.nombre_producto}</td>
                                <td>${item.cantidad}</td>
                                <td>${item.subtotal} $ / ${item.subtotal_bs} Bs</td>
                            </tr>`);
                        });
                        $('#totalDetallesVenta').text(`Total: ${data[0].total_venta} $ / ${data[0].total_venta_bs} Bs`);
                    } else {
                        tbody.append('<tr><td colspan="3" class="text-center">Sin detalles</td></tr>');
                    }
                }
            });
        });

        // Modal detalles ingreso
        $('#modalDetallesIngreso').on('show.bs.modal', function(event) {
            const idIngreso = $(event.relatedTarget).data('id');
            $.ajax({
                type: 'POST', url: '?c=CIngresos/searchDetails',
                data: { id_ingreso: idIngreso }, dataType: 'json',
                success: function(data) {
                    const tbody = $('#tbodyDetallesIngreso').empty();
                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(item => {
                            tbody.append(`<tr>
                                <td>${item.nombre_producto}</td>
                                <td>${item.cantidad}</td>
                                <td>${item.subtotal} $ / ${item.subtotal_bs} Bs</td>
                            </tr>`);
                        });
                        $('#totalDetalleIngreso').text(`Total: ${data[0].total_ingreso} $ / ${data[0].total_ingreso_bs} Bs`);
                    } else {
                        tbody.append('<tr><td colspan="3" class="text-center">Sin detalles</td></tr>');
                    }
                }
            });
        });

        // ---- Tablas dinámicas de ingreso/venta múltiple ----
        function getOptionsProductos() {
            let options = `<option value="">Seleccione</option>`;
            <?php foreach ($productos as $p): ?>
                options += `<option value="<?= $p['id'] ?>" data-precio="<?= $p['precio'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>`;
            <?php endforeach; ?>
            return options;
        }

        function filaTemplate(tipo) {
            return `
            <tr>
                <td><select name="productos[]" class="form-select select-producto" required>${getOptionsProductos()}</select></td>
                <td><input type="number" name="cantidades[]" class="form-control cantidad" min="1" value="1" required></td>
                <td><input type="text" name="precio[]" class="form-control precio input-price" required></td>
                <td><input type="text" name="precio_bs[]" class="form-control precio_bs input-price" required></td>
                <td><input type="text" class="form-control subtotal input-price" readonly></td>
                <td><input type="text" class="form-control subtotal_bs input-price" readonly></td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">X</button></td>
            </tr>`;
        }

        function agregarFilaIngreso() {
            $('#tbodyIngreso').append(filaTemplate('ingreso'));
            bindEvents();
            $('#tbodyIngreso .select-producto').last().select2({ dropdownParent: $('#modalNuevoIngreso') });
        }

        function agregarFilaVenta() {
            $('#tbodyVenta').append(filaTemplate('venta'));
            bindEvents();
            $('#tbodyVenta .select-producto').last().select2({ dropdownParent: $('#modalNuevaVenta') });
        }

        function eliminarFila(btn) {
            $(btn).closest('tr').remove();
            actualizarTotales();
            actualizarTotalesVenta();
        }

        function bindEvents() {
            // Re-bind en todas las filas de ingreso
            $('#tbodyIngreso tr').each(function() {
                const fila = $(this);
                fila.find('.select-producto').off('change').on('change', function() {
                    let precio = parseFloat($(this).find('option:selected').data('precio')) || 0;
                    fila.find('.precio').val(formatMoneda(precio));
                    fila.find('.precio_bs').val(formatMoneda(precio * tasa));
                    recalcularFila(fila);
                    actualizarTotales();
                });
                fila.find('.precio').off('keyup').on('keyup', function() {
                    let v = parseMoneda($(this).val());
                    fila.find('.precio_bs').val(formatMoneda(v * tasa));
                    recalcularFila(fila);
                    actualizarTotales();
                });
                fila.find('.precio_bs').off('keyup').on('keyup', function() {
                    let v = parseMoneda($(this).val());
                    fila.find('.precio').val(formatMoneda(v / tasa));
                    recalcularFila(fila);
                    actualizarTotales();
                });
                fila.find('.cantidad').off('input').on('input', function() {
                    recalcularFila(fila);
                    actualizarTotales();
                });
            });

            // Re-bind en todas las filas de venta
            $('#tbodyVenta tr').each(function() {
                const fila = $(this);
                fila.find('.select-producto').off('change').on('change', function() {
                    let precio = parseFloat($(this).find('option:selected').data('precio')) || 0;
                    fila.find('.precio').val(formatMoneda(precio));
                    fila.find('.precio_bs').val(formatMoneda(precio * tasa));
                    recalcularFila(fila);
                    actualizarTotalesVenta();
                });
                fila.find('.precio').off('keyup').on('keyup', function() {
                    let v = parseMoneda($(this).val());
                    fila.find('.precio_bs').val(formatMoneda(v * tasa));
                    recalcularFila(fila);
                    actualizarTotalesVenta();
                });
                fila.find('.precio_bs').off('keyup').on('keyup', function() {
                    let v = parseMoneda($(this).val());
                    fila.find('.precio').val(formatMoneda(v / tasa));
                    recalcularFila(fila);
                    actualizarTotalesVenta();
                });
                fila.find('.cantidad').off('input').on('input', function() {
                    recalcularFila(fila);
                    actualizarTotalesVenta();
                });
            });
        }

        function recalcularFila(fila) {
            let cantidad = parseFloat(fila.find('.cantidad').val()) || 1;
            let precio = parseMoneda(fila.find('.precio').val());
            let precio_bs = parseMoneda(fila.find('.precio_bs').val());
            fila.find('.subtotal').val(formatMoneda(cantidad * precio));
            fila.find('.subtotal_bs').val(formatMoneda(cantidad * precio_bs));
        }

        function actualizarTotales() {
            let total = 0, total_bs = 0;
            $('#tbodyIngreso tr').each(function() {
                total += parseMoneda($(this).find('.subtotal').val());
                total_bs += parseMoneda($(this).find('.subtotal_bs').val());
            });
            $('#totalResumen').text(`Total: ${formatMoneda(total)} $ / ${formatMoneda(total_bs)} Bs`);
        }

        function actualizarTotalesVenta() {
            let total = 0, total_bs = 0;
            $('#tbodyVenta tr').each(function() {
                total += parseMoneda($(this).find('.subtotal').val());
                total_bs += parseMoneda($(this).find('.subtotal_bs').val());
            });
            $('#totalResumenVenta').text(`Total: ${formatMoneda(total)} $ / ${formatMoneda(total_bs)} Bs`);
        }
    </script>
</body>
</html>