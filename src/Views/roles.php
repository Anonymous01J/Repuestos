<!DOCTYPE html>
<html lang="es">
    <?php
        require_once __DIR__ . '/complements/head.php';
    ?>
    <?php
        require_once __DIR__ . '/complements/header.php';
    ?>

    <div class="container py-4">
        <div class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold">Gestión de Roles</h3>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAgregar">+ Nuevo Rol</button>
            </div>
            <div class="row g-4">
                <?php foreach ($roles as $r): ?>
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <h5 class="card-title mb-2">Rol Nro <?= htmlspecialchars($r['id']) ?></h5>
                                    <p class="card-text"><?= htmlspecialchars($r['nombre']) ?></p>
                                </div>
                                <div class="mt-3">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $r['id'] ?>">Editar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Editar -->
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
                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Modal Agregar Rol -->
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
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    <?php
        require_once __DIR__ . '/complements/footer.php';
    ?>
</body>
</html>
