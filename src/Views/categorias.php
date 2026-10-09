<!DOCTYPE html>
<html lang="es">
    <?php require_once __DIR__ . '../complements/head.php'; ?>
    <?php require_once __DIR__ . '../complements/header.php'; ?>

    <div class="uk-container uk-container-large uk-padding-large">
        <div class="uk-flex uk-flex-between uk-flex-middle uk-flex-wrap uk-margin-bottom" style="gap: 12px;">
            <h2 class="uk-heading-bullet uk-margin-remove">Gestión de Categorías de productos</h2>
            <button class="uk-button uk-button-primary" type="button" data-bs-toggle="modal" data-bs-target="#modalAgregar">+ Nueva Categoría</button>
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
                                    <button class="uk-button uk-button-primary uk-button-small" type="button" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $c['id'] ?>">Editar</button>
                                    <button type="button" class="uk-button uk-button-danger uk-button-small" data-bs-toggle="modal" data-bs-target="#modalEliminar<?= $c['id'] ?>">Eliminar</button>
                                </td>
                            </tr>

                            <div class="modal fade" id="modalEditar<?= $c['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="?c=CCategorias/edit" method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Editar Categoría</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                                <div class="mb-3">
                                                    <label>Nombre</label>
                                                    <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($c['nombre']) ?>" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="uk-button uk-button-default" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="uk-button uk-button-primary">Guardar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="modalEliminar<?= $c['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="?c=CCategorias/delete" method="POST">
                                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirmar eliminación</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                ¿Estás seguro de que deseas eliminar la categoría
                                                <strong><?= htmlspecialchars($c['nombre']) ?></strong>?
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
        </div>
    </div>

    <div class="modal fade" id="modalAgregar" tabindex="-1">
        <div class="modal-dialog">
            <form action="?c=CCategorias/add" method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="uk-button uk-button-default" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="uk-button uk-button-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <?php require_once __DIR__ . '../complements/footer.php'; ?>
</body>
</html>