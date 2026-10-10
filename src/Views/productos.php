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
                    <div class="app-product-table-wrap app-history-table-wrap">
                        <table class="uk-table uk-table-divider uk-table-hover uk-table-middle app-product-table" id="tablaEntradas">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Usuario</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Productos</th>
                                    <th class="uk-text-right">Detalles</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ingresos as $i):
                                    $nombresProductosIngreso = [];
                                    foreach ($detallesIngresos as $di) {
                                        if ($di['id_ingreso'] == $i['id']) {
                                            $prod = array_filter($productos, fn($p) => $p['id'] == $di['id_producto']);
                                            if ($prod) {
                                                $productoIngreso = reset($prod);
                                                $nombreProductoIngreso = $productoIngreso['nombre'];
                                                if (!empty($productoIngreso['codigo_producto'])) {
                                                    $nombreProductoIngreso .= ' · Código: ' . $productoIngreso['codigo_producto'];
                                                }
                                                $nombresProductosIngreso[] = $nombreProductoIngreso;
                                            }
                                        }
                                    }
                                ?>
                                    <tr>
                                        <td><?= $i['id'] ?></td>
                                        <td><?= htmlspecialchars($i['usuario']) ?></td>
                                        <td><?= $i['fecha'] ?></td>
                                        <td><?= htmlspecialchars($i['total']) ?> $ / <?= htmlspecialchars($i['total_bs']) ?> Bs</td>
                                        <td><?= htmlspecialchars(implode(', ', $nombresProductosIngreso)) ?: '—' ?></td>
                                        <td class="uk-text-right">
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
                    <div class="app-product-table-wrap app-history-table-wrap">
                        <table class="uk-table uk-table-divider uk-table-hover uk-table-middle app-product-table" id="tablaVentas">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Usuario</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Productos</th>
                                    <th class="uk-text-right">Detalles</th>
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
                                        <td class="uk-text-right">
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
            <img id="imgGrande" src="" alt="Imagen producto" class="uk-width-1-1 uk-border-rounded" style="max-height: 80vh; object-fit: contain;">
            <p id="imgGrandeTitulo" class="uk-text-meta uk-margin-small-top uk-margin-remove-bottom"></p>
        </div>
    </div>

    <!-- Modal Registro de Ingreso Múltiple -->
    <div id="modalNuevoIngreso" uk-modal>
        <div class="uk-modal-dialog uk-modal-body uk-width-2xlarge app-transaction-modal">
            <button class="uk-modal-close-default" type="button" uk-close></button>

            <div class="app-modal-header">
                <div>
                    <span class="app-modal-kicker">Inventario</span>
                    <h2 class="uk-modal-title">Registrar ingreso</h2>
                </div>
                <span class="app-modal-tag app-modal-tag--success">Stock +</span>
            </div>

            <div class="app-transaction-overview">
                <div class="app-stat-pill">
                    <span>Productos</span>
                    <strong id="ingresoRowsCount">0</strong>
                </div>
                <div class="app-stat-pill app-stat-pill--accent">
                    <span>Total estimado</span>
                    <strong id="ingresoTotalChip">$ 0,00</strong>
                </div>
            </div>

            <form method="POST" action="?c=CIngresos/addMany" class="app-transaction-form">
                <input type="hidden" name="id_usuario" value="<?= $_SESSION['id'] ?>">

                <div class="uk-grid-small uk-child-width-1-2@m uk-margin-bottom" uk-grid>
                    <div class="uk-width-1-1">
                        <label class="uk-form-label">Proveedor</label>
                        <div class="uk-form-controls">
                            <select name="id_proveedor" class="uk-select select-proveedor" data-placeholder="Seleccione un proveedor">
                                <?php if (!empty($proveedores)): ?>
                                    <option value="">Seleccione un proveedor</option>
                                    <?php foreach ($proveedores as $proveedor): ?>
                                        <option value="<?= $proveedor['id'] ?>"><?= htmlspecialchars($proveedor['nombre']) ?><?= !empty($proveedor['cedula_rif']) ? ' · ' . htmlspecialchars($proveedor['cedula_rif']) : '' ?></option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" selected>⚠️ No hay proveedores activos</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="app-modal-toolbar">
                    <div class="app-transaction-meta">
                        <span class="uk-text-meta">Productos a registrar</span>
                    </div>
                    <button type="button" class="uk-button uk-button-primary app-add-row-button" onclick="agregarFilaIngreso()">
                        <span uk-icon="icon: plus; ratio: 0.9"></span>
                        Agregar producto
                    </button>
                </div>

                <div class="app-transaction-table-wrap uk-overflow-auto">
                    <table class="uk-table uk-table-divider uk-table-small uk-table-striped uk-table-middle app-transaction-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio ($)</th>
                                <th>Precio (Bs)</th>
                                <th>Subtotal ($)</th>
                                <th>Subtotal (Bs)</th>
                                <th class="app-row-actions-head">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyIngreso"></tbody>
                    </table>
                </div>

                <div class="app-transaction-summary">
                    <span class="uk-text-meta">Resumen</span>
                    <div id="totalResumen" class="app-transaction-total"></div>
                </div>

                <div class="app-modal-actions uk-text-right">
                    <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                    <button type="submit" class="uk-button uk-button-primary">Guardar ingreso</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Registro de Venta Múltiple (con validación AJAX de stock) -->
    <div id="modalNuevaVenta" uk-modal>
        <div class="uk-modal-dialog uk-modal-body uk-width-2xlarge app-transaction-modal">
            <button class="uk-modal-close-default" type="button" uk-close></button>

            <div class="app-modal-header">
                <div>
                    <span class="app-modal-kicker">Ventas</span>
                    <h2 class="uk-modal-title">Registrar venta</h2>
                </div>
                <span class="app-modal-tag app-modal-tag--warning">Control de stock</span>
            </div>

            <div class="app-transaction-overview">
                <div class="app-stat-pill">
                    <span>Productos</span>
                    <strong id="ventaRowsCount">0</strong>
                </div>
                <div class="app-stat-pill app-stat-pill--accent">
                    <span>Total estimado</span>
                    <strong id="ventaTotalChip">$ 0,00</strong>
                </div>
            </div>

            <form id="formVentaMultiple" method="POST" action="?c=CVentas/addMany" class="app-transaction-form">
                <input type="hidden" name="id_usuario" value="<?= $_SESSION['id'] ?>">

                <div class="app-modal-toolbar">
                    <div class="app-transaction-meta">
                        <div id="stockErrorMultiple" class="uk-alert-danger uk-hidden app-alert-inline" uk-alert role="alert"></div>
                    </div>
                    <button type="button" class="uk-button uk-button-primary app-add-row-button" onclick="agregarFilaVenta()">
                        <span uk-icon="icon: plus; ratio: 0.9"></span>
                        Agregar producto
                    </button>
                </div>

                <div class="app-transaction-table-wrap uk-overflow-auto">
                    <table class="uk-table uk-table-divider uk-table-small uk-table-striped uk-table-middle app-transaction-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio ($)</th>
                                <th>Precio (Bs)</th>
                                <th>Subtotal ($)</th>
                                <th>Subtotal (Bs)</th>
                                <th class="app-row-actions-head">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyVenta"></tbody>
                    </table>
                </div>

                <div class="app-transaction-summary">
                    <span class="uk-text-meta">Resumen</span>
                    <div id="totalResumenVenta" class="app-transaction-total"></div>
                </div>

                <div class="app-modal-actions uk-text-right">
                    <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                    <button type="button" id="btnGuardarVenta" class="uk-button uk-button-primary">Guardar venta</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detalles Ingreso -->
    <div id="modalDetallesIngreso" uk-modal>
        <div class="uk-modal-dialog uk-modal-body uk-width-1-2@m">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Detalles del Ingreso</h2>
            <div class="uk-overflow-auto app-detail-table-wrap">
                <table class="uk-table uk-table-divider uk-table-small uk-table-striped app-detail-table">
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
            <div class="uk-overflow-auto app-detail-table-wrap">
                <table class="uk-table uk-table-divider uk-table-small uk-table-striped app-detail-table">
                    <thead><tr><th>Producto</th><th>Cantidad</th><th>Precio unitario / tarifa</th><th>Subtotal</th></tr></thead>
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
                        <label class="uk-form-label">Código del producto</label>
                        <div class="uk-form-controls">
                            <input type="text" name="codigo_producto" class="uk-input">
                        </div>
                    </div>
                    <div>
                        <label class="uk-form-label">Marca</label>
                        <div class="uk-form-controls">
                            <input type="text" name="marca" class="uk-input">
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
                        <label class="uk-form-label">Stock actual</label>
                        <div class="uk-form-controls">
                            <input type="number" name="stock" class="uk-input" min="0" value="0">
                        </div>
                    </div>
                    <div>
                        <label class="uk-form-label">Stock mínimo</label>
                        <div class="uk-form-controls">
                            <input type="number" name="stock_minimo" class="uk-input" min="0" value="0" required>
                        </div>
                    </div>
                    <div>
                        <label class="uk-form-label">Stock máximo</label>
                        <div class="uk-form-controls">
                            <input type="number" name="stock_maximo" class="uk-input" min="0" value="0" required>
                        </div>
                    </div>
                    <div>
                        <label class="uk-form-label">Precio ($)</label>
                        <div class="uk-form-controls">
                            <input type="text" name="precio" class="uk-input input-price" required>
                        </div>
                    </div>
                    <div>
                        <label class="uk-form-label">Precio al mayor ($)</label>
                        <div class="uk-form-controls">
                            <input type="text" name="precio_al_mayor" class="uk-input input-price">
                        </div>
                    </div>
                    <div>
                        <label class="uk-form-label">Cantidad al mayor</label>
                        <div class="uk-form-controls">
                            <input type="number" name="cantidad_al_mayor" class="uk-input">
                        </div>
                    </div>
                    <div>
                        <label class="uk-form-label">Disponibilidad</label>
                        <div class="uk-form-controls">
                            <select name="disponibilidad" class="uk-select" required>
                                <option value="1" selected>Disponible</option>
                                <option value="0">No disponible</option>
                            </select>
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
                        <label class="uk-form-label">Código del producto</label>
                        <div class="uk-form-controls">
                            <input type="text" name="codigo_producto" id="editCodigoProducto" class="uk-input">
                        </div>
                    </div>
                    <div>
                        <label class="uk-form-label">Marca</label>
                        <div class="uk-form-controls">
                            <input name="marca" id="editMarca" class="uk-input">
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
                        <label class="uk-form-label">Stock actual</label>
                        <div class="uk-form-controls">
                            <input type="number" name="stock" id="editStock" class="uk-input" min="0">
                        </div>
                    </div>
                    <div>
                        <label class="uk-form-label">Stock mínimo</label>
                        <div class="uk-form-controls">
                            <input type="number" name="stock_minimo" id="editStockMinimo" class="uk-input" min="1" required>
                        </div>
                    </div>
                    <div>
                        <label class="uk-form-label">Stock máximo</label>
                        <div class="uk-form-controls">
                            <input type="number" name="stock_maximo" id="editStockMaximo" class="uk-input" required>
                        </div>
                    </div>
                    <div>
                        <label class="uk-form-label">Precio ($)</label>
                        <div class="uk-form-controls">
                            <input type="text" name="precio" id="editPrecio" class="uk-input input-price" required>
                        </div>
                    </div>
                    <div>
                        <label class="uk-form-label">Precio al mayor ($)</label>
                        <div class="uk-form-controls">
                            <input type="text" name="precio_al_mayor" id="editPrecioAlMayor" class="uk-input input-price">
                        </div>
                    </div>
                    <div>
                        <label class="uk-form-label">Cantidad al mayor</label>
                        <div class="uk-form-controls">
                            <input type="number" name="cantidad_al_mayor" id="editCantidadAlMayor" class="uk-input">
                        </div>
                    </div>
                    <div>
                        <label class="uk-form-label">Disponibilidad</label>
                        <div class="uk-form-controls">
                            <select name="disponibilidad" id="editDisponibilidad" class="uk-select">
                                <option value="1">Disponible</option>
                                <option value="0">No disponible</option>
                            </select>
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
                        <div class="uk-width-1-1">
                            <label class="uk-form-label">Proveedor</label>
                            <div class="uk-form-controls">
                                <select name="id_proveedor" class="uk-select select-proveedor-rapida" data-placeholder="Seleccione un proveedor" required>
                                    <?php if (!empty($proveedores)): ?>
                                        <option value="">Seleccione un proveedor</option>
                                        <?php foreach ($proveedores as $proveedor): ?>
                                            <option value="<?= $proveedor['id'] ?>"><?= htmlspecialchars($proveedor['nombre']) ?><?= !empty($proveedor['cedula_rif']) ? ' · ' . htmlspecialchars($proveedor['cedula_rif']) : '' ?></option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" selected>⚠️ No hay proveedores activos</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
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
               data-stock="<?= (int)$p['stock'] ?>"
               data-precio-regular="<?= (float)$p['precio'] ?>"
               data-precio-mayor="<?= $p['precio_al_mayor'] !== null ? (float)$p['precio_al_mayor'] : '' ?>"
               data-cantidad-mayor="<?= $p['cantidad_al_mayor'] !== null ? (float)$p['cantidad_al_mayor'] : '' ?>">
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
                                <input type="text" name="precio" class="uk-input input-price" value="<?= $p['precio'] ?>" required>
                                <input type="hidden" name="precio_al_mayor" class="precio-mayor-flag" value="0">
                                <span class="uk-label uk-label-success precio-mayor-info uk-hidden uk-margin-small-top">Precio al mayor aplicado</span>
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
            const hayTexto = [p.codigo_producto || '', p.nombre, p.marca, p.categoria || '', p.descripcion || ''].join(' ').toLowerCase();
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
                            const codigoProducto = p.codigo_producto || 'Sin código';
                            const imagen = p.imagen ? `public/assets/img/products/${p.imagen}` : 'public/assets/img/products/generic.png';
                            return `
                                <tr>
                                    <td>
                                        <div class="app-product-row-identity">
                                            <button class="app-product-image-trigger" type="button" data-product-image-id="${p.id}" aria-label="Ver imagen completa de ${p.nombre}">
                                                <img src="${imagen}" alt="" onerror="this.onerror=null;this.src='public/assets/img/products/generic.png';">
                                            </button>
                                            <div>
                                                <strong>${p.nombre}</strong>
                                                <div class="uk-text-meta">Código: <strong>${codigoProducto}</strong></div>
                                                <span>${p.marca || 'Sin marca'}</span>
                                            </div>
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
            const codigoProducto = p.codigo_producto || 'Sin código';
            const imagen = p.imagen ? `public/assets/img/products/${p.imagen}` : 'public/assets/img/products/generic.png';
            const badge = getProductoBadge(p);
            return `
                <div class="uk-width-1-1 uk-width-1-2@l uk-width-1-3@xl">
                    <div class="uk-card uk-card-default app-product-card">
                        <div class="uk-card-body">
                            <div class="app-product-visual">
                                <button class="app-product-image-trigger" type="button" data-product-image-id="${p.id}" aria-label="Ver imagen completa de ${p.nombre}">
                                    <img src="${imagen}" alt="${p.nombre}" onerror="this.onerror=null;this.src='public/assets/img/products/generic.png';">
                                </button>
                            </div>
                            <h5 class="app-product-name">${p.nombre}</h5>
                            <div class="app-product-code uk-text-meta uk-margin-small-bottom">Código: <strong>${codigoProducto}</strong></div>
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
                            <div class="app-product-actions app-product-actions--bottom uk-margin-top">
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

        listEl.querySelectorAll('[data-product-image-id]').forEach((imageTrigger) => {
            imageTrigger.addEventListener('click', function () {
                const product = pageItems.find((item) => String(item.id) === this.dataset.productImageId);
                if (product) verImagen(product.imagen || 'generic.png', product.nombre);
            });
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        function activateProductTabFromHash() {
            const rawHash = window.location.hash || '';
            if (!rawHash) return;

            const hashValue = rawHash.replace(/^#/, '').toLowerCase();
            const tabPanelMap = {
                productos: 'productos-tab-panel',
                entradas: 'entradas-tab-panel',
                ventas: 'ventas-tab-panel'
            };

            const normalizedHash = hashValue.replace(/-tab-panel$/, '');
            const panelId = tabPanelMap[normalizedHash] || hashValue;
            const tabTrigger = document.querySelector(`a[href="#${panelId}"]`);

            const tabList = tabTrigger && tabTrigger.closest('ul.uk-tab');
            if (!tabList) return;

            const tabLinks = Array.from(tabList.querySelectorAll('a'));
            const tabIndex = tabLinks.indexOf(tabTrigger);
            if (tabIndex < 0) return;

            if (window.UIkit && typeof UIkit.tab === 'function') {
                UIkit.tab(tabList).show(tabIndex);
            }
        }

        activateProductTabFromHash();
        window.addEventListener('hashchange', activateProductTabFromHash);

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
        
        $('.select-proveedor, .select-proveedor-rapida').each(function() {
            const $select = $(this);
            if ($select.data('select2')) {
                $select.select2('destroy');
            }

            $select.select2({
                dropdownParent: $select.closest('.uk-modal-dialog'),
                width: '100%',
                placeholder: 'Seleccione un proveedor',
                allowClear: true
            });
        });

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
            const precioMayorFlag = modal.querySelector('.precio-mayor-flag');
            
            if (!cantidadInput || !precioInput || !precioBsInput) return;
            
            const precioRegular = parseFloat(modal.dataset.precioRegular) || 0;
            const precioMayor = modal.dataset.precioMayor === '' ? null : parseFloat(modal.dataset.precioMayor);
            const cantidadMayor = parseFloat(modal.dataset.cantidadMayor) || 0;

            function actualizarPreciosPorCantidad() {
                const cantidad = parseFloat(cantidadInput.value) || 1;
                const aplicaPrecioMayor = cantidadMayor > 0
                    && cantidad >= cantidadMayor
                    && precioMayor !== null
                    && Number.isFinite(precioMayor)
                    && precioMayor > 0;
                const precioPropuesto = aplicaPrecioMayor ? precioMayor : precioRegular;

                // Sustituye el valor automáticamente al cruzar el umbral, pero respeta
                // cualquier ajuste manual que el administrador ya haya hecho.
                if (precioInput.dataset.autoPrice !== '0') {
                    precioInput.value = formatMoneda(precioPropuesto);
                    precioInput.dataset.autoPrice = '1';
                }
                precioBsInput.value = formatMoneda(parseMoneda(precioInput.value) * tasa);
                precioMayorFlag.value = aplicaPrecioMayor ? '1' : '0';
                modal.querySelector('.precio-mayor-info').classList.toggle('uk-hidden', !aplicaPrecioMayor);
            }

            precioInput.addEventListener('input', function() {
                const inputPrecio = this;
                requestAnimationFrame(function() {
                    inputPrecio.dataset.autoPrice = '0';
                    precioBsInput.value = formatMoneda(parseMoneda(inputPrecio.value) * tasa);
                });
            });
            
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
                            const nombres = data.map((item) => {
                                const codigo = item.codigo_producto ? ` · Código: ${item.codigo_producto}` : '';
                                return `${item.nombre_producto || ''}${codigo}`;
                            }).join(', ');
                            celda.textContent = nombres;
                            const tabla = $('#tablaVentas').DataTable();
                            tabla.row($(celda).closest('tr')).invalidate().draw(false);
                    } else {
                            celda.textContent = '—';
                    }
                },
                error: function() { 
                        celda.textContent = '—';
                }
            });
        });
    }

    function verImagen(nombre, titulo = 'Producto') {
        const img = document.getElementById('imgGrande');
        img.onerror = function () {
            this.onerror = null;
            this.src = 'public/assets/img/products/generic.png';
        };
        img.src = 'public/assets/img/products/' + encodeURIComponent(nombre || 'generic.png');
        img.alt = 'Imagen de ' + titulo;
        document.getElementById('imgGrandeTitulo').textContent = titulo;
        if (window.UIkit && UIkit.modal) UIkit.modal('#modalImagen').show();
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
        document.getElementById('editNombre').value = data.nombre || '';
        document.getElementById('editCodigoProducto').value = data.codigo_producto || '';
        document.getElementById('editMarca').value = data.marca || '';
        document.getElementById('editPrecio').value = formatMoneda(parseFloat(data.precio || 0));
        document.getElementById('editPrecioAlMayor').value = data.precio_al_mayor ? formatMoneda(parseFloat(data.precio_al_mayor)) : '';
        document.getElementById('editStock').value = data.stock ?? 0;
        document.getElementById('editStockMinimo').value = data.stock_minimo ?? 0;
        document.getElementById('editStockMaximo').value = data.stock_maximo ?? 0;
        document.getElementById('editCantidadAlMayor').value = data.cantidad_al_mayor === null || data.cantidad_al_mayor === undefined || data.cantidad_al_mayor === '' ? '' : data.cantidad_al_mayor;
        document.getElementById('editDescripcion').value = data.descripcion || '';
        document.getElementById('editImagenHidden').value = data.imagen || '';
        document.getElementById('previewEditar').src = 'public/assets/img/products/' + (data.imagen || 'generic.png');
        document.getElementById('editCategoria').value = data.id_categoria;
        document.getElementById('editDisponibilidad').value = String(data.disponibilidad ?? 1);
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
                        const tarifa = Number(item.precio_al_mayor) === 1
                            ? '<span class="uk-label uk-label-success">Mayorista</span>'
                            : '<span class="uk-label">Regular</span>';
                        const nombreConCodigo = item.codigo_producto
                            ? `${item.nombre_producto || ''} · Código: ${item.codigo_producto}`
                            : (item.nombre_producto || '');
                        const fila = $('<tr>');
                        fila.append($('<td>').text(nombreConCodigo));
                        fila.append($('<td>').text(formatMoneda(item.cantidad)));
                        fila.append($('<td>').html(`${formatMoneda(item.precio_unitario)} $<br>${tarifa}`));
                        fila.append($('<td>').text(`${formatMoneda(item.subtotal)} $ / ${formatMoneda(item.subtotal_bs)} Bs`));
                        tbody.append(fila);
                    });
                    $('#totalDetallesVenta').text(
                        `Total: ${formatMoneda(data[0].total_venta)} $ / ${formatMoneda(data[0].total_venta_bs)} Bs`
                    );
                } else {
                    tbody.append('<tr><td colspan="4" class="text-center">Sin detalles</td></tr>');
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
                        const nombreConCodigo = item.codigo_producto
                            ? `${item.nombre_producto || ''} · Código: ${item.codigo_producto}`
                            : (item.nombre_producto || '');
                        const fila = $('<tr>');
                        fila.append($('<td>').text(nombreConCodigo));
                        fila.append($('<td>').text(item.cantidad));
                        fila.append($('<td>').text(`${item.subtotal} $ / ${item.subtotal_bs} Bs`));
                        tbody.append(fila);
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
    function getOptionsProductos(mostrarStock = false) {
        let options = '<option value="">Seleccione</option>';
        <?php foreach ($productos as $p): ?>
            options += `<option value="<?= $p['id'] ?>"
                data-precio="<?= $p['precio'] ?>"
                data-precio-mayor="<?= $p['precio_al_mayor'] !== null ? (float)$p['precio_al_mayor'] : '' ?>"
                data-cantidad-mayor="<?= $p['cantidad_al_mayor'] !== null ? (float)$p['cantidad_al_mayor'] : '' ?>"
                data-stock="<?= floatval($p['stock']) ?>">
                <?= htmlspecialchars($p['nombre'], ENT_QUOTES, 'UTF-8') ?><?= !empty($p['codigo_producto']) ? ' · Código: ' . htmlspecialchars($p['codigo_producto'], ENT_QUOTES, 'UTF-8') : '' ?>${mostrarStock ? ' (<?= floatval($p['stock']) ?>)' : ''}
            </option>`;
        <?php endforeach; ?>
        return options;
    }

    function filaTemplate(mostrarStock = false) {
        return `
        <tr class="app-transaction-row">
            <td data-label="Producto">
                <select name="productos[]" class="form-select select-producto app-transaction-field" required>
                    ${getOptionsProductos(mostrarStock)}
                </select>
            </td>
            <td data-label="Cantidad">
                <input type="number"
                       name="cantidades[]"
                       class="form-control cantidad app-transaction-field"
                       min="1"
                       step=""
                       value="1"
                       required>
            </td>
            <td data-label="Precio ($)">
                <input type="text"
                       name="precio[]"
                       class="form-control precio input-price app-transaction-field"
                      required>
                  <input type="hidden" name="precio_al_mayor[]" class="precio-mayor-flag" value="0">
                <div class="uk-margin-small-top"><span class="uk-label uk-label-success indicador-precio-mayor uk-hidden">Precio al mayor aplicado</span></div>
            </td>
            <td data-label="Precio (Bs)">
                <input type="text"
                       name="precio_bs[]"
                       class="form-control precio_bs input-price app-transaction-field"
                       required>
            </td>
            <td data-label="Subtotal ($)">
                <input type="text"
                       class="form-control subtotal input-price app-transaction-field"
                       readonly>
            </td>
            <td data-label="Subtotal (Bs)">
                <input type="text"
                       class="form-control subtotal_bs input-price app-transaction-field"
                       readonly>
            </td>
            <td data-label="Acción" class="app-row-actions-cell">
                <button type="button" class="uk-button uk-button-danger uk-button-small app-row-delete" onclick="eliminarFila(this)" aria-label="Eliminar fila">
                    <span uk-icon="icon: trash; ratio: 0.8"></span>
                </button>
            </td>
        </tr>`;
    }

    function agregarFilaIngreso() {
        $('#tbodyIngreso').prepend(filaTemplate());
        const nuevaFila = $('#tbodyIngreso tr').first();
        
        // Aplicar formateo a los inputs de precio
        nuevaFila.find('.precio, .precio_bs').each(function() {
            formatearInputPrecio(this);
        });
        
        bindEventosFilaIngreso(nuevaFila);
        nuevaFila.find('.select-producto').select2({
            dropdownParent: $('#modalNuevoIngreso .uk-modal-dialog'),
            width: '100%',
            placeholder: 'Buscar por nombre o código'
        });
        actualizarTotales();
    }

    function agregarFilaVenta() {
        limpiarErrorMultiple();
        $('#tbodyVenta').prepend(filaTemplate(true));
        const nuevaFila = $('#tbodyVenta tr').first();
        
        // Aplicar formateo a los inputs de precio
        nuevaFila.find('.precio, .precio_bs').each(function() {
            formatearInputPrecio(this);
        });
        
        bindEventosFilaVenta(nuevaFila);
        nuevaFila.find('.select-producto').select2({
            dropdownParent: $('#modalNuevaVenta .uk-modal-dialog'),
            width: '100%',
            placeholder: 'Buscar por nombre o código'
        });
        actualizarTotalesVenta();
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
        fila.find('.precio, .precio_bs').prop('readonly', false);

        fila.find('.precio').off('input.precioVenta').on('input.precioVenta', function() {
            $(this).data('precio-editado', true);
            fila.find('.precio_bs').val(formatMoneda(parseMoneda($(this).val()) * tasa));
            recalcularFila(fila);
            actualizarTotalesVenta();
        });

        fila.find('.precio_bs').off('input.precioBsVenta').on('input.precioBsVenta', function() {
            $(this).data('precio-bs-editado', true);
            fila.find('.precio').val(formatMoneda(parseMoneda($(this).val()) / tasa));
            recalcularFila(fila);
            actualizarTotalesVenta();
        });
        
        fila.find('.select-producto').off('change').on('change', function() {
            const option = $(this).find('option:selected');
            const stock = parseFloat(option.data('stock')) || 0;

            fila.find('.precio').data('precio-editado', false);
            fila.find('.precio_bs').data('precio-bs-editado', false);
            
            fila.find('.cantidad')
                .attr('max', stock)
                .attr('placeholder', `Máx: ${formatMoneda(stock)}`);

            actualizarPrecioVentaFila(fila);
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

            actualizarPrecioVentaFila(fila);
            recalcularFila(fila);
            actualizarTotalesVenta();
            limpiarErrorMultiple();
        });
    }

    function actualizarPrecioVentaFila(fila) {
        fila = $(fila);
        const option = fila.find('.select-producto option:selected');
        const precioRegular = parseFloat(option.data('precio')) || 0;
        const precioMayorRaw = option.attr('data-precio-mayor');
        const precioMayor = precioMayorRaw === undefined || precioMayorRaw === '' ? null : parseFloat(precioMayorRaw);
        const cantidadMayor = parseFloat(option.attr('data-cantidad-mayor')) || 0;
        const cantidad = parseFloat(fila.find('.cantidad').val()) || 0;
        const aplicaPrecioMayor = cantidadMayor > 0
            && cantidad >= cantidadMayor
            && precioMayor !== null
            && Number.isFinite(precioMayor)
            && precioMayor > 0;
        const precioPropuesto = aplicaPrecioMayor ? precioMayor : precioRegular;
        const precioInput = fila.find('.precio');
        const precioBsInput = fila.find('.precio_bs');

        if (!precioInput.data('precio-editado')) {
            precioInput.val(formatMoneda(precioPropuesto));
        }
        precioBsInput.val(formatMoneda(parseMoneda(precioInput.val()) * tasa));
        fila.find('.precio-mayor-flag').val(aplicaPrecioMayor ? '1' : '0');
        fila.find('.indicador-precio-mayor').toggleClass('uk-hidden', !aplicaPrecioMayor);
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
        $('#ingresoRowsCount').text($('#tbodyIngreso tr').length);
        $('#ingresoTotalChip').text(`$ ${formatMoneda(total)}`);
    }

    function actualizarTotalesVenta() {
        let total = 0, total_bs = 0;
        $('#tbodyVenta tr').each(function() {
            total += parseMoneda($(this).find('.subtotal').val());
            total_bs += parseMoneda($(this).find('.subtotal_bs').val());
        });
        $('#totalResumenVenta').text(`Total: ${formatMoneda(total)} $ / ${formatMoneda(total_bs)} Bs`);
        $('#ventaRowsCount').text($('#tbodyVenta tr').length);
        $('#ventaTotalChip').text(`$ ${formatMoneda(total)}`);
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