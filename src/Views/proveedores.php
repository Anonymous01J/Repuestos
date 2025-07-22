<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
    <?php
        require_once __DIR__ . '../complements/head.php';
    ?>
    <?php
        require_once __DIR__ . '../complements/header.php';
    ?>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold">Gestión de Proveedores</h3>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAgregar">+ Nuevo Proveedor</button>
        </div>

        <!-- Tabs de Bootstrap -->
        <ul class="nav nav-tabs mb-3" id="proveedorTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="activos-tab" data-bs-toggle="tab" data-bs-target="#activos" type="button">Activos</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="inactivos-tab" data-bs-toggle="tab" data-bs-target="#inactivos" type="button">Inactivos</button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Proveedores Activos -->
            <div class="tab-pane fade show active" id="activos">
                <div class="table-responsive">
                    <table id="tablaActivos" class="table-DT table table-striped">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Cédula/RIF</th>
                                <th>Teléfono</th>
                                <th>Dirección</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($proveedores as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p['nombre']) ?></td>
                                    <td><?= htmlspecialchars($p['cedula_rif']) ?></td>
                                    <td><?= htmlspecialchars($p['telefono']) ?></td>
                                    <td><?= htmlspecialchars($p['direccion']) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $p['id'] ?>">Editar</button>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalConfirmarEliminar<?= $p['id'] ?>">Eliminar</button>
                                    </td>
                                </tr>
                                <!-- Modal Editar -->
                                <div class="modal fade" id="modalEditar<?= $p['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="?c=CProveedores/edit" method="POST">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Editar Proveedor</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                                    <div class="mb-3">
                                                        <label>Teléfono</label>
                                                        <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($p['telefono']) ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Dirección</label>
                                                        <textarea name="direccion" class="form-control"><?= htmlspecialchars($p['direccion']) ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal Confirmar Eliminación -->
                                <div class="modal fade" id="modalConfirmarEliminar<?= $p['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="?c=CProveedores/delete" method="POST">
                                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirmar eliminación</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    ¿Estás seguro de que deseas inhabilitar a <?= htmlspecialchars($p['nombre']) ?>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-danger">Eliminar</button>
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

            <!-- Proveedores Inactivos -->
            <div class="tab-pane fade" id="inactivos">
                <div class="table-responsive">
                    <table id="tablaInactivos" class="table-DT table table-striped">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Cédula/RIF</th>
                                <th>Teléfono</th>
                                <th>Dirección</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($proveedoresInactive as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p['nombre']) ?></td>
                                    <td><?= htmlspecialchars($p['cedula_rif']) ?></td>
                                    <td><?= htmlspecialchars($p['telefono']) ?></td>
                                    <td><?= htmlspecialchars($p['direccion']) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $p['id'] ?>">Editar</button>
                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalConfirmarActivar<?= $p['id'] ?>">Activar</button>
                                    </td>
                                </tr>
                                <!-- Modal Editar -->
                                <div class="modal fade" id="modalEditar<?= $p['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="?c=CProveedores/edit" method="POST">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Editar Proveedor</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                                    <div class="mb-3">
                                                        <label>Teléfono</label>
                                                        <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($p['telefono']) ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Dirección</label>
                                                        <textarea name="direccion" class="form-control"><?= htmlspecialchars($p['direccion']) ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal Confirmar Activación -->
                                <div class="modal fade" id="modalConfirmarActivar<?= $p['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="?c=CProveedores/active" method="POST">
                                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirmar activación</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    ¿Estás seguro de que deseas habilitar a <?= htmlspecialchars($p['nombre']) ?>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-success">Activar</button>
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
        </div>
    </div>

    <!-- Modal Agregar Proveedor -->
    <div class="modal fade" id="modalAgregar" tabindex="-1">
        <div class="modal-dialog">
            <form action="?c=CProveedores/add" method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Proveedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Cédula/RIF</label>
                        <input type="text" name="cedula_rif" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Dirección</label>
                        <textarea name="direccion" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    <?php
        require_once __DIR__ . '../complements/footer.php';
    ?>
</body>
</html>
