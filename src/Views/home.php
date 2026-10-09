<!DOCTYPE html>
<html lang="es">
    <?php require_once __DIR__ . '../complements/head.php'; ?>
    <?php require_once __DIR__ . '../complements/header.php'; ?>

    <div class="container py-5">
        <h2 class="mb-4">Panel principal</h2>

        <!-- ── Tarjetas de acceso rápido ───────────────────────────────── -->
        <div class="row g-4 d-flex justify-content-center">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Clientes</h5>
                        <p class="card-text">Gestiona todos los clientes registrados.</p>
                        <a href="?c=CClientes/view" class="btn btn-primary">Ver clientes</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Productos</h5>
                        <p class="card-text">Controla tus repuestos disponibles.</p>
                        <a href="?c=CProductos/view" class="btn btn-primary">Ver productos</a>
                    </div>
                </div>  
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Proveedores</h5>
                        <p class="card-text">Gestiona a tus proveedores.</p>
                        <a href="?c=CProveedores/view" class="btn btn-primary">Ver proveedores</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Categorías de Productos</h5>
                        <p class="card-text">Gestiona las categorías disponibles.</p>
                        <a href="?c=CCategorias/view" class="btn btn-primary">Ver categorías</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Ventas</h5>
                        <p class="card-text">Realiza y revisa ventas realizadas.</p>
                        <a href="?c=CProductos/view#ventas" class="btn btn-primary">Ver ventas</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Sección de alertas de stock bajo ────────────────────────── -->
        <div class="mt-5">
            <?php if (!empty($productosAlerta)): ?>
                <?php
                    $sinStock = array_filter($productosAlerta, fn($p) => $p['nivel_alerta'] === 'SIN_STOCK');
                    $criticos = array_filter($productosAlerta, fn($p) => $p['nivel_alerta'] === 'CRITICO');
                    $totalAlertas = count($productosAlerta);
                ?>

                <!-- Encabezado de sección -->
                <div class="d-flex align-items-center gap-2 mb-3">
                    <h4 class="mb-0 fw-bold text-danger">
                        ⚠️ Alertas de Inventario
                    </h4>
                    <span class="badge bg-danger fs-6"><?= $totalAlertas ?></span>
                    <?php if (count($sinStock) > 0): ?>
                        <span class="badge bg-dark"><?= count($sinStock) ?> sin stock</span>
                    <?php endif; ?>
                    <?php if (count($criticos) > 0): ?>
                        <span class="badge bg-warning text-dark"><?= count($criticos) ?> críticos</span>
                    <?php endif; ?>
                </div>

                <div class="card border-danger shadow-sm">
                    <div class="card-header bg-danger text-white fw-semibold d-flex justify-content-between align-items-center">
                        <span>Productos con stock por debajo del mínimo</span>
                        <a href="?c=CProductos/view" class="btn btn-sm btn-light text-danger fw-semibold">
                            Ver todos los productos →
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Producto</th>
                                        <th>Categoría</th>
                                        <th>Precio</th>
                                        <th class="text-center">Stock actual</th>
                                        <th class="text-center">Stock mínimo</th>
                                        <th class="text-center">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($productosAlerta as $p): ?>
                                        <?php
                                            // Determinar clases de fila y badge según nivel de alerta
                                            if ($p['nivel_alerta'] === 'SIN_STOCK') {
                                                $rowClass   = 'table-danger';
                                                $badgeClass = 'bg-danger';
                                                $badgeLabel = '🚫 Sin stock';
                                            } else {
                                                $rowClass   = 'table-warning';
                                                $badgeClass = 'bg-warning text-dark';
                                                $badgeLabel = '⚠️ Crítico';
                                            }
                                            $deficit = $p['stock_minimo'] - $p['stock_actual'];
                                        ?>
                                        <tr class="<?= $rowClass ?>">
                                            <td class="ps-3 fw-semibold">
                                                <?= htmlspecialchars($p['nombre']) ?>
                                                <?php if ($p['marca']): ?>
                                                    <small class="text-muted fw-normal">
                                                        — <?= htmlspecialchars($p['marca']) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($p['categoria'] ?? '—') ?></td>
                                            <td><?= number_format($p['precio'], 2) ?> $</td>
                                            <td class="text-center fw-bold">
                                                <?= (int) $p['stock_actual'] ?>
                                            </td>
                                            <td class="text-center text-muted">
                                                <?= (int) $p['stock_minimo'] ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge <?= $badgeClass ?> rounded-pill px-3 py-2">
                                                    <?= $badgeLabel ?>
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    Faltan <?= $deficit ?> ud<?= $deficit !== 1 ? 's' : '' ?>.
                                                </small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <!-- Todo en orden -->
                <div class="alert alert-success d-flex align-items-center gap-2 shadow-sm" role="alert">
                    <span style="font-size: 1.4rem;">✅</span>
                    <div>
                        <strong>Inventario en orden.</strong>
                        Todos los productos tienen stock por encima del mínimo establecido.
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-4 text-muted">
            <small>Rol: <?= htmlspecialchars($_SESSION['rol']) ?> | <?= date('d/m/Y H:i') ?></small>
        </div>
    </div>

    <?php require_once __DIR__ . '../complements/footer.php'; ?>
</body>
</html>