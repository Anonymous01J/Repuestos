<!DOCTYPE html>
<html lang="es">
    <?php require_once __DIR__ . '../complements/head.php'; ?>
    <?php require_once __DIR__ . '../complements/header.php'; ?>

    <div class="uk-container uk-container-large uk-padding-large">
        <div class="uk-flex uk-flex-between uk-flex-middle uk-flex-wrap uk-margin-bottom" style="gap: 12px;">
            <h2 class="uk-heading-bullet uk-margin-remove">Gestión de Categorías de productos</h2>
            <button class="uk-button uk-button-primary" type="button" uk-toggle="target: #modalAgregar">+ Nueva Categoría</button>
        </div>

        <div class="uk-card uk-card-default uk-card-body">
            <div class="uk-overflow-auto">
                <table id="tablaCategorias" class="table-DT table table-striped uk-table uk-table-middle uk-table-divider">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categorias as $c): ?>
                            <tr>
                                <td><?= $c['id'] ?></td>
                                <td><?= htmlspecialchars($c['nombre']) ?></td>
                                <td>
                                    <button class="uk-button uk-button-primary uk-button-small" type="button" uk-toggle="target: #modalEditar<?= $c['id'] ?>">Editar</button>
                                    <button type="button" class="uk-button uk-button-danger uk-button-small" uk-toggle="target: #modalEliminar<?= $c['id'] ?>">Eliminar</button>
                                </td>
                            </tr>

                            <div id="modalEditar<?= $c['id'] ?>" uk-modal>
                                <div class="uk-modal-dialog uk-modal-body uk-width-1-2@m">
                                    <button class="uk-modal-close-default" type="button" uk-close></button>
                                    <h2 class="uk-modal-title">Editar Categoría</h2>
                                    <form action="?c=CCategorias/edit" method="POST">
                                        <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                        <div class="uk-margin">
                                            <label class="uk-form-label">Nombre</label>
                                            <div class="uk-form-controls">
                                                <input type="text" name="nombre" class="uk-input" value="<?= htmlspecialchars($c['nombre']) ?>" required>
                                            </div>
                                        </div>
                                        <div class="uk-text-right">
                                            <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                                            <button type="submit" class="uk-button uk-button-primary">Guardar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div id="modalEliminar<?= $c['id'] ?>" uk-modal>
                                <div class="uk-modal-dialog uk-modal-body uk-width-medium">
                                    <button class="uk-modal-close-default" type="button" uk-close></button>
                                    <h2 class="uk-modal-title">Confirmar eliminación</h2>
                                    <form action="?c=CCategorias/delete" method="POST">
                                        <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                        <p>¿Estás seguro de que deseas eliminar la categoría <strong><?= htmlspecialchars($c['nombre']) ?></strong>?</p>
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
        </div>
    </div>

    <div id="modalAgregar" uk-modal>
        <div class="uk-modal-dialog uk-modal-body uk-width-1-2@m">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Agregar Categoría</h2>
            <form action="?c=CCategorias/add" method="POST">
                <div class="uk-margin">
                    <label class="uk-form-label">Nombre</label>
                    <div class="uk-form-controls">
                        <input type="text" name="nombre" class="uk-input" required>
                    </div>
                </div>
                <div class="uk-text-right">
                    <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                    <button type="submit" class="uk-button uk-button-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <?php require_once __DIR__ . '../complements/footer.php'; ?>
</body>
</html>