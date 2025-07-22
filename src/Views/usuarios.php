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
            <h3 class="fw-bold">Gestión de Usuarios</h3>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAgregar">+ Nuevo Usuario</button>
        </div>

        <!-- Tabs de Bootstrap -->
        <ul class="nav nav-tabs mb-3" id="usuarioTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="activos-tab" data-bs-toggle="tab" data-bs-target="#activos" type="button">Activos</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="inactivos-tab" data-bs-toggle="tab" data-bs-target="#inactivos" type="button">Inactivos</button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Usuarios Activos -->
            <div class="tab-pane fade show active" id="activos">
                <div class="table-responsive">
                    <table id="tablaActivos" class="table-DT table-striped">
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
                                    <td><input type="password" name="clave" id="" value="<?= htmlspecialchars($u['clave']) ?>"></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $u['id'] ?>">Editar</button>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalConfirmarEliminar<?= $u['id'] ?>">Eliminar</button>
                                    </td>
                                </tr>
                                <!-- Modal Editar -->
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
                                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal Confirmar Eliminación -->
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

            <!-- Clientes Inactivos -->
            <div class="tab-pane fade" id="inactivos">
                <div class="table-responsive">
                    <table id="tablaInactivos" class="table-DT table table-striped">
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
                                    <td><input type="password" name="clave" id="" value="<?= htmlspecialchars($u['clave']) ?>"></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $u['id'] ?>">Editar</button>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalConfirmarActivar<?= $u['id'] ?>">Activar</button>
                                    </td>
                                </tr>
                                <!-- Modal Editar -->
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
                                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal Confirmar Activación -->
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
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-success">Habilitar</button>
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

    <!-- Modal Agregar Usuario -->
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
