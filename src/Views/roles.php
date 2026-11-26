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
            <h2 class="uk-heading-bullet uk-margin-remove">Gestión de Roles</h2>
            <button class="uk-button uk-button-primary" type="button" uk-toggle="target: #modalAgregar">+ Nuevo Rol</button>
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
                                <button class="uk-button uk-button-primary uk-button-small" type="button" uk-toggle="target: #modalEditar<?= $r['id'] ?>">Editar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="modalEditar<?= $r['id'] ?>" uk-modal>
                    <div class="uk-modal-dialog uk-modal-body uk-width-1-2@m">
                        <button class="uk-modal-close-default" type="button" uk-close></button>
                        <h2 class="uk-modal-title">Editar Rol</h2>
                        <form action="?c=CRoles/edit" method="POST">
                            <input type="hidden" name="id" value="<?= $r['id'] ?>">
                            <div class="uk-margin">
                                <label class="uk-form-label">Nombre</label>
                                <div class="uk-form-controls">
                                    <input type="text" name="nombre" class="uk-input" value="<?= htmlspecialchars($r['nombre']) ?>" required>
                                </div>
                            </div>
                            <div class="uk-text-right">
                                <button type="button" class="uk-button uk-button-default uk-modal-close">Cancelar</button>
                                <button type="submit" class="uk-button uk-button-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="modalAgregar" uk-modal>
        <div class="uk-modal-dialog uk-modal-body uk-width-1-2@m">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Agregar Rol</h2>
            <form action="?c=CRoles/add" method="POST">
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
    <?php
        require_once __DIR__ . '/complements/footer.php';
    ?>
</body>
</html>
