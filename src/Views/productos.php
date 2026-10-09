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
            <button type="button" class="uk-close uk-position-small uk-position-top-right" aria-label="Cerrar" onclick="this.closest('.toast').remove();"></button>
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
            <button type="button" class="uk-close uk-position-small uk-position-top-right" aria-label="Cerrar" onclick="this.closest('.toast').remove();"></button>
        </div>
    </div>
</div>
<?php endif; ?>

    <div class="uk-container uk-container-large uk-padding-large">
        <div class="uk-flex uk-flex-between uk-flex-middle uk-flex-wrap uk-margin-bottom" style="gap: 12px;">
            <h2 class="uk-heading-bullet uk-margin-remove">Productos</h2>
            <div class="uk-flex uk-flex-wrap" style="gap: 12px;">
                <button class="uk-button uk-button-primary" type="button" uk-toggle="target: #modalNuevo">+ Nuevo Producto</button>
                <button class="uk-button uk-button-success" type="button" uk-toggle="target: #modalNuevoIngreso">+ Registrar Entrada</button>
                <button class="uk-button uk-button-default" type="button" uk-toggle="target: #modalNuevaVenta">+ Registrar Venta</button>
            </div>
        </div>

        <div class="uk-card uk-card-default uk-card-body">
            <ul class="uk-tab uk-margin-bottom" uk-tab>
                <li class="uk-active"><a href="#productos-tab-panel">Productos</a></li>
                <li><a href="#entradas-tab-panel">Entradas</a></li>
                <li><a href="#ventas-tab-panel">Ventas</a></li>
            </ul>
            <div class="uk-switcher">
                <!-- ── Tab: Productos ──────────────────────────────────── -->
                <div id="productos-tab-panel" class="uk-active">
                    <div class="app-item-list-shell">
                        <div class="app-item-toolbar">
                            <div class="app-item-search uk-search uk-search-default">
                                <span uk-search-icon></span>
                                <input id="productosSearch" class="uk-search-input" type="search" placeholder="Buscar producto..." aria-label="Buscar producto">
                            </div>
                            <div class="app-item-sort">
                                <span>Orden</span>
                                <button type="button" class="uk-icon-button" data-sort="asc" aria-label="Orden ascendente">
                                    <span uk-icon="icon: arrow-down; ratio: 0.9"></span>
                                </button>
                                <button type="button" class="uk-icon-button" data-sort="desc" aria-label="Orden descendente">
                                    <span uk-icon="icon: arrow-up; ratio: 0.9"></span>
                                </button>
                            </div>
                            <div class="uk-button-group app-view-toggle" role="group" aria-label="Vista de productos">
                                <button type="button" class="uk-button uk-button-small" data-product-view="cards" aria-pressed="true">
                                    <span uk-icon="icon: grid" aria-hidden="true"></span><span>Tarjetas</span>
                                </button>
                                <button type="button" class="uk-button uk-button-small" data-product-view="rows" aria-pressed="false">
                                    <span uk-icon="icon: table" aria-hidden="true"></span><span>Filas</span>
                                </button>
                            </div>
                        </div>

                        <div id="productosList" class="uk-grid uk-grid-medium uk-flex-center app-product-grid" uk-grid></div>
                        <div id="productosEmpty" class="app-empty-state uk-hidden">
                            <span uk-icon="icon: bag; ratio: 1.5"></span>
                            <div>No hay productos para mostrar.</div>
                        </div>
                        <div class="app-pagination-controls uk-flex uk-flex-between uk-flex-middle uk-flex-wrap uk-margin-top">
                            <label class="app-page-size-control" for="productosPageSize">
                                <span>Elementos por página</span>
                                <select id="productosPageSize" class="uk-select uk-form-small">
                                    <option value="6">6</option>
                                    <option value="12">12</option>
                                    <option value="24">24</option>
                                    <option value="48">48</option>
                                </select>
                            </label>
                            <ul id="productosPagination" class="uk-pagination" uk-margin></ul>
                        </div>
                    </div>
                </div>

                <!-- ── Tab: Entradas ───────────────────────────────────── -->
                <div id="entradas-tab-panel">
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
                                            <button class="uk-button uk-button-small uk-button-primary"
                                                uk-toggle="target: #modalDetallesIngreso"
                                                data-id="<?= $i['id'] ?>">Ver</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ── Tab: Ventas ─────────────────────────────────────── -->
                <div id="ventas-tab-panel">
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
                                            <button class="uk-button uk-button-small uk-button-primary btn-ver-venta"
                                                uk-toggle="target: #modalDetallesVenta"
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
    <div id="modalImagen" uk-modal>
        <div class="uk-modal-dialog uk-modal-body uk-text-center">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <img id="imgGrande" src="" alt="Imagen producto" class="uk-width-1-1 uk-border-rounded">
        </div>
    </div>

    <!-- Modal Registro de Ingreso Múltiple -->
    <div id="modalNuevoIngreso" uk-modal>
        <div class="uk-modal-dialog uk-modal-body uk-width-2xlarge">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Registrar Nuevo Ingreso</h2>
            <form method="POST" action="?c=CIngresos/addMany">
                <input type="hidden" name="id_usuario" value="<?= $_SESSION['id'] ?>">
                <div class="uk-margin">
                    <button type="button" class="uk-button uk-button-default" onclick="agregarFilaIngreso()">+ Agregar Producto</button>
                </div>
                <div class="uk-overflow-auto">
                    <table class="uk-table uk-table-divider uk-table-small uk-table-striped uk-table-middle">
                        <thead>
                            <tr>
                                <th>Producto</th><th>Cantidad</th><th>Precio ($)</th>
                                <th>Precio (Bs)</th><th>Subtotal ($)</th><th>Subtotal (Bs)</th><th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyIngreso"></tbody>
                    </table>
                </div>
                <div id="totalResumen" class="uk-text-right uk-text-bold"></div>
                <div class="uk-text-right uk-margin-top">
                    <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                    <button type="submit" class="uk-button uk-button-primary">Guardar Ingreso</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Registro de Venta Múltiple (con validación AJAX de stock) -->
    <div id="modalNuevaVenta" uk-modal>
        <div class="uk-modal-dialog uk-modal-body uk-width-2xlarge">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Registrar Nueva Venta</h2>
            <form id="formVentaMultiple" method="POST" action="?c=CVentas/addMany">
                <input type="hidden" name="id_usuario" value="<?= $_SESSION['id'] ?>">
                <div class="uk-margin">
                    <div id="stockErrorMultiple" class="uk-alert-danger uk-hidden" uk-alert role="alert"></div>
                    <button type="button" class="uk-button uk-button-default" onclick="agregarFilaVenta()">+ Agregar Producto</button>
                </div>
                <div class="uk-overflow-auto">
                    <table class="uk-table uk-table-divider uk-table-small uk-table-striped uk-table-middle">
                        <thead>
                            <tr>
                                <th>Producto</th><th>Cantidad</th><th>Precio ($)</th>
                                <th>Precio (Bs)</th><th>Subtotal ($)</th><th>Subtotal (Bs)</th><th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyVenta"></tbody>
                    </table>
                </div>
                <div id="totalResumenVenta" class="uk-text-right uk-text-bold"></div>
                <div class="uk-text-right uk-margin-top">
                    <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                    <button type="button" id="btnGuardarVenta" class="uk-button uk-button-primary">Guardar Venta</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detalles Ingreso -->
    <div id="modalDetallesIngreso" uk-modal>
        <div class="uk-modal-dialog uk-modal-body uk-width-1-2@m">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Detalles del Ingreso</h2>
            <div class="uk-overflow-auto">
                <table class="uk-table uk-table-divider uk-table-small uk-table-striped">
                    <thead><tr><th>Producto</th><th>Cantidad</th><th>Subtotal</th></tr></thead>
                    <tbody id="tbodyDetallesIngreso"></tbody>
                </table>
            </div>
            <div class="uk-text-right"><strong id="totalDetalleIngreso"></strong></div>
        </div>
    </div>

    <!-- Modal Detalles Venta -->
    <div id="modalDetallesVenta" uk-modal>
        <div class="uk-modal-dialog uk-modal-body uk-width-1-2@m">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Detalles de la Venta</h2>
            <div class="uk-overflow-auto">
                <table class="uk-table uk-table-divider uk-table-small uk-table-striped">
                    <thead><tr><th>Producto</th><th>Cantidad</th><th>Subtotal</th></tr></thead>
                    <tbody id="tbodyDetallesVenta"></tbody>
                </table>
            </div>
            <div class="uk-text-right"><strong id="totalDetallesVenta"></strong></div>
        </div>
    </div>

    <!-- Modal Nuevo Producto -->
    <div id="modalNuevo" uk-modal>
        <div class="uk-modal-dialog uk-modal-body uk-width-2xlarge">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Nuevo Producto</h2>
            <form method="POST" action="?c=CProductos/add" enctype="multipart/form-data">
                <div class="uk-grid-small uk-child-width-1-2@m" uk-grid>
                    <div>
                        <label class="uk-form-label">Nombre</label>
                        <div class="uk-form-controls">
                            <input type="text" name="nombre" class="uk-input" required>
                        </div>
                    </div>
                    <div>
                        <label class="uk-form-label">Marca</label>
                        <div class="uk-form-controls">
                            <input type="text" name="marca" class="uk-input">
                        </div>
                    </div>
                    <div>
                        <label class="uk-form-label">Precio ($)</label>
                        <div class="uk-form-controls">
                            <input type="text" name="precio" class="uk-input input-price" required>
                        </div>
                    </div>
                    <div>
                        <label class="uk-form-label">Categoría</label>
                        <div class="uk-form-controls">
                            <select name="id_categoria" class="uk-select select-categoria" required>
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
                    </div>
                    <div>
                        <label class="uk-form-label">Stock Mínimo</label>
                        <div class="uk-form-controls">
                            <input type="number" name="stock_minimo" class="uk-input" required>
                        </div>
                    </div>
                    <div>
                        <label class="uk-form-label">Stock Máximo</label>
                        <div class="uk-form-controls">
                            <input type="number" name="stock_maximo" class="uk-input" required>
                        </div>
                    </div>
                    <div class="uk-width-1-1">
                        <label class="uk-form-label">Imagen</label>
                        <div class="uk-form-controls">
                            <input type="file" name="imagen" class="uk-input" onchange="previewImagen(this, 'previewNuevo')">
                            <img id="previewNuevo" src="" alt="Vista previa" class="uk-border-rounded uk-margin-small-top" width="100" style="display:none;">
                        </div>
                    </div>
                    <div class="uk-width-1-1">
                        <label class="uk-form-label">Descripción</label>
                        <div class="uk-form-controls">
                            <textarea name="descripcion" class="uk-textarea" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="uk-text-right uk-margin-top">
                    <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                    <button type="submit" class="uk-button uk-button-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Editar Producto -->
    <div id="modalEditar" uk-modal>
        <div class="uk-modal-dialog uk-modal-body uk-width-2xlarge">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Editar Producto</h2>
            <form method="POST" action="?c=CProductos/edit" enctype="multipart/form-data">
                <input type="hidden" name="id" id="editId">
                <div class="uk-grid-small uk-child-width-1-2@m" uk-grid>
                    <div>
                        <label class="uk-form-label">Nombre</label>
                        <div class="uk-form-controls">
                            <input name="nombre" id="editNombre" class="uk-input" required>
                        </div>
                    </div>
                    <div>
                        <label class="uk-form-label">Marca</label>
                        <div class="uk-form-controls">
                            <input name="marca" id="editMarca" class="uk-input">
                        </div>
                    </div>
                    <div>
                        <label class="uk-form-label">Precio ($)</label>
                        <div class="uk-form-controls">
                            <input type="text" name="precio" id="editPrecio" class="uk-input input-price" required>
                        </div>
                    </div>
                    <div>
                        <label class="uk-form-label">Categoría</label>
                        <div class="uk-form-controls">
                            <select name="id_categoria" id="editCategoria" class="uk-select">
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="uk-form-label">Stock Mínimo</label>
                        <div class="uk-form-controls">
                            <input type="number" name="stock_minimo" id="editStockMinimo" class="uk-input" required>
                        </div>
                    </div>
                    <div>
                        <label class="uk-form-label">Stock Máximo</label>
                        <div class="uk-form-controls">
                            <input type="number" name="stock_maximo" id="editStockMaximo" class="uk-input" required>
                        </div>
                    </div>
                    <div class="uk-width-1-1">
                        <label class="uk-form-label">Imagen</label>
                        <div class="uk-form-controls">
                            <input type="file" name="imagen" class="uk-input" onchange="previewImagen(this, 'previewEditar')">
                            <input type="hidden" name="imagen" id="editImagenHidden">
                            <img id="previewEditar" src="" alt="Preview" class="uk-border-rounded uk-margin-small-top" width="100">
                        </div>
                    </div>
                    <div class="uk-width-1-1">
                        <label class="uk-form-label">Descripción</label>
                        <div class="uk-form-controls">
                            <textarea name="descripcion" id="editDescripcion" class="uk-textarea" rows="2"></textarea>
                        </div>
                    </div>
                    <div>
                        <label class="uk-form-label">Estado</label>
                        <div class="uk-form-controls">
                            <select name="estado" id="editEstado" class="uk-select">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="uk-text-right uk-margin-top">
                    <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                    <button type="submit" class="uk-button uk-button-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    <?php foreach ($productos as $p): ?>
        <div id="modalIngresoRapido_<?= $p['id'] ?>" uk-modal>
            <div class="uk-modal-dialog uk-modal-body uk-width-2xlarge">
                <button class="uk-modal-close-default" type="button" uk-close></button>
                <h2 class="uk-modal-title">Entrada Rápida - <?= htmlspecialchars($p['nombre']) ?></h2>
                <form method="POST" action="?c=CIngresos/add">
                    <input type="hidden" name="id_usuario" value="<?= $_SESSION['id'] ?>">
                    <input type="hidden" name="id_producto" value="<?= $p['id'] ?>">
                    <div class="uk-grid-small uk-child-width-1-2@m" uk-grid>
                        <div>
                            <label class="uk-form-label">Cantidad</label>
                            <div class="uk-form-controls">
                                <input type="number" name="cantidad" class="uk-input" min="1" required>
                            </div>
                        </div>
                        <div>
                            <label class="uk-form-label">Precio de Compra ($)</label>
                            <div class="uk-form-controls">
                                <input type="text" name="precio" class="uk-input precio input-price" required>
                            </div>
                        </div>
                        <div class="uk-width-1-1">
                            <label class="uk-form-label">Precio de Compra (Bs)</label>
                            <div class="uk-form-controls">
                                <input type="text" name="precio_bs" class="uk-input input-price precio_bs">
                            </div>
                        </div>
                        <div class="uk-width-1-1">
                            <div class="uk-alert-primary" uk-alert>
                                <p><strong>Nota:</strong> Este ingreso se registrará con el usuario actual y la fecha actual.</p>
                            </div>
                        </div>
                    </div>
                    <div class="uk-text-right uk-margin-top">
                        <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                        <button type="submit" class="uk-button uk-button-primary">Registrar</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="modalVentaRapida_<?= $p['id'] ?>"
             uk-modal
             data-id-producto="<?= $p['id'] ?>"
             data-stock="<?= (int)$p['stock'] ?>">
            <div class="uk-modal-dialog uk-modal-body uk-width-2xlarge">
                <button class="uk-modal-close-default" type="button" uk-close></button>
                <h2 class="uk-modal-title">Venta Rápida - <?= htmlspecialchars($p['nombre']) ?></h2>
                <form class="form-venta-rapida" method="POST" action="?c=CVentas/add">
                    <input type="hidden" name="id_usuario" value="<?= $_SESSION['id'] ?>">
                    <input type="hidden" name="id_producto" value="<?= $p['id'] ?>">
                    <div class="uk-margin">
                        <div class="uk-alert-primary" uk-alert>
                            <p>Stock disponible: <strong><?= (int)$p['stock'] ?></strong> unidad(es)</p>
                        </div>
                        <div class="uk-alert-danger stock-error-msg uk-hidden" role="alert"></div>
                    </div>
                    <div class="uk-grid-small uk-child-width-1-2@m" uk-grid>
                        <div>
                            <label class="uk-form-label">Cantidad</label>
                            <div class="uk-form-controls">
                                <input type="number" name="cantidad" class="uk-input cantidad-venta-rapida" min="1" value="1" max="<?= (int)$p['stock'] ?>" data-stock="<?= (int)$p['stock'] ?>" required>
                            </div>
                        </div>
                        <div>
                            <label class="uk-form-label">Precio ($)</label>
                            <div class="uk-form-controls">
                                <input type="text" name="precio" class="uk-input" value="<?= $p['precio'] ?>" readonly>
                            </div>
                        </div>
                        <div class="uk-width-1-1">
                            <label class="uk-form-label">Precio (Bs)</label>
                            <div class="uk-form-controls">
                                <input type="text" name="precio_bs" class="uk-input" value="<?= htmlspecialchars($p['precio'] * $tasaValor) ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="uk-alert-primary uk-margin-top" uk-alert>
                        <p><strong>Nota:</strong> Esta venta se registrará con el usuario actual y la fecha actual.</p>
                    </div>
                    <div class="uk-text-right uk-margin-top">
                        <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                        <button type="submit" class="uk-button uk-button-primary btn-submit-venta-rapida">Registrar Venta</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>

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

    const productosData = <?= json_encode($productos, JSON_UNESCAPED_UNICODE) ?>;
    let productosView = localStorage.getItem('productosView') === 'rows' ? 'rows' : 'cards';
    const pageSizeOptions = [6, 12, 24, 48];
    const savedPageSize = Number(localStorage.getItem('productosPageSize'));
    let productosPageSize = pageSizeOptions.includes(savedPageSize) ? savedPageSize : 6;
    let productosSortDirection = 1;

    function getProductoBadge(product) {
        if (Number(product.stock) === 0) {
            return '<span class="uk-badge uk-border-rounded" style="background:#ef4444;color:#fff;">Sin stock</span>';
        }
        if (Number(product.stock) <= Number(product.stock_minimo)) {
            return '<span class="uk-badge uk-border-rounded" style="background:#f59e0b;color:#111827;">Bajo stock</span>';
        }
        return '<span class="uk-badge uk-border-rounded" style="background:#22c55e;color:#fff;">Activo</span>';
    }

    function renderProductosList() {
        const listEl = document.getElementById('productosList');
        const emptyEl = document.getElementById('productosEmpty');
        const paginationEl = document.getElementById('productosPagination');
        if (!listEl) return;

        const searchInput = document.getElementById('productosSearch');
        const query = (searchInput ? searchInput.value : '').trim().toLowerCase();
        const sorted = [...productosData].sort((a, b) => a.nombre.localeCompare(b.nombre, 'es', { sensitivity: 'base' }) * productosSortDirection);
        const filtered = sorted.filter((p) => {
            const hayTexto = [p.nombre, p.marca, p.categoria || '', p.descripcion || ''].join(' ').toLowerCase();
            return !query || hayTexto.includes(query);
        });

        const pageSize = productosPageSize;
        const totalPages = Math.max(1, Math.ceil(filtered.length / pageSize));
        const currentPage = Math.min(Number(listEl.dataset.page || 1), totalPages);
        const start = (currentPage - 1) * pageSize;
        const pageItems = filtered.slice(start, start + pageSize);
        const isCardView = productosView === 'cards';
        listEl.classList.toggle('uk-grid', isCardView);
        listEl.classList.toggle('uk-grid-medium', isCardView);
        listEl.classList.toggle('uk-flex-center', isCardView);
        listEl.classList.toggle('app-product-list-rows', !isCardView);

        document.querySelectorAll('[data-product-view]').forEach((button) => {
            const active = button.dataset.productView === productosView;
            button.classList.toggle('uk-button-primary', active);
            button.classList.toggle('uk-button-default', !active);
            button.setAttribute('aria-pressed', String(active));
        });

        if (!pageItems.length) {
            listEl.innerHTML = '';
            emptyEl.classList.remove('uk-hidden');
            paginationEl.innerHTML = '';
            return;
        }

        emptyEl.classList.add('uk-hidden');

        if (productosView === 'rows') {
            listEl.innerHTML = `
                <div class="uk-overflow-auto app-product-table-wrap">
                    <table class="uk-table uk-table-divider uk-table-hover uk-table-middle app-product-table">
                        <thead>
                            <tr><th>Producto</th><th>Categoría</th><th>Estado</th><th>Precio</th><th>Stock</th><th class="uk-text-right">Acciones</th></tr>
                        </thead>
                        <tbody>${pageItems.map((p) => {
                            const precio = Number(p.precio || 0);
                            const stock = Number(p.stock || 0);
                            const categoria = p.categoria || 'Sin categoría';
                            const imagen = p.imagen ? `public/assets/img/products/${p.imagen}` : 'public/assets/img/products/generic.png';
                            return `
                                <tr>
                                    <td>
                                        <div class="app-product-row-identity">
                                            <img src="${imagen}" alt="" onerror="this.onerror=null;this.src='public/assets/img/products/generic.png';">
                                            <div><strong>${p.nombre}</strong><span>${p.marca || 'Sin marca'}</span></div>
                                        </div>
                                    </td>
                                    <td>${categoria}</td>
                                    <td>${getProductoBadge(p)}</td>
                                    <td>${precio.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} $</td>
                                    <td>${stock}</td>
                                    <td>
                                        <div class="app-product-row-actions">
                                            <button class="app-icon-action" type="button" title="Editar" aria-label="Editar" onclick="editarProducto(${JSON.stringify(p).replace(/"/g, '&quot;')})"><span uk-icon="icon: file-edit; ratio: 0.9"></span></button>
                                            <button class="app-icon-action delete" type="button" title="Eliminar" aria-label="Eliminar" uk-toggle="target: #modalConfirmarEliminar${p.id}"><span uk-icon="icon: trash; ratio: 0.9"></span></button>
                                            <button class="uk-button uk-button-primary uk-button-small" type="button" uk-toggle="target: #modalIngresoRapido_${p.id}">Entrada</button>
                                            <button class="uk-button uk-button-default uk-button-small" type="button" uk-toggle="target: #modalVentaRapida_${p.id}">Venta</button>
                                        </div>
                                    </td>
                                </tr>`;
                        }).join('')}</tbody>
                    </table>
                </div>`;
        } else {
            listEl.innerHTML = pageItems.map((p) => {
            const precio = Number(p.precio || 0);
            const stock = Number(p.stock || 0);
            const categoria = p.categoria || 'Sin categoría';
            const imagen = p.imagen ? `public/assets/img/products/${p.imagen}` : 'public/assets/img/products/generic.png';
            const badge = getProductoBadge(p);
            return `
                <div class="uk-width-1-1 uk-width-1-2@l uk-width-1-3@xl">
                    <div class="uk-card uk-card-default app-product-card">
                        <div class="uk-card-body">
                            <div class="app-product-visual">
                                <img src="${imagen}" alt="${p.nombre}" onerror="this.onerror=null;this.src='public/assets/img/products/generic.png';">
                            </div>
                            <h5 class="app-product-name">${p.nombre}</h5>
                            <div class="app-product-head">
                                <div>
                                    <h4 class="app-product-title">${p.marca || 'Sin marca'}</h4>
                                </div>
                                <div class="app-product-actions">
                                    <button class="app-icon-action" type="button" title="Editar" aria-label="Editar" onclick="editarProducto(${JSON.stringify(p).replace(/"/g, '&quot;')})">
                                        <span uk-icon="icon: file-edit; ratio: 0.9"></span>
                                    </button>
                                    <button class="app-icon-action delete" type="button" title="Eliminar" aria-label="Eliminar" uk-toggle="target: #modalConfirmarEliminar${p.id}">
                                        <span uk-icon="icon: trash; ratio: 0.9"></span>
                                    </button>
                                </div>
                            </div>
                            <hr class="app-product-divider">
                            <div class="app-product-meta">
                                <p class="app-meta-label">Estado</p>
                                <p class="app-meta-value">${badge}</p>
                            </div>
                            <div class="app-product-meta">
                                <p class="app-meta-label">Precio</p>
                                <p class="app-meta-value">${precio.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} $</p>
                            </div>
                            <div class="app-product-meta">
                                <p class="app-meta-label">Stock</p>
                                <p class="app-meta-value">${stock}</p>
                            </div>
                            <div class="app-product-meta">
                                <p class="app-meta-label">Categoría</p>
                                <p class="app-meta-value">${categoria}</p>
                            </div>
                            <div class="app-product-actions uk-margin-top" style="justify-content:flex-end;">
                                <button class="uk-button uk-button-primary uk-button-small" type="button" uk-toggle="target: #modalIngresoRapido_${p.id}">Entrada</button>
                                <button class="uk-button uk-button-default uk-button-small" type="button" uk-toggle="target: #modalVentaRapida_${p.id}">Venta</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            }).join('');
        }

        paginationEl.innerHTML = `
            <li class="${currentPage === 1 ? 'uk-disabled' : ''}"><a href="#" data-page="1" uk-pagination-prev>«</a></li>
            <li class="${currentPage === 1 ? 'uk-disabled' : ''}"><a href="#" data-page="${Math.max(1, currentPage - 1)}">Anterior</a></li>
            <li class="uk-active"><span>${currentPage}</span></li>
            <li class="${currentPage >= totalPages ? 'uk-disabled' : ''}"><a href="#" data-page="${Math.min(totalPages, currentPage + 1)}">Siguiente</a></li>
            <li class="${currentPage >= totalPages ? 'uk-disabled' : ''}"><a href="#" data-page="${totalPages}" uk-pagination-next>»</a></li>
        `;

        paginationEl.querySelectorAll('a[data-page]').forEach((anchor) => {
            anchor.addEventListener('click', function (event) {
                event.preventDefault();
                const nextPage = Number(this.dataset.page || 1);
                if (Number.isNaN(nextPage) || nextPage < 1 || nextPage > totalPages) return;
                listEl.dataset.page = nextPage;
                renderProductosList();
            });
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        const hash = window.location.hash;
        if (hash) {
            const tabId = hash.replace('#', '');
            const tabTrigger = document.getElementById(`${tabId}-tab`);
            if (tabTrigger) showTabFromTrigger(tabTrigger);
        }

        const searchInput = document.getElementById('productosSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                document.getElementById('productosList').dataset.page = '1';
                renderProductosList();
            });
        }

        document.querySelectorAll('[data-product-view]').forEach((button) => {
            button.addEventListener('click', function () {
                productosView = this.dataset.productView;
                localStorage.setItem('productosView', productosView);
                document.getElementById('productosList').dataset.page = '1';
                renderProductosList();
            });
        });

        const pageSizeSelect = document.getElementById('productosPageSize');
        if (pageSizeSelect) {
            pageSizeSelect.value = String(productosPageSize);
            pageSizeSelect.addEventListener('change', function () {
                const selectedPageSize = Number(this.value);
                if (!pageSizeOptions.includes(selectedPageSize)) return;

                productosPageSize = selectedPageSize;
                localStorage.setItem('productosPageSize', String(productosPageSize));
                document.getElementById('productosList').dataset.page = '1';
                renderProductosList();
            });
        }

        document.querySelectorAll('.app-item-sort .uk-icon-button').forEach((button) => {
            button.addEventListener('click', function () {
                productosSortDirection = this.dataset.sort === 'asc' ? 1 : -1;
                document.getElementById('productosList').dataset.page = '1';
                renderProductosList();
            });
        });

        renderProductosList();
        cargarNombresProductosVentas();
        initVentaRapidaValidacion();
        initVentaMultipleValidacion();
        
        $('[id^="modalIngresoRapido"][uk-modal] .precio, [id^="modalIngresoRapido"][uk-modal] .precio_bs').each(function() {
            formatearInputPrecio(this);
        });

        $('[id^="modalVentaRapida"][uk-modal] .precio, [id^="modalVentaRapida"][uk-modal] .precio_bs').each(function() {
            formatearInputPrecio(this);
        });

        document.querySelectorAll('.toast.show').forEach(function(el) {
            setTimeout(() => {
                hideToastElement(el);
            }, 4000);
        });
    });

    const modalNuevo = document.getElementById('modalNuevo');
    if (modalNuevo && window.UIkit && UIkit.util) {
        UIkit.util.on(modalNuevo, 'shown', function () {
            $('.select-categoria').select2({
                dropdownParent: $('#modalNuevo .uk-modal-dialog'),
                placeholder: 'Seleccione una categoría',
                allowClear: true
            });
        });
    }

    // ═══════════════════════════════════════════════════════════════════════
    // VALIDACIÓN VENTA RÁPIDA (CORREGIDA)
    // ═══════════════════════════════════════════════════════════════════════
    function initVentaRapidaValidacion() {
        // Para cada modal de venta rápida
        document.querySelectorAll('[id^="modalVentaRapida"][uk-modal]').forEach(function(modal) {
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
        openModalElement('#modalEditar');
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
    $(document).on('keyup', '[id^="modalIngresoRapido"][uk-modal] .precio', function() {
        const modal = $(this).closest('[uk-modal]');
        const v = parseMoneda($(this).val());
        modal.find('.precio_bs').val(formatMoneda(v * tasa));
    });

    $(document).on('keyup', '[id^="modalIngresoRapido"][uk-modal] .precio_bs', function() {
        const modal = $(this).closest('[uk-modal]');
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

    // ── Modales de detalles con UIkit ────────────────────────────────────
    function cargarDetallesVenta(idVenta) {
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
    }

    function cargarDetallesIngreso(idIngreso) {
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
    }

    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('[uk-toggle]');
        if (!trigger) return;

        const ukToggleValue = trigger.getAttribute('uk-toggle') || '';
        const target = ukToggleValue.match(/#([A-Za-z0-9_-]+)/);
        if (!target) return;

        const modalId = target[1];
        const modal = document.getElementById(modalId);
        if (!modal) return;

        const id = trigger.dataset.id;
        if (modalId === 'modalDetallesVenta' && id) {
            cargarDetallesVenta(id);
        }
        if (modalId === 'modalDetallesIngreso' && id) {
            cargarDetallesIngreso(id);
        }
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
        nuevaFila.find('.select-producto').select2({ dropdownParent: $('#modalNuevoIngreso .uk-modal-dialog') });
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
        nuevaFila.find('.select-producto').select2({ dropdownParent: $('#modalNuevaVenta .uk-modal-dialog') });
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