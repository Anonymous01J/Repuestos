<!DOCTYPE html>
<html lang="es">
    <?php
        require_once __DIR__ . '/complements/head.php';
    ?>
    <?php
        require_once __DIR__ . '/complements/header.php';
    ?>

    <div class="uk-container uk-container-large uk-padding-large">
        <div class="uk-flex uk-flex-between uk-flex-middle uk-flex-wrap uk-margin-bottom" style="gap: 12px;">
            <h2 class="uk-heading-bullet uk-margin-remove">Gestión de Usuarios</h2>
            <button class="uk-button uk-button-primary" type="button" uk-toggle="target: #modalAgregar">+ Nuevo Usuario</button>
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
                                            <button class="uk-button uk-button-primary uk-button-small" type="button" uk-toggle="target: #modalEditar<?= $u['id'] ?>">Editar</button>
                                            <button type="button" class="uk-button uk-button-danger uk-button-small" uk-toggle="target: #modalConfirmarEliminar<?= $u['id'] ?>">Eliminar</button>
                                        </td>
                                    </tr>
                                    <div id="modalEditar<?= $u['id'] ?>" uk-modal>
                                        <div class="uk-modal-dialog uk-modal-body uk-width-1-2@m">
                                            <button class="uk-modal-close-default" type="button" uk-close></button>
                                            <h2 class="uk-modal-title">Editar Usuario</h2>
                                            <form action="?c=CUsuarios/edit" method="POST">
                                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                                <div class="uk-margin">
                                                    <label class="uk-form-label">Nombre</label>
                                                    <div class="uk-form-controls">
                                                        <input type="text" name="nombre" class="uk-input" value="<?= htmlspecialchars($u['nombre']) ?>" required>
                                                    </div>
                                                </div>
                                                <div class="uk-margin">
                                                    <label class="uk-form-label">Rol</label>
                                                    <div class="uk-form-controls">
                                                        <select name="rol" class="uk-select" required>
                                                            <option value="">Seleccionar rol</option>
                                                            <?php foreach ($roles as $r): ?>
                                                                <option value="<?= $r['id'] ?>" <?= $r['id'] == $u['rol'] ? 'selected' : '' ?>><?= htmlspecialchars($r['nombre']) ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="uk-margin">
                                                    <label class="uk-form-label">Usuario</label>
                                                    <div class="uk-form-controls">
                                                        <input type="text" name="usuario" class="uk-input" value="<?= htmlspecialchars($u['usuario']) ?>" required>
                                                    </div>
                                                </div>
                                                <div class="uk-margin">
                                                    <label class="uk-form-label">Contraseña</label>
                                                    <div class="uk-form-controls">
                                                        <input type="password" name="contrasena" class="uk-input" required>
                                                    </div>
                                                </div>
                                                <div class="uk-text-right">
                                                    <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                                                    <button type="submit" class="uk-button uk-button-primary">Guardar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div id="modalConfirmarEliminar<?= $u['id'] ?>" uk-modal>
                                        <div class="uk-modal-dialog uk-modal-body uk-width-medium">
                                            <button class="uk-modal-close-default" type="button" uk-close></button>
                                            <h2 class="uk-modal-title">Confirmar eliminación</h2>
                                            <form action="?c=CUsuarios/delete" method="POST">
                                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                                <p>¿Estás seguro de que deseas inhabilitar a <?= htmlspecialchars($u['nombre']) ?>?</p>
                                                <div class="uk-text-right">
                                                    <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                                                    <button type="submit" class="uk-button uk-button-danger">Eliminar</button>
                                                </div>
                                            </form>
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
                                            <button class="uk-button uk-button-primary uk-button-small" type="button" uk-toggle="target: #modalEditar<?= $u['id'] ?>">Editar</button>
                                            <button type="button" class="uk-button uk-button-primary uk-button-small" uk-toggle="target: #modalConfirmarActivar<?= $u['id'] ?>">Activar</button>
                                        </td>
                                    </tr>
                                    <div id="modalEditar<?= $u['id'] ?>" uk-modal>
                                        <div class="uk-modal-dialog uk-modal-body uk-width-1-2@m">
                                            <button class="uk-modal-close-default" type="button" uk-close></button>
                                            <h2 class="uk-modal-title">Editar Usuario</h2>
                                            <form action="?c=CUsuarios/edit" method="POST">
                                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                                <div class="uk-margin">
                                                    <label class="uk-form-label">Nombre</label>
                                                    <div class="uk-form-controls">
                                                        <input type="text" name="nombre" class="uk-input" value="<?= htmlspecialchars($u['nombre']) ?>" required>
                                                    </div>
                                                </div>
                                                <div class="uk-margin">
                                                    <label class="uk-form-label">Rol</label>
                                                    <div class="uk-form-controls">
                                                        <select name="rol" class="uk-select" required>
                                                            <option value="">Seleccionar rol</option>
                                                            <?php foreach ($roles as $r): ?>
                                                                <option value="<?= $r['id'] ?>" <?= $r['id'] == $u['id_rol'] ? 'selected' : '' ?>><?= htmlspecialchars($r['nombre']) ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="uk-margin">
                                                    <label class="uk-form-label">Usuario</label>
                                                    <div class="uk-form-controls">
                                                        <input type="text" name="usuario" class="uk-input" value="<?= htmlspecialchars($u['usuario']) ?>" required>
                                                    </div>
                                                </div>
                                                <div class="uk-margin">
                                                    <label class="uk-form-label">Contraseña</label>
                                                    <div class="uk-form-controls">
                                                        <input type="password" name="clave" class="uk-input" required>
                                                    </div>
                                                </div>
                                                <div class="uk-text-right">
                                                    <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                                                    <button type="submit" class="uk-button uk-button-primary">Guardar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div id="modalConfirmarActivar<?= $u['id'] ?>" uk-modal>
                                        <div class="uk-modal-dialog uk-modal-body uk-width-medium">
                                            <button class="uk-modal-close-default" type="button" uk-close></button>
                                            <h2 class="uk-modal-title">Confirmar activación</h2>
                                            <form action="?c=CUsuarios/active" method="POST">
                                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                                <p>¿Estás seguro de que deseas habilitar a <?= htmlspecialchars($u['nombre']) ?>?</p>
                                                <div class="uk-text-right">
                                                    <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                                                    <button type="submit" class="uk-button uk-button-primary">Habilitar</button>
                                                </div>
                                            </form>
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

    <div id="modalAgregar" uk-modal>
        <div class="uk-modal-dialog uk-modal-body uk-width-1-2@m">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Agregar Usuario</h2>
            <form action="?c=CUsuarios/add" method="POST">
                <div class="uk-margin">
                    <label class="uk-form-label">Nombre</label>
                    <div class="uk-form-controls">
                        <input type="text" name="nombre" class="uk-input" required>
                    </div>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label">Rol</label>
                    <div class="uk-form-controls">
                        <select name="rol" class="uk-select" required>
                            <option value="">Seleccionar rol</option>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label">Usuario</label>
                    <div class="uk-form-controls">
                        <input type="text" name="usuario" class="uk-input" required>
                    </div>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label">Contraseña</label>
                    <div class="uk-form-controls">
                        <input type="password" name="clave" class="uk-input" required>
                    </div>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label">Confirmar Contraseña</label>
                    <div class="uk-form-controls">
                        <input type="password" name="confirmar_clave" class="uk-input" required>
                    </div>
                </div>
                <div class="uk-text-right">
                    <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                    <button type="submit" class="uk-button uk-button-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    <?php
        require_once __DIR__ . '/complements/footer.php';
    ?>
</body>
</html>
