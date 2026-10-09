<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
    <?php
        require_once __DIR__ . '../complements/head.php';
    ?>
    <?php
        require_once __DIR__ . '../complements/header.php';
    ?>

    <div class="uk-container uk-container-large uk-padding-large">
        <div class="uk-flex uk-flex-between uk-flex-middle uk-flex-wrap uk-margin-bottom" style="gap: 12px;">
            <h2 class="uk-heading-bullet uk-margin-remove">Gestión de Proveedores</h2>
            <button class="uk-button uk-button-primary" type="button" data-bs-toggle="modal" data-bs-target="#modalAgregar">+ Nuevo Proveedor</button>
        </div>

        <div class="uk-card uk-card-default uk-card-body">
            <ul class="uk-tab" uk-tab>
                <li class="uk-active"><a href="#">Activos</a></li>
                <li><a href="#">Inactivos</a></li>
            </ul>

            <ul class="uk-switcher uk-margin-top">
                <li>
                    <div class="uk-overflow-auto">
                        <table id="tablaActivos" class="table-DT table table-striped uk-table uk-table-middle uk-table-divider">
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
                                            <button class="uk-button uk-button-primary uk-button-small" type="button" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $p['id'] ?>">Editar</button>
                                            <button type="button" class="uk-button uk-button-danger uk-button-small" data-bs-toggle="modal" data-bs-target="#modalConfirmarEliminar<?= $p['id'] ?>">Eliminar</button>
                                        </td>
                                    </tr>
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
                                                        <button type="submit" class="uk-button uk-button-primary">Guardar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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
                                                        <button type="button" class="uk-button uk-button-default" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="uk-button uk-button-danger">Eliminar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </li>

                <li>
                    <div class="uk-overflow-auto">
                        <table id="tablaInactivos" class="table-DT table table-striped uk-table uk-table-middle uk-table-divider">
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
                                            <button class="uk-button uk-button-primary uk-button-small" type="button" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $p['id'] ?>">Editar</button>
                                            <button type="button" class="uk-button uk-button-primary uk-button-small" data-bs-toggle="modal" data-bs-target="#modalConfirmarActivar<?= $p['id'] ?>">Activar</button>
                                        </td>
                                    </tr>
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
                                                        <button type="submit" class="uk-button uk-button-primary">Guardar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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
                                                        <button type="button" class="uk-button uk-button-default" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="uk-button uk-button-primary">Activar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </li>
            </ul>
        </div>
    </div>

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
                    <button type="submit" class="uk-button uk-button-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    <?php
        require_once __DIR__ . '../complements/footer.php';
    ?>
</body>
</html>
