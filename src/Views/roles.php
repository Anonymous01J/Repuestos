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
            <h2 class="uk-heading-bullet uk-margin-remove">Gestión de Roles</h2>
            <button class="uk-button uk-button-primary" type="button" data-bs-toggle="modal" data-bs-target="#modalAgregar">+ Nuevo Rol</button>
        </div>

        <div class="uk-grid uk-grid-small uk-child-width-1-1 uk-child-width-1-2@m uk-child-width-1-3@l" uk-grid>
            <?php foreach ($roles as $r): ?>
                <div>
                    <div class="uk-card uk-card-default uk-card-body uk-height-1-1">
                        <div class="uk-flex uk-flex-column uk-height-1-1" style="gap: 12px;">
                            <div>
                                <h5 class="uk-card-title uk-margin-small-bottom">Rol Nro <?= htmlspecialchars($r['id']) ?></h5>
                                <p class="uk-margin-remove uk-text-muted"><?= htmlspecialchars($r['nombre']) ?></p>
                            </div>
                            <div>
                                <button class="uk-button uk-button-primary uk-button-small" type="button" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $r['id'] ?>">Editar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="modalEditar<?= $r['id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="?c=CRoles/edit" method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title">Editar Rol</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                    <div class="mb-3">
                                        <label>Nombre</label>
                                        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($r['nombre']) ?>" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="uk-button uk-button-primary">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="modal fade" id="modalAgregar" tabindex="-1">
        <div class="modal-dialog">
            <form action="?c=CRoles/add" method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
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
