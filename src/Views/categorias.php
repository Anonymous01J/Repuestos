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
            <h3 class="fw-bold">Gestión de Categorías de productos</h3>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAgregar">+ Nueva Categoría</button>
        </div>
        <div class="container bg-white p-4 rounded shadow-sm">
            <!-- Tabs de Bootstrap -->
            <ul class="nav nav-tabs mb-3" id="categoriaTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="activos-tab" data-bs-toggle="tab" data-bs-target="#activos" type="button">Activos</button>
                </li>
                <!-- <li class="nav-item">
                    <button class="nav-link" id="inactivos-tab" data-bs-toggle="tab" data-bs-target="#inactivos" type="button">Inactivos</button>
                </li> -->
            </ul>

            <div class="tab-content">
                <!-- Categorías Activas -->
                <div class="tab-pane fade show active" id="activos">
                    <div class="table-responsive">
                        <table id="tablaActivos" class="table-DT table table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Código</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categorias as $c): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($c['nombre']) ?></td>
                                        <td><?= htmlspecialchars($c['codigo']) ?></td>
                                        <td><?= htmlspecialchars($c['descripcion']) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $c['id'] ?>">Editar</button>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalConfirmarEliminar<?= $c['id'] ?>">Eliminar</button>
                                        </td>
                                    </tr>
                                    <!-- Modal Editar -->
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
                                                            <label>Código</label>
                                                            <input type="text" name="codigo" class="form-control" value="<?= htmlspecialchars($c['codigo']) ?>">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>Descripción</label>
                                                            <textarea name="descripcion" class="form-control"><?= htmlspecialchars($c['descripcion']) ?></textarea>
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
                                    <div class="modal fade" id="modalConfirmarEliminar<?= $c['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="?c=CCategorias/delete" method="POST">
                                                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Confirmar eliminación</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        ¿Estás seguro de que deseas inhabilitar la categoría <?= htmlspecialchars($c['nombre']) ?>?
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

                <!-- Categorias Inactivas -->
                <div class="tab-pane fade" id="inactivos">
                    <div class="table-responsive">
                        <table id="tablaInactivos" class="table-DT table table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Código</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categoriasInactive as $c): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($c['nombre']) ?></td>
                                        <td><?= htmlspecialchars($c['codigo']) ?></td>
                                        <td><?= htmlspecialchars($c['descripcion']) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $c['id'] ?>">Editar</button>
                                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalConfirmarActivar<?= $c['id'] ?>">Activar</button>
                                        </td>
                                    </tr>
                                    <!-- Modal Editar -->
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
                                                            <label>Código</label>
                                                            <input type="text" name="codigo" class="form-control" value="<?= htmlspecialchars($c['codigo']) ?>">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>Descripción</label>
                                                            <textarea name="descripcion" class="form-control"><?= htmlspecialchars($c['descripcion']) ?></textarea>
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
                                    <div class="modal fade" id="modalConfirmarActivar<?= $c['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="?c=CClientes/active" method="POST">
                                                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Confirmar activación</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        ¿Estás seguro de que deseas habilitar la categoría <?= htmlspecialchars($c['nombre']) ?>?
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

    </div>

    <!-- Modal Agregar categoria -->
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
                    <div class="mb-3">
                        <label>Código</label>
                        <input type="text" name="codigo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Descripción</label>
                        <textarea name="descripcion" class="form-control" required></textarea>
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
