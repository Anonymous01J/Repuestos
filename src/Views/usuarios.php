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
            <h2 class="uk-heading-bullet uk-margin-remove">Gestión de Usuarios</h2>
            <button class="uk-button uk-button-primary" type="button" data-bs-toggle="modal" data-bs-target="#modalAgregar">+ Nuevo Usuario</button>
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
                                    <th>Rol</th>
                                    <th>Usuario</th>
                                    <th>Clave</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $u): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($u['nombre']) ?></td>
                                        <td><?= htmlspecialchars($u['rol']) ?></td>
                                        <td><?= htmlspecialchars($u['usuario']) ?></td>
                                        <td><input type="password" name="clave" value="<?= htmlspecialchars($u['clave']) ?>" class="uk-input uk-form-small"></td>
                                        <td>
                                            <button class="uk-button uk-button-primary uk-button-small" type="button" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $u['id'] ?>">Editar</button>
                                            <button type="button" class="uk-button uk-button-danger uk-button-small" data-bs-toggle="modal" data-bs-target="#modalConfirmarEliminar<?= $u['id'] ?>">Eliminar</button>
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="modalEditar<?= $u['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="?c=CUsuarios/edit" method="POST">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Editar Usuario</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                                        <div class="mb-3">
                                                            <label>Nombre</label>
                                                            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($u['nombre']) ?>" required>
                                                            <label>Rol</label>
                                                            <select name="rol" class="form-control" required>
                                                                <option value="">Seleccionar rol</option>
                                                                <?php foreach ($roles as $r): ?>
                                                                    <option value="<?= $r['id'] ?>" <?= $r['id'] == $u['rol'] ? 'selected' : '' ?>><?= htmlspecialchars($r['nombre']) ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>Usuario</label>
                                                            <input type="text" name="usuario" class="form-control" value="<?= htmlspecialchars($u['usuario']) ?>" required>
                                                            <label>Contraseña</label>
                                                            <input type="password" name="contrasena" class="form-control" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="uk-button uk-button-primary">Guardar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="modalConfirmarEliminar<?= $u['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="?c=CUsuarios/delete" method="POST">
                                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Confirmar eliminación</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        ¿Estás seguro de que deseas inhabilitar a <?= htmlspecialchars($u['nombre']) ?>?
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
                                    <th>Rol</th>
                                    <th>Usuario</th>
                                    <th>Clave</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuariosInactive as $u): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($u['nombre']) ?></td>
                                        <td><?= htmlspecialchars($u['rol']) ?></td>
                                        <td><?= htmlspecialchars($u['usuario']) ?></td>
                                        <td><input type="password" name="clave" value="<?= htmlspecialchars($u['clave']) ?>" class="uk-input uk-form-small"></td>
                                        <td>
                                            <button class="uk-button uk-button-primary uk-button-small" type="button" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $u['id'] ?>">Editar</button>
                                            <button type="button" class="uk-button uk-button-primary uk-button-small" data-bs-toggle="modal" data-bs-target="#modalConfirmarActivar<?= $u['id'] ?>">Activar</button>
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="modalEditar<?= $u['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="?c=CUsuarios/edit" method="POST">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Editar Usuario</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                                        <div class="mb-3">
                                                            <label>Nombre</label>
                                                            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($u['nombre']) ?>" required>
                                                            <label>Rol</label>
                                                            <select name="rol" class="form-control" required>
                                                                <option value="">Seleccionar rol</option>
                                                                <?php foreach ($roles as $r): ?>
                                                                    <option value="<?= $r['id'] ?>" <?= $r['id'] == $u['id_rol'] ? 'selected' : '' ?>><?= htmlspecialchars($r['nombre']) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>Usuario</label>
                                                            <input type="text" name="usuario" class="form-control" value="<?= htmlspecialchars($u['usuario']) ?>" required>
                                                            <label>Contraseña</label>
                                                            <input type="password" name="clave" class="form-control" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="uk-button uk-button-primary">Guardar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="modalConfirmarActivar<?= $u['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="?c=CUsuarios/active" method="POST">
                                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Confirmar activación</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        ¿Estás seguro de que deseas habilitar a <?= htmlspecialchars($u['nombre']) ?>?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="uk-button uk-button-default" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="uk-button uk-button-primary">Habilitar</button>
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
            <form action="?c=CUsuarios/add" method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Rol</label>
                        <select name="rol" class="form-control" required>
                            <option value="">Seleccionar rol</option>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Usuario</label>
                        <input type="text" name="usuario" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Contraseña</label>
                        <input type="password" name="clave" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Confirmar Contraseña</label>
                        <input type="password" name="confirmar_clave" class="form-control" required>
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
