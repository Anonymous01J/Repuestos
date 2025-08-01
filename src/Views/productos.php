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
            <!-- Tabs de Bootstrap -->
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
                <div class="tab-pane fade show active" id="productos" role="tabpanel" aria-labelledby="productos-tab">
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
                                                        <button 
                                                        class="accordion-button collapsed p-2" 
                                                        type="button" 
                                                        data-bs-toggle="collapse" 
                                                        data-bs-target="#collapseAcciones_<?= $p['id'] ?>" 
                                                        aria-expanded="false" 
                                                        aria-controls="collapseAcciones_<?= $p['id'] ?>">
                                                        
                                                        </button>
                                                <div 
                                                    id="collapseAcciones_<?= $p['id'] ?>" 
                                                    class="accordion-collapse collapse" 
                                                    aria-labelledby="headingAcciones_<?= $p['id'] ?>" 
                                                    data-bs-parent="#accordionAcciones_<?= $p['id'] ?>">
                                                    <div class="accordion-body p-2">
                                                    <div class="d-flex gap-1">
                                                        <button 
                                                        class="btn btn-sm btn-outline-warning" 
                                                        onclick="editarProducto(<?= htmlspecialchars(json_encode($p)) ?>)">
                                                        Editar
                                                        </button>
                                                        <button 
                                                        class="btn btn-sm btn-outline-success" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalIngresoRapido_<?= $p['id'] ?>" 
                                                        data-idProducto="<?= $p['id'] ?>">
                                                        Entrada Rápida
                                                        </button>
                                                        <button 
                                                        class="btn btn-sm btn-outline-success" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalVentaRapida_<?= $p['id'] ?>" 
                                                        data-idProducto="<?= $p['id'] ?>" 
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
                                    <div class="modal fade modalIngresoRapido" id="modalIngresoRapido_<?= $p['id'] ?>" tabindex="-1">
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
                                                    <label for="cantidad" class="form-label">Cantidad</label>
                                                    <input type="number" name="cantidad" id="cantidad" class="form-control cantidad-input" min="1" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="precio" class="form-label">Precio de Compra ($)</label>
                                                    <input type="text" step="0.01" name="precio" id="precio" class="form-control  precio input-price" min="0.01" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="precio_bs" class="form-label">Precio de Compra (Bs)</label>
                                                    <input type="text" step="0.01" name="precio_bs" id="precio_bs" class="form-control input-price precio_bs">
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
                                    <!-- Modal Venta Rápida -->
                                    <div class="modal fade modalVentaRapida" id="modalVentaRapida_<?= $p['id'] ?>" tabindex="-1">
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
                                                        <label for="cantidad" class="form-label">Cantidad</label>
                                                        <input type="number" name="cantidad" id="cantidad" class="form-control" min="1" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="precio" class="form-label">Precio ($)</label>
                                                        <input type="text" name="precio" id="precio" class="form-control" value="<?= $p['precio'] ?>" readonly>
                                                    </div>
                                                </div>
                                                <div class="modal-body row g-3">
                                                    <div class="col-md-6">
                                                        <label for="precio" class="form-label">Precio (BS)</label>
                                                        <input type="text" name="precio" id="precio" class="form-control precio-input" value="<?= htmlspecialchars($p['precio'] * $tasaValor) ?>" readonly>
                                                    </div>
                                                </div>
                                                <div class="alert alert-info">
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
                <div class="tab-pane fade" id="entradas" role="tabpanel" aria-labelledby="entradas-tab">
                    <div class="d-flex justify-content-between mb-3">
                        <h5>Entradas Registrados</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table-DT table table-striped" id="tablaEntradas" data-order='[[ 0, "desc" ]]'>
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
                                        <td><?= htmlspecialchars($i['proveedor']) ?></td>
                                        <td><?= htmlspecialchars($i['usuario']) ?></td>
                                        <td><?= $i['fecha'] ?></td>
                                        <td>
                                            <?= htmlspecialchars($i['total']) ?> $ /
                                            <?= htmlspecialchars($i['total_bs']) ?> Bs
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalDetallesIngreso"
                                                data-id="<?= $i['id'] ?>">
                                                    Ver
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Ventas -->
                <div class="tab-pane fade" id="ventas" role="tabpanel" aria-labelledby="ventas-tab">
                    <div class="d-flex justify-content-between mb-3">
                        <h5>Ventas Registradas</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table-DT table table-striped" id="tablaVentas" data-order='[[ 0, "desc" ]]'>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Usuario</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Detalles</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ventas as $v): ?>
                                    <tr>
                                        <td><?= $v['id'] ?></td>
                                        <td><?= htmlspecialchars($v['id_cliente']) ?></td>
                                        <td><?= htmlspecialchars($v['id_usuario']) ?></td>
                                        <td><?= $v['fecha'] ?></td>
                                        <td>
                                            <?= htmlspecialchars($v['total']) ?> $ /
                                            <?= htmlspecialchars($v['total_bs']) ?> Bs
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalDetallesVenta"
                                                data-id="<?= $v['id'] ?>">
                                                    Ver
                                            </button>
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

    <!-- Modal de Registro de Ingreso Múltiple -->
    <div class="modal fade" id="modalNuevoIngreso" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
            <form method="POST" action="?c=CIngresos/addMany">
                <input type="hidden" name="id_usuario" value="<?= $_SESSION['id'] ?>">
                <div class="modal-header">
                <h5 class="modal-title">Registrar Nuevo Ingreso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row g-3">
                <div class="col-12">
                    <h6>Productos a Ingresar</h6>
                    <table class="table table-striped table-bordered" id="tablaProductosIngreso">
                    <thead>
                        <tr>
                        <th>Producto</th>
                        <th class="col-sm-2">Cantidad</th>
                        <th class="col-sm-2">Precio ($)</th>
                        <th class="col-sm-2">Precio (Bs)</th>
                        <th class="col-sm-2">Subtotal</th>
                        <th class="col-sm-2">Subtotal (Bs)</th>
                        <th></th>
                        </tr>
                    </thead>
                    <tbody id="tbodyIngreso">
                        <!-- Filas dinámicas -->
                    </tbody>
                    </table>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="agregarFilaIngreso()">+ Añadir Producto</button>
                </div>

                <div class="col-md-6 offset-md-6 text-end">
                    <strong id="totalResumen">Total: 0.00 $ / 0.00 Bs</strong>
                    <input type="text" class="input-price" name="total" id="total">
                    <input type="text" class="input-price" name="total_bs" id="total_bs">
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
    <!-- Modal de Registro de Venta Múltiple -->
    <div class="modal fade" id="modalNuevaVenta" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
            <form method="POST" action="?c=CVentas/addMany">
                <input type="hidden" name="id_usuario" value="<?= $_SESSION['id'] ?>">
                <div class="modal-header">
                <h5 class="modal-title">Registrar Nueva Venta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row g-3">
                <div class="col-12">
                    <h6>Productos a Vender</h6>
                    <table class="table table-striped table-bordered" id="tablaProductosVenta">
                    <thead>
                        <tr>
                        <th>Producto</th>
                        <th class="col-sm-2">Cantidad</th>
                        <th class="col-sm-2">Precio Unitario($)</th>
                        <th class="col-sm-2">Precio Unitario(Bs)</th>
                        <th class="col-sm-2">Subtotal</th>
                        <th class="col-sm-2">Subtotal (Bs)</th>
                        <th></th>
                        </tr>
                    </thead>
                    <tbody id="tbodyVenta">
                        <!-- Filas dinámicas -->
                    </tbody>
                    </table>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="agregarFilaVenta()">+ Añadir Producto</button>
                </div>

                <div class="col-md-6 offset-md-6 text-end">
                    <strong id="totalResumen">Total: 0.00 $ / 0.00 Bs</strong>
                    <input type="text" class="input-price" name="total" id="total">
                    <input type="text" class="input-price" name="total_bs" id="total_bs">
                </div>
                </div>

                <div class="modal-footer">
                <button type="submit" class="btn btn-success">Guardar Venta</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <!-- Modal Ver Detalles Ingreso -->
    <div class="modal fade" id="modalDetallesIngreso" tabindex="-1" aria-labelledby="modalDetallesIngresoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetallesIngresoLabel">Detalles del Ingreso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="tableDetails table table-striped table-bordered">
                    <thead>
                        <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyDetallesIngreso">
                        <!-- Aquí se cargan dinámicamente los detalles -->
                    </tbody>
                    </table>
                    <div class="text-end">
                    <strong id="totalDetalleIngreso"></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Ver Detalles Venta -->
    <div class="modal fade" id="modalDetallesVenta" tabindex="-1" aria-labelledby="modalDetallesVentaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetallesVentaLabel">Detalles de la Venta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="tableDetails table table-striped table-bordered">
                    <thead>
                        <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyDetallesVenta">
                        <!-- Aquí se cargan dinámicamente los detalles -->
                    </tbody>
                    </table>
                    <div class="text-end">
                    <strong id="totalDetallesVenta"></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal: Nuevo Producto -->
    <div class="modal fade" id="modalNuevo" tabindex="-1" aria-labelledby="modalNuevoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <form method="POST" action="?c=CProductos/add" enctype="multipart/form-data">
                <div class="modal-header">
                <h5 class="modal-title" id="modalNuevoLabel">Nuevo Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                <div class="row g-3">
                    <!-- Nombre -->
                    <div class="col-md-6">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                    </div>

                    <!-- Marca -->
                    <div class="col-md-6">
                    <label for="marca" class="form-label">Marca</label>
                    <input type="text" id="marca" name="marca" class="form-control">
                    </div>

                    <!-- Precio -->
                    <div class="col-md-6">
                    <label for="precio" class="form-label">Precio</label>
                    <input type="number" step="0.01" id="precio" name="precio" class="form-control" required>
                    </div>
                    <!-- Categoría -->
                    <div class="col-md-6">
                    <label for="id_categoria" class="form-label">Categoría</label>
                        <select id="id_categoria" name="id_categoria" class="form-select select-categoria" required>
                            <?php if (!empty($categorias)): ?>
                                <option value="">Seleccione una categoría</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                            <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled selected>⚠️ No hay categorías disponibles. Cree una primero.</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <!-- Stock Mínimo -->
                    <div class="col-md-2">
                    <label for="stock_minimo" class="form-label">Stock Mínimo</label>
                    <input type="number" id="stock_minimo" name="stock_minimo" class="form-control" required>
                    </div>

                    <!-- Stock Máximo -->
                    <div class="col-md-2">
                    <label for="stock_maximo" class="form-label">Stock Máximo</label>
                    <input type="number" id="stock_maximo" name="stock_maximo" class="form-control" required>
                    </div>

                    <!-- Imagen -->
                    <div class="col-md-8">
                    <label for="imagen" class="form-label">Imagen</label>
                    <input type="file" id="imagen" name="imagen" class="form-control" onchange="previewImagen(this, 'previewNuevo')">
                    <img id="previewNuevo" src="" alt="Vista previa" class="img-thumbnail mt-2" width="100" style="display: none;">
                    </div>

                    <!-- Descripción -->
                    <div class="col-12">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea id="descripcion" name="descripcion" class="form-control" rows="2"></textarea>
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
                            <select name="id_categoria" id="editCategoria" class="form-select select-categoria">
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
        // Asume que PHP imprime el valor directamente en el JS
        const tasa = <?= floatval($tasaData[0]['valor']) ?>;

        // Formato de precio con coma decimal y punto de miles
        function formatMoneda(valor) {
        let entero = Math.floor(valor).toString();
        let decimales = Math.round((valor % 1) * 100).toString().padStart(2, '0');
        let miles = entero.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return miles + ',' + decimales;
        }

        // Al escribir en precio en dólares
        $('.precio').on('keyup', function () {
        let valorDolares = parseFloat($(this).val().replace(/\./g, '').replace(/,/g, '.')) || 0;
        let valorBolivares = valorDolares * tasa;
        $('.precio_bs').val(formatMoneda(valorBolivares));
        $('.subtotal').val(formatMoneda(valorDolares));
        $('.subtotal_bs').val(formatMoneda(valorBolivares));
        });

        // Al escribir en precio en bolívares
        $('.precio_bs').on('keyup', function () {
        let valorBolivares = parseFloat($(this).val().replace(/\./g, '').replace(/,/g, '.')) || 0;
        let valorDolares = valorBolivares / tasa;
        $('.precio').val(formatMoneda(valorDolares));
        $('.subtotal_bs').val(formatMoneda(valorBolivares));
        $('.subtotal').val(formatMoneda(valorDolares));
        });
        $(".tableDetails").DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json"
            },
            "paging": false,
            "searching": false,
            "info": false
        });
        $('#modalDetallesVenta').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget); // Botón que abrió el modal
            const idVenta = button.data('id');

            $.ajax({
                type: 'POST',
                url: '?c=CVentas/searchDetails',
                data: { id_venta: idVenta },
                dataType: 'json',
                success: function (data) {
                const tbody = $('#tbodyDetallesVenta');
                tbody.empty(); // Limpiar tabla anterior

                if (Array.isArray(data)) {
                    data.forEach(item => {
                    const fila = `<tr>
                        <td>${item.nombre_producto}</td>
                        <td>${item.cantidad}</td>
                        <td>${item.subtotal} $ / ${item.subtotal_bs} Bs</td>
                    </tr>`;
                    tbody.append(fila);
                    });
                    $('#totalDetallesVenta').text("Total: " + data[0].total_venta + " $ / " + data[0].total_venta_bs + " Bs");
                } else {
                    tbody.append('<tr><td colspan="4" class="text-center">Sin detalles disponibles</td></tr>');
                }
                },
                error: function () {
                $('#tbodyDetallesVenta').html('<tr><td colspan="4" class="text-danger text-center">Error al cargar datos</td></tr>');
                }
            });
        });
        $('#modalDetallesIngreso').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget); // Botón que abrió el modal
            const idIngreso = button.data('id');

            $.ajax({
                type: 'POST',
                url: '?c=CIngresos/searchDetails',
                data: { id_ingreso: idIngreso },
                dataType: 'json',
                success: function (data) {
                const tbody = $('#tbodyDetallesIngreso');
                tbody.empty(); // Limpiar tabla anterior

                if (Array.isArray(data)) {
                    data.forEach(item => {
                    const fila = `<tr>
                        <td>${item.nombre_producto}</td>
                        <td>${item.cantidad}</td>
                        <td>${item.subtotal} $ / ${item.subtotal_bs} Bs</td>
                    </tr>`;
                    tbody.append(fila);
                    });
                    $('#totalDetalleIngreso').text("Total: " + data[0].total_ingreso + " $ / " + data[0].total_ingreso_bs + " Bs");
                } else {
                    tbody.append('<tr><td colspan="4" class="text-center">Sin detalles disponibles</td></tr>');
                }
                },
                error: function () {
                $('#tbodyDetallesIngreso').html('<tr><td colspan="4" class="text-danger text-center">Error al cargar datos</td></tr>');
                }
            });
        });



function agregarFilaIngreso() {
  const fila = `
    <tr>
      <td>
        <select name="productos[]" class="form-select select-producto" required>
          <option value="">Seleccione</option>
          <?php foreach ($productos as $p): ?>
            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </td>
      <td><input type="number" name="cantidades[]" class="form-control cantidad" min="1" value="1" required></td>
      <td><input type="text" name="precio[]" class="form-control precio input-price" required></td>
      <td><input type="text" name="precio_bs[]" class="form-control precio_bs input-price" required></td>
      <td><input type="text" class="form-control subtotal input-price" readonly></td>
      <td><input type="text" class="form-control subtotal_bs input-price" readonly></td>
      <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">X</button></td>
    </tr>
  `;
    $('#tbodyIngreso').append(fila);
    bindEvents(); // Reaplica eventos a nuevos inputs
    // InputPrice(".input-price");

    $('.select-producto').select2({ dropdownParent: $('#modalNuevoIngreso') }); // Reaplica select2
}

function eliminarFila(btn) {
  $(btn).closest('tr').remove();
  actualizarTotales();
}

function bindEvents() {
    // Aplicar tu formateo personalizado
    function InputPrice(input) {
        let inputDom = document.querySelectorAll(input);
        inputDom.forEach((element) => {
            element.addEventListener("input", (e) => {
            let valueUser = e.target.value.replace(/,/g, "").replace(/\./g, "").replace(/[^0-9]/g, "");
            let valueLength = valueUser.length;
            if (valueLength <= 2) {
                element.value = "0" + "," + valueUser;
            } else if (valueLength >= 3) {
                let it = valueUser.slice(0, valueLength - 2) + "," + valueUser.slice(valueLength - 2);
                element.value = it.replace(/^0(?=\d)\,?/, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }
            });
        });
    }
    InputPrice(".input-price");
    function parseMoneda(valor) {
        return parseFloat(valor.replace(/\./g, '').replace(/,/g, '.')) || 0;
    }
    function formatMoneda(valor) {
        let entero = Math.floor(valor).toString();
        let decimales = Math.round((valor % 1) * 100).toString().padStart(2, '0');
        let miles = entero.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return miles + ',' + decimales;
    }
    // Asignar eventos individuales por fila
    $('#tbodyIngreso').find('tr').each(function () {
        const fila = $(this);
        fila.find('.precio').off('keyup').on('keyup', function () {
            // let cantidad = parseFloat(fila.find('.cantidad').val()) || 1;
            let valorDolares = parseMoneda($(this).val());
            let valorBolivares = valorDolares * tasa;
            let subtotal = valorDolares;
            let subtotalBs = valorBolivares;

            fila.find('.precio_bs').val(formatMoneda(valorBolivares));
            fila.find('.subtotal').val(formatMoneda(subtotal));
            fila.find('.subtotal_bs').val(formatMoneda(subtotalBs));

            actualizarTotales();
        });

        fila.find('.precio_bs').off('keyup').on('keyup', function () {
            // let cantidad = parseFloat(fila.find('.cantidad').val()) || 1;
            let valorBolivares = parseMoneda($(this).val());
            let valorDolares = valorBolivares / tasa;
            let subtotal = valorDolares;
            let subtotalBs = valorBolivares;

            fila.find('.precio').val(formatMoneda(valorDolares));
            fila.find('.subtotal').val(formatMoneda(subtotal));
            fila.find('.subtotal_bs').val(formatMoneda(subtotalBs));

            actualizarTotales();
        });

        fila.find('.cantidad').off('input').on('input', function () {
            fila.find('.precio').trigger('keyup'); // recalcula con nueva cantidad
        });
    });

    $('.select-producto').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#modalNuevoIngreso'),
        width: '100%'
    });
        // Asignar eventos individuales por fila
    $('#tbodyVenta').find('tr').each(function () {
        const fila = $(this);
        fila.find('.select-producto').off('change').on('change', function () {
            let precio = parseFloat($(this).find('option:selected').data('precio')) || 0;
            let cantidad = parseFloat(fila.find('.cantidad').val()) || 1;
            let subtotal = cantidad * precio;
            let subtotalBs = subtotal * tasa;
            fila.find('.precio').val(formatMoneda(precio));
            fila.find('.precio_bs').val(formatMoneda(precio * tasa));
            fila.find('.subtotal').val(formatMoneda(subtotal));
            fila.find('.subtotal_bs').val(formatMoneda(subtotalBs));
            actualizarTotales();
        });
        function parseMoneda(valor) {
            return parseFloat(valor.replace(/\./g, '').replace(/,/g, '.')) || 0;
        }
        fila.find('.cantidad').off('input').on('input', function () {
            let cantidad = parseFloat($(this).val()) || 1;
            let precio = parseMoneda(fila.find('.precio').val());

            let subtotal = cantidad * precio;
            let subtotalBs = subtotal * tasa;

            fila.find('.subtotal').val(formatMoneda(subtotal));
            fila.find('.subtotal_bs').val(formatMoneda(subtotalBs));
            actualizarTotales();
        });


        fila.find('.precio_bs').off('keyup').on('keyup', function () {
            // let cantidad = parseFloat(fila.find('.cantidad').val()) || 1;
            let valorBolivares = parseMoneda($(this).val());
            let valorDolares = valorBolivares / tasa;
            let subtotal = valorDolares;
            let subtotalBs = valorBolivares;

            fila.find('.precio').val(formatMoneda(valorDolares));
            fila.find('.subtotal').val(formatMoneda(subtotal));
            fila.find('.subtotal_bs').val(formatMoneda(subtotalBs));

            actualizarTotales();
        });

        fila.find('.cantidad').off('input').on('input', function () {
            fila.find('.precio').trigger('keyup'); // recalcula con nueva cantidad
        });
    });

    $('.select-producto').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#modalNuevaVenta'),
        width: '100%'
    });
}
    function actualizarTotales() {
    let total = 0;
    let total_bs = 0;

    $('#tbodyIngreso tr').each(function () {
        const fila = $(this);
        function parseMoneda(valor) {
            return parseFloat(valor.replace(/\./g, '').replace(/,/g, '.')) || 0;
        }
        let cantidad = parseFloat(fila.find('.cantidad').val()) || 1;
        let precio = parseMoneda(fila.find('.precio').val());
        let subtotal = cantidad * precio;
        let subtotalBs = subtotal * tasa;

        total += subtotal;
        total_bs += subtotalBs;
    });

    $('#totalResumen').text("Total: " + formatMoneda(total) + " $ / " + formatMoneda(total_bs) + " Bs");
    $('#total').val(formatMoneda(total));
    $('#total_bs').val(formatMoneda(total_bs));
}
function agregarFilaVenta() {
  const fila = `
    <tr>
      <td>
        <select name="productos[]" class="form-select select-producto" required>
          <option value="">Seleccione</option>
          <?php foreach ($productos as $p): ?>
            <option value="<?= $p['id'] ?>" data-precio="<?= $p['precio'] ?>">
                <?= htmlspecialchars($p['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </td>
      <td><input type="number" name="cantidades[]" class="form-control cantidad" min="1" value="1" required></td>
      <td><input type="text" name="precio[]" class="form-control precio input-price" required></td>
      <td><input type="text" name="precio_bs[]" class="form-control precio_bs input-price" required></td>
      <td><input type="text" class="form-control subtotal input-price" readonly></td>
      <td><input type="text" class="form-control subtotal_bs input-price" readonly></td>
      <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">X</button></td>
    </tr>
  `;
    $('#tbodyVenta').append(fila);
    bindEvents(); // Reaplica eventos a nuevos inputs
    // InputPrice(".input-price");

    $('.select-producto').select2({ dropdownParent: $('#modalNuevaVenta') }); // Reaplica select2
}

function eliminarFila(btn) {
  $(btn).closest('tr').remove();
  actualizarTotales();
}
// Inicial
$(document).ready(function () {
    bindEvents();
    $('.select-producto').select2({ 
        theme: 'bootstrap-5',
        dropdownParent: $('#modalNuevoIngreso'),
        width: '100%'
     });
    $('.select-producto').select2({ 
        theme: 'bootstrap-5',
        dropdownParent: $('#modalNuevaVenta'),
        width: '100%'
     });
    $('.select-proveedor').each(function () {
        let $this = $(this);
        let modal = $this.closest('.modal');
        $this.select2({
            theme: 'bootstrap-5',
            dropdownParent: modal,
            width: '100%'
        });
    });
    $('.select-categoria').each(function () {
        let $this = $(this);
        let modal = $this.closest('.modal');
        $this.select2({
            theme: 'bootstrap-5',
            dropdownParent: modal,
            width: '100%'
        });
    });
});
    </script>
</body>
</html>
