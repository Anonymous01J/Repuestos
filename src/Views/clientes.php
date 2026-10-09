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
            <h2 class="uk-heading-bullet uk-margin-remove">Gestión de Clientes</h2>
            <button class="uk-button uk-button-primary" type="button" uk-toggle="target: #modalAgregar">+ Nuevo Cliente</button>
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
                                <?php foreach ($clientes as $c): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($c['nombre']) ?></td>
                                        <td><?= htmlspecialchars($c['cedula_rif']) ?></td>
                                        <td><?= htmlspecialchars($c['telefono']) ?></td>
                                        <td><?= htmlspecialchars($c['direccion']) ?></td>
                                        <td>
                                            <button class="uk-button uk-button-primary uk-button-small" type="button" uk-toggle="target: #modalEditar<?= $c['id'] ?>">Editar</button>
                                            <button type="button" class="uk-button uk-button-danger uk-button-small" uk-toggle="target: #modalConfirmarEliminar<?= $c['id'] ?>">Eliminar</button>
                                        </td>
                                    </tr>
                                    <div id="modalEditar<?= $c['id'] ?>" uk-modal>
                                        <div class="uk-modal-dialog uk-modal-body uk-width-1-2@m">
                                            <button class="uk-modal-close-default" type="button" uk-close></button>
                                            <h2 class="uk-modal-title">Editar Cliente</h2>
                                            <form action="?c=CClientes/edit" method="POST">
                                                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                                <div class="uk-margin">
                                                    <label class="uk-form-label">Teléfono</label>
                                                    <div class="uk-form-controls">
                                                        <input type="text" name="telefono" class="uk-input" value="<?= htmlspecialchars($c['telefono']) ?>">
                                                    </div>
                                                </div>
                                                <div class="uk-margin">
                                                    <label class="uk-form-label">Dirección</label>
                                                    <div class="uk-form-controls">
                                                        <textarea name="direccion" class="uk-textarea"><?= htmlspecialchars($c['direccion']) ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="uk-text-right">
                                                    <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                                                    <button type="submit" class="uk-button uk-button-primary">Guardar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div id="modalConfirmarEliminar<?= $c['id'] ?>" uk-modal>
                                        <div class="uk-modal-dialog uk-modal-body uk-width-medium">
                                            <button class="uk-modal-close-default" type="button" uk-close></button>
                                            <h2 class="uk-modal-title">Confirmar eliminación</h2>
                                            <form action="?c=CClientes/delete" method="POST">
                                                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                                <p>¿Estás seguro de que deseas inhabilitar a <?= htmlspecialchars($c['nombre']) ?>?</p>
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
                                    <th>Cédula/RIF</th>
                                    <th>Teléfono</th>
                                    <th>Dirección</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($clientesInactive as $c): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($c['nombre']) ?></td>
                                        <td><?= htmlspecialchars($c['cedula_rif']) ?></td>
                                        <td><?= htmlspecialchars($c['telefono']) ?></td>
                                        <td><?= htmlspecialchars($c['direccion']) ?></td>
                                        <td>
                                            <button class="uk-button uk-button-primary uk-button-small" type="button" uk-toggle="target: #modalEditar<?= $c['id'] ?>">Editar</button>
                                            <button type="button" class="uk-button uk-button-primary uk-button-small" uk-toggle="target: #modalConfirmarActivar<?= $c['id'] ?>">Activar</button>
                                        </td>
                                    </tr>
                                    <div id="modalEditar<?= $c['id'] ?>" uk-modal>
                                        <div class="uk-modal-dialog uk-modal-body uk-width-1-2@m">
                                            <button class="uk-modal-close-default" type="button" uk-close></button>
                                            <h2 class="uk-modal-title">Editar Cliente</h2>
                                            <form action="?c=CClientes/edit" method="POST">
                                                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                                <div class="uk-margin">
                                                    <label class="uk-form-label">Teléfono</label>
                                                    <div class="uk-form-controls">
                                                        <input type="text" name="telefono" class="uk-input" value="<?= htmlspecialchars($c['telefono']) ?>">
                                                    </div>
                                                </div>
                                                <div class="uk-margin">
                                                    <label class="uk-form-label">Dirección</label>
                                                    <div class="uk-form-controls">
                                                        <textarea name="direccion" class="uk-textarea"><?= htmlspecialchars($c['direccion']) ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="uk-text-right">
                                                    <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                                                    <button type="submit" class="uk-button uk-button-primary">Guardar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div id="modalConfirmarActivar<?= $c['id'] ?>" uk-modal>
                                        <div class="uk-modal-dialog uk-modal-body uk-width-medium">
                                            <button class="uk-modal-close-default" type="button" uk-close></button>
                                            <h2 class="uk-modal-title">Confirmar activación</h2>
                                            <form action="?c=CClientes/active" method="POST">
                                                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                                <p>¿Estás seguro de que deseas habilitar a <?= htmlspecialchars($c['nombre']) ?>?</p>
                                                <div class="uk-text-right">
                                                    <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                                                    <button type="submit" class="uk-button uk-button-primary">Activar</button>
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
            <h2 class="uk-modal-title">Agregar Cliente</h2>
            <form action="?c=CClientes/add" method="POST">
                <div class="uk-margin">
                    <label class="uk-form-label">Nombre</label>
                    <div class="uk-form-controls">
                        <input type="text" name="nombre" class="uk-input" required>
                    </div>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label">Cédula/RIF</label>
                    <div class="uk-form-controls">
                        <input type="text" name="cedula_rif" class="uk-input" required>
                    </div>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label">Teléfono</label>
                    <div class="uk-form-controls">
                        <input type="text" name="telefono" class="uk-input" required>
                    </div>
                </div>
                <div class="uk-margin">
                    <label class="uk-form-label">Dirección</label>
                    <div class="uk-form-controls">
                        <textarea name="direccion" class="uk-textarea" required></textarea>
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
        require_once __DIR__ . '../complements/footer.php';
    ?>
</body>
</html>
