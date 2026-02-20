<!DOCTYPE html>
<html lang="es">
<?php
include_once 'src/Views/complements/head.php';
include_once 'src/Views/complements/header.php';
?>

<!-- ── Toast de error de stock (generado por backend tras redirect) ────── -->
<?php if (isset($_GET['stock_error']) && !empty($_SESSION['stock_error'])): ?>
<div class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 9999;">
    <div id="toastStockError" class="toast show align-items-center text-bg-danger border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <strong>❌ Stock insuficiente:</strong><br>
                <?= $_SESSION['stock_error'] ?>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<?php unset($_SESSION['stock_error']); ?>
<?php endif; ?>

<!-- ── Toast de éxito ──────────────────────────────────────────────────── -->
<?php if (isset($_GET['success'])): ?>
<div class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 9999;">
    <div id="toastSuccess" class="toast show align-items-center text-bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">✅ Operación realizada con éxito.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<?php endif; ?>

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
                <!-- ── Tab: Productos ──────────────────────────────────── -->
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
                                    <?php
                                        // Resaltar fila si stock está en nivel crítico
                                        $stockAlerta = '';
                                        if ($p['stock'] == 0) {
                                            $stockAlerta = 'table-danger';
                                        } elseif ($p['stock'] <= $p['stock_minimo']) {
                                            $stockAlerta = 'table-warning';
                                        }
                                    ?>
                                    <tr class="<?= $stockAlerta ?>">
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
                                        <td>
                                            <?php if ($p['stock'] == 0): ?>
                                                <span class="badge bg-danger">0 — Sin stock</span>
                                            <?php elseif ($p['stock'] <= $p['stock_minimo']): ?>
                                                <span class="badge bg-warning text-dark"><?= $p['stock'] ?> ⚠️</span>
                                            <?php else: ?>
                                                <?= htmlspecialchars($p['stock']) ?>
                                            <?php endif; ?>
                                        </td>
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
                                                                    data-precioProducto="<?= $p['precio'] ?>"
                                                                    data-stock="<?= (int)$p['stock'] ?>">
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

                                    <!-- Modal Venta Rápida — con validación de stock en frontend -->
                                    <div class="modal fade modal-venta-rapida"
                                         id="modalVentaRapida_<?= $p['id'] ?>"
                                         tabindex="-1"
                                         data-id-producto="<?= $p['id'] ?>"
                                         data-stock="<?= (int)$p['stock'] ?>">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <form class="form-venta-rapida" method="POST" action="?c=CVentas/add">
                                                    <input type="hidden" name="id_usuario" value="<?= $_SESSION['id'] ?>">
                                                    <input type="hidden" name="id_producto" value="<?= $p['id'] ?>">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Venta Rápida - <?= htmlspecialchars($p['nombre']) ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body row g-3">
                                                        <div class="col-12">
                                                            <!-- Alerta de stock disponible -->
                                                            <div class="alert alert-info mb-3 py-2">
                                                                Stock disponible: <strong><?= (int)$p['stock'] ?></strong> unidad(es)
                                                            </div>
                                                            <!-- Zona de error de stock (dinámica) -->
                                                            <div class="alert alert-danger stock-error-msg d-none" role="alert"></div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Cantidad</label>
                                                            <input type="number"
                                                                   name="cantidad"
                                                                   class="form-control cantidad-venta-rapida"
                                                                   min="1"
                                                                   value="1"
                                                                   max="<?= (int)$p['stock'] ?>"
                                                                   data-stock="<?= (int)$p['stock'] ?>"
                                                                   required>
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
                                                    <div class="alert alert-info m-3 mb-0">
                                                        <strong>Nota:</strong> Esta venta se registrará con el usuario actual y la fecha actual.
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary btn-submit-venta-rapida">Registrar Venta</button>
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

                <!-- ── Tab: Entradas ───────────────────────────────────── -->
                <div class="tab-pane fade" id="entradas" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-striped" id="tablaEntradas">
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
                                    $nombresProductosIngreso = [];
                                    foreach ($detallesIngresos as $di) {
                                        if ($di['id_ingreso'] == $i['id']) {
                                            $prod = array_filter($productos, fn($p) => $p['id'] == $di['id_producto']);
                                            if ($prod) $nombresProductosIngreso[] = reset($prod)['nombre'];
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

                <!-- ── Tab: Ventas ─────────────────────────────────────── -->
                <div class="tab-pane fade" id="ventas" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-striped" id="tablaVentas">
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
                                <?php foreach ($ventas as $v): ?>
                                    <tr>
                                        <td><?= $v['id'] ?></td>
                                        <td><?= htmlspecialchars($v['id_usuario']) ?></td>
                                        <td><?= $v['fecha'] ?></td>
                                        <td><?= htmlspecialchars($v['total']) ?> $ / <?= htmlspecialchars($v['total_bs']) ?> Bs</td>
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

    <!-- ── Modales globales ──────────────────────────────────────────────── -->

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

    <!-- Modal Registro de Venta Múltiple (con validación AJAX de stock) -->
    <div class="modal fade" id="modalNuevaVenta" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form id="formVentaMultiple" method="POST" action="?c=CVentas/addMany">
                    <input type="hidden" name="id_usuario" value="<?= $_SESSION['id'] ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar Nueva Venta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Zona de errores de stock para venta múltiple -->
                        <div id="stockErrorMultiple" class="alert alert-danger d-none" role="alert"></div>

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
                        <button type="button" id="btnGuardarVenta" class="btn btn-success">Guardar Venta</button>
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
    // ── Variables globales ─────────────────────────────────────────────────
    const tasa = <?= floatval($tasaData[0]['valor']) ?>;
    const stockDisponible = {
        <?php foreach ($productos as $p): ?>
            <?= $p['id'] ?>: <?= floatval($p['stock']) ?>,
        <?php endforeach; ?>
    };

    // ── Utilidades de moneda MEJORADAS ────────────────────────────────────
    function parseMoneda(valor) {
        if (!valor || valor === '') return 0;
        
        // Si ya es número, devolverlo
        if (typeof valor === 'number') return valor;
        
        valor = String(valor).trim();
        
        // Detectar formato venezolano (termina en ,XX)
        if (/,(\d{1,2})$/.test(valor)) {
            // Formato: 1.234,56
            valor = valor.replace(/\./g, '');  // Quitar puntos de miles
            valor = valor.replace(',', '.');    // Cambiar coma decimal a punto
        } else {
            // Formato internacional: 1,234.56
            valor = valor.replace(/,/g, '');    // Quitar comas de miles
        }
        
        return parseFloat(valor) || 0;
    }

    function formatMoneda(valor) {
        if (valor === undefined || valor === null) return '0,00';
        
        valor = parseFloat(valor) || 0;
        let partes = valor.toFixed(2).split('.');
        let entero = partes[0];
        let decimal = partes[1];
        
        // Agregar separadores de miles
        entero = entero.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        
        return entero + ',' + decimal;
    }

    // ── Función para formatear input de precio en tiempo real ─────────────
    function formatearInputPrecio(input) {
        $(input).off('input.formateo').on('input.formateo', function (e) {
            // Prevenir que esto interfiera con otros eventos
            e.stopPropagation();
            
            let value = e.target.value;
            
            // Eliminar todo excepto dígitos
            let digits = value.replace(/\D/g, '');
            
            if (digits.length === 0) {
                e.target.value = '';
                return;
            }
            
            // Formatear como 0,00 cuando hay pocos dígitos
            if (digits.length <= 2) {
                e.target.value = '0,' + digits.padStart(2, '0');
            } else {
                // Separar parte entera y decimal
                let integerPart = digits.slice(0, -2);
                let decimalPart = digits.slice(-2);
                
                // Eliminar ceros a la izquierda en la parte entera
                integerPart = integerPart.replace(/^0+/, '');
                if (integerPart === '') integerPart = '0';
                
                // Agregar separadores de miles
                integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                
                e.target.value = integerPart + ',' + decimalPart;
            }
        });
        
        // Formatear al perder el foco
        $(input).off('blur.formateo').on('blur.formateo', function() {
            let value = $(this).val();
            if (value === '' || value === '0,00') {
                $(this).val('');
            }
        });
    }

    // ── Inicialización ────────────────────────────────────────────────────
    document.addEventListener("DOMContentLoaded", function () {
        const hash = window.location.hash;
        if (hash) {
            const tabTrigger = document.querySelector(`button[data-bs-target="${hash}"]`);
            if (tabTrigger) new bootstrap.Tab(tabTrigger).show();
        }
        
        cargarNombresProductosVentas();
        initVentaRapidaValidacion();
        initVentaMultipleValidacion();
        
        // Aplicar formateo a los inputs de entrada rápida existentes
        $('.modal[id^="modalIngresoRapido"] .precio, .modal[id^="modalIngresoRapido"] .precio_bs').each(function() {
            formatearInputPrecio(this);
        });

        // Aplicar formateo a los inputs de venta rápida existentes
        $('.modal[id^="modalVentaRapida"] .precio, .modal[id^="modalVentaRapida"] .precio_bs').each(function() {
            formatearInputPrecio(this);
        });

        // Auto-cerrar toasts tras 4 segundos
        document.querySelectorAll('.toast.show').forEach(function(el) {
            setTimeout(() => { 
                try { new bootstrap.Toast(el).hide(); } catch(e) {} 
            }, 4000);
        });
    });

    $('#modalNuevo').on('shown.bs.modal', function () {
        $('.select-categoria').select2({
            dropdownParent: $('#modalNuevo'),
            placeholder: 'Seleccione una categoría',
            allowClear: true
        });
    });

    // ═══════════════════════════════════════════════════════════════════════
    // VALIDACIÓN VENTA RÁPIDA (CORREGIDA)
    // ═══════════════════════════════════════════════════════════════════════
    function initVentaRapidaValidacion() {
        // Para cada modal de venta rápida
        document.querySelectorAll('.modal[id^="modalVentaRapida"]').forEach(function(modal) {
            const cantidadInput = modal.querySelector('.cantidad-venta-rapida');
            const precioInput = modal.querySelector('input[name="precio"]');
            const precioBsInput = modal.querySelector('input[name="precio_bs"]');
            
            if (!cantidadInput || !precioInput || !precioBsInput) return;
            
            // Obtener el precio unitario del value actual del input precio
            // (que viene del PHP con el valor del producto)
            const precioUnitario = parseFloat(precioInput.value) || 0;
            
            // Guardar el precio unitario como atributo data en el modal
            modal.dataset.precioUnitario = precioUnitario;
            
            // Función para actualizar precios según cantidad
            function actualizarPreciosPorCantidad() {
                const cantidad = parseFloat(cantidadInput.value) || 1;
                const precioTotalUSD = precioUnitario * cantidad;
                const precioTotalBS = precioTotalUSD * tasa;
                
                precioInput.value = formatMoneda(precioTotalUSD);
                precioBsInput.value = formatMoneda(precioTotalBS);
            }
            
            // Evento input para la cantidad
            cantidadInput.addEventListener('input', function() {
                const stockMax = parseFloat(this.dataset.stock);
                const solicitado = parseFloat(this.value) || 0;
                const errorDiv = modal.querySelector('.stock-error-msg');
                const btnSubmit = modal.querySelector('.btn-submit-venta-rapida');

                if (solicitado > stockMax) {
                    errorDiv.textContent = `❌ Stock insuficiente: disponible ${formatMoneda(stockMax)}, solicitado ${formatMoneda(solicitado)}.`;
                    errorDiv.classList.remove('d-none');
                    btnSubmit.disabled = true;
                } else if (solicitado <= 0) {
                    errorDiv.textContent = '❌ La cantidad debe ser mayor a 0.';
                    errorDiv.classList.remove('d-none');
                    btnSubmit.disabled = true;
                } else {
                    errorDiv.classList.add('d-none');
                    btnSubmit.disabled = false;
                    actualizarPreciosPorCantidad();
                }
            });

            // Validar al perder el foco
            cantidadInput.addEventListener('blur', function() {
                if (this.value === '' || parseFloat(this.value) <= 0) {
                    this.value = '1';
                    this.dispatchEvent(new Event('input'));
                }
            });
            
            // Calcular precios iniciales
            actualizarPreciosPorCantidad();
        });

        // Validación al submit
        document.querySelectorAll('.form-venta-rapida').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                const input = this.querySelector('.cantidad-venta-rapida');
                const stockMax = parseFloat(input.dataset.stock);
                const solicitado = parseFloat(input.value) || 0;
                const errorDiv = this.querySelector('.stock-error-msg');

                if (solicitado > stockMax || solicitado <= 0) {
                    e.preventDefault();
                    errorDiv.textContent = solicitado <= 0
                        ? '❌ La cantidad debe ser mayor a 0.'
                        : `❌ Stock insuficiente: disponible ${formatMoneda(stockMax)}, solicitado ${formatMoneda(solicitado)}.`;
                    errorDiv.classList.remove('d-none');
                }
            });
        });
    }

    // ═══════════════════════════════════════════════════════════════════════
    // VALIDACIÓN VENTA MÚLTIPLE
    // ═══════════════════════════════════════════════════════════════════════
    function initVentaMultipleValidacion() {
        const btnGuardar = document.getElementById('btnGuardarVenta');
        if (!btnGuardar) return;
        
        btnGuardar.addEventListener('click', function() {
            const filas = document.querySelectorAll('#tbodyVenta tr');
            
            if (filas.length === 0) {
                mostrarErrorMultiple('Agregue al menos un producto antes de guardar.');
                return;
            }

            const items = [];
            let filasValidas = 0;

            filas.forEach(function(fila) {
                const select = fila.querySelector('.select-producto');
                const cantidad = fila.querySelector('.cantidad');
                
                if (!select || !cantidad) return;
                
                const id = parseInt(select.value);
                const cant = parseFloat(cantidad.value) || 0;
                
                if (id && cant > 0) {
                    items.push({ id: id, cantidad: cant });
                    filasValidas++;
                }
            });

            if (filasValidas === 0) {
                mostrarErrorMultiple('Complete los productos y cantidades antes de guardar.');
                return;
            }

            const btn = this;
            const textoOriginal = btn.textContent;
            btn.textContent = 'Verificando stock...';
            btn.disabled = true;

            $.ajax({
                type: 'POST',
                url: '?c=CProductos/checkStock',
                data: { items: JSON.stringify(items) },
                dataType: 'json',
                success: function(resp) {
                    if (resp.ok) {
                        document.getElementById('formVentaMultiple').submit();
                    } else {
                        let html = '<strong>❌ Stock insuficiente:</strong><ul class="mb-0 mt-1">';
                        if (resp.errores && resp.errores.length > 0) {
                            resp.errores.forEach(function(err) {
                                html += `<li><strong>${err.nombre}</strong>: disponible ${err.disponible}, solicitado ${err.solicitado}</li>`;
                            });
                        } else if (resp.message) {
                            html += `<li>${resp.message}</li>`;
                        }
                        html += '</ul>';
                        mostrarErrorMultiple(html);
                    }
                },
                error: function() {
                    mostrarErrorMultiple('Error al verificar el stock. Intente de nuevo.');
                },
                complete: function() {
                    btn.textContent = textoOriginal;
                    btn.disabled = false;
                }
            });
        });
    }

    function mostrarErrorMultiple(html) {
        const zona = document.getElementById('stockErrorMultiple');
        if (zona) {
            zona.innerHTML = html;
            zona.classList.remove('d-none');
            zona.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    function limpiarErrorMultiple() {
        const zona = document.getElementById('stockErrorMultiple');
        if (zona) zona.classList.add('d-none');
    }

    // ── Utilidades ──────────────────────────────────────────────────────────
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
                        const tabla = $('#tablaVentas').DataTable();
                        tabla.row($(celda).closest('tr')).invalidate().draw(false);
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
        const img = document.getElementById('imgGrande');
        img.onerror = function() { 
            this.onerror=null; 
            this.src='public/assets/img/products/generic.png'; 
        };
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
        document.getElementById('editPrecio').value = formatMoneda(parseFloat(data.precio));
        document.getElementById('editStockMinimo').value = data.stock_minimo;
        document.getElementById('editStockMaximo').value = data.stock_maximo;
        document.getElementById('editDescripcion').value = data.descripcion || '';
        document.getElementById('editImagenHidden').value = data.imagen || '';
        document.getElementById('previewEditar').src = 'public/assets/img/products/' + (data.imagen || 'generic.png');
        document.getElementById('editCategoria').value = data.id_categoria;
        document.getElementById('editEstado').value = data.estado;
        new bootstrap.Modal(document.getElementById('modalEditar')).show();
    }

    // ── Eventos de precios para tablas múltiples ─────────────────────────
    $(document).on('keyup', '#tbodyIngreso .precio', function() {
        const fila = $(this).closest('tr');
        const v = parseMoneda($(this).val());
        fila.find('.precio_bs').val(formatMoneda(v * tasa));
        recalcularFila(fila);
        actualizarTotales();
    });

    $(document).on('keyup', '#tbodyIngreso .precio_bs', function() {
        const fila = $(this).closest('tr');
        const v = parseMoneda($(this).val());
        fila.find('.precio').val(formatMoneda(v / tasa));
        recalcularFila(fila);
        actualizarTotales();
    });

    $(document).on('keyup', '#tbodyVenta .precio', function() {
        const fila = $(this).closest('tr');
        const v = parseMoneda($(this).val());
        fila.find('.precio_bs').val(formatMoneda(v * tasa));
        recalcularFila(fila);
        actualizarTotalesVenta();
    });

    $(document).on('keyup', '#tbodyVenta .precio_bs', function() {
        const fila = $(this).closest('tr');
        const v = parseMoneda($(this).val());
        fila.find('.precio').val(formatMoneda(v / tasa));
        recalcularFila(fila);
        actualizarTotalesVenta();
    });

    // ── Eventos de precios para entrada rápida ───────────────────────────
    $(document).on('keyup', '.modal[id^="modalIngresoRapido"] .precio', function() {
        const modal = $(this).closest('.modal');
        const v = parseMoneda($(this).val());
        modal.find('.precio_bs').val(formatMoneda(v * tasa));
    });

    $(document).on('keyup', '.modal[id^="modalIngresoRapido"] .precio_bs', function() {
        const modal = $(this).closest('.modal');
        const v = parseMoneda($(this).val());
        modal.find('.precio').val(formatMoneda(v / tasa));
    });

    // ── DataTables ────────────────────────────────────────────────────────
    $(".tableDetails").DataTable({
        language: { url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json" },
        paging: false, 
        searching: false, 
        info: false
    });

    // ── Modales de detalles ───────────────────────────────────────────────
    $('#modalDetallesVenta').on('show.bs.modal', function(event) {
        const idVenta = $(event.relatedTarget).data('id');
        $.ajax({
            type: 'POST', 
            url: '?c=CVentas/searchDetails',
            data: { id_venta: idVenta }, 
            dataType: 'json',
            success: function(data) {
                const tbody = $('#tbodyDetallesVenta').empty();
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(item => {
                        tbody.append(`<tr>
                            <td>${item.nombre_producto}</td>
                            <td>${formatMoneda(item.cantidad)}</td>
                            <td>${formatMoneda(item.subtotal)} $ / ${formatMoneda(item.subtotal_bs)} Bs</td>
                        </tr>`);
                    });
                    $('#totalDetallesVenta').text(
                        `Total: ${formatMoneda(data[0].total_venta)} $ / ${formatMoneda(data[0].total_venta_bs)} Bs`
                    );
                } else {
                    tbody.append('<tr><td colspan="3" class="text-center">Sin detalles</td></tr>');
                }
            }
        });
    });

    $('#modalDetallesIngreso').on('show.bs.modal', function(event) {
        const idIngreso = $(event.relatedTarget).data('id');
        $.ajax({
            type: 'POST', 
            url: '?c=CIngresos/searchDetails',
            data: { id_ingreso: idIngreso }, 
            dataType: 'json',
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
                    $('#totalDetalleIngreso').text(
                        `Total: ${data[0].total_ingreso} $ / ${data[0].total_ingreso_bs} Bs`
                    );
                } else {
                    tbody.append('<tr><td colspan="3" class="text-center">Sin detalles</td></tr>');
                }
            }
        });
    });

    // ── Tablas dinámicas ingreso/venta múltiple ───────────────────────────
    function getOptionsProductos() {
        let options = '<option value="">Seleccione</option>';
        <?php foreach ($productos as $p): ?>
            options += `<option value="<?= $p['id'] ?>"
                data-precio="<?= $p['precio'] ?>"
                data-stock="<?= floatval($p['stock']) ?>">
                <?= htmlspecialchars($p['nombre']) ?>
            </option>`;
        <?php endforeach; ?>
        return options;
    }

    function filaTemplate() {
        return `
        <tr>
            <td>
                <select name="productos[]" class="form-select select-producto" required>
                    ${getOptionsProductos()}
                </select>
            </td>
            <td>
                <input type="number" 
                       name="cantidades[]" 
                       class="form-control cantidad" 
                       min="0.01" 
                       step="0.01" 
                       value="1" 
                       required>
            </td>
            <td>
                <input type="text" 
                       name="precio[]" 
                       class="form-control precio input-price" 
                       required>
            </td>
            <td>
                <input type="text" 
                       name="precio_bs[]" 
                       class="form-control precio_bs input-price" 
                       required>
            </td>
            <td>
                <input type="text" 
                       class="form-control subtotal input-price" 
                       readonly>
            </td>
            <td>
                <input type="text" 
                       class="form-control subtotal_bs input-price" 
                       readonly>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">X</button>
            </td>
        </tr>`;
    }

    function agregarFilaIngreso() {
        $('#tbodyIngreso').append(filaTemplate());
        const nuevaFila = $('#tbodyIngreso tr').last();
        
        // Aplicar formateo a los inputs de precio
        nuevaFila.find('.precio, .precio_bs').each(function() {
            formatearInputPrecio(this);
        });
        
        bindEventosFilaIngreso(nuevaFila);
        nuevaFila.find('.select-producto').select2({ dropdownParent: $('#modalNuevoIngreso') });
    }

    function agregarFilaVenta() {
        limpiarErrorMultiple();
        $('#tbodyVenta').append(filaTemplate());
        const nuevaFila = $('#tbodyVenta tr').last();
        
        // Aplicar formateo a los inputs de precio
        nuevaFila.find('.precio, .precio_bs').each(function() {
            formatearInputPrecio(this);
        });
        
        bindEventosFilaVenta(nuevaFila);
        nuevaFila.find('.select-producto').select2({ dropdownParent: $('#modalNuevaVenta') });
    }

    function eliminarFila(btn) {
        $(btn).closest('tr').remove();
        actualizarTotales();
        actualizarTotalesVenta();
        limpiarErrorMultiple();
    }

    function bindEventosFilaIngreso(fila) {
        fila = $(fila);
        
        fila.find('.select-producto').off('change').on('change', function() {
            const precio = parseFloat($(this).find('option:selected').data('precio')) || 0;
            fila.find('.precio').val(formatMoneda(precio));
            fila.find('.precio_bs').val(formatMoneda(precio * tasa));
            recalcularFila(fila);
            actualizarTotales();
        });
        
        fila.find('.cantidad').off('input').on('input', function() {
            recalcularFila(fila);
            actualizarTotales();
        });
    }

    function bindEventosFilaVenta(fila) {
        fila = $(fila);
        
        fila.find('.select-producto').off('change').on('change', function() {
            const option = $(this).find('option:selected');
            const precio = parseFloat(option.data('precio')) || 0;
            const stock = parseFloat(option.data('stock')) || 0;
            
            fila.find('.precio').val(formatMoneda(precio));
            fila.find('.precio_bs').val(formatMoneda(precio * tasa));
            fila.find('.cantidad')
                .attr('max', stock)
                .attr('placeholder', `Máx: ${formatMoneda(stock)}`);
            
            recalcularFila(fila);
            actualizarTotalesVenta();
            limpiarErrorMultiple();
        });

        fila.find('.cantidad').off('input').on('input', function() {
            const select = fila.find('.select-producto');
            const stock = parseFloat(select.find('option:selected').data('stock')) || 0;
            const val = parseFloat($(this).val()) || 0;
            
            if (stock > 0 && val > stock) {
                $(this).addClass('is-invalid');
                select.addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
                select.removeClass('is-invalid');
            }
            
            recalcularFila(fila);
            actualizarTotalesVenta();
            limpiarErrorMultiple();
        });
    }

    function recalcularFila(fila) {
        fila = $(fila);
        const cantidad = parseFloat(fila.find('.cantidad').val()) || 0;
        const precio = parseMoneda(fila.find('.precio').val());
        const precio_bs = parseMoneda(fila.find('.precio_bs').val());
        
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

    // Inicializar bindings para filas existentes
    $(document).ready(function() {
        $('#tbodyIngreso tr').each(function() {
            bindEventosFilaIngreso($(this));
        });
        $('#tbodyVenta tr').each(function() {
            bindEventosFilaVenta($(this));
        });
    });
</script>
</body>
</html>