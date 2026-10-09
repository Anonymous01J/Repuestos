<!DOCTYPE html>
<html lang="es">
    <?php require_once __DIR__ . '../complements/head.php'; ?>
    <?php require_once __DIR__ . '../complements/header.php'; ?>

    <div class="uk-container uk-container-large uk-padding-large" style="padding-top: 40px; padding-bottom: 40px;">
        <div class="uk-margin-bottom">
            <h2 class="uk-heading-bullet uk-margin-remove">Panel principal</h2>
        </div>

        <div class="uk-grid uk-grid-small uk-child-width-1-1 uk-child-width-1-2@m uk-child-width-1-3@l" uk-grid>
            <div>
                <div class="uk-card uk-card-default uk-card-body uk-height-1-1">
                    <h5 class="uk-card-title uk-margin-small-bottom">Clientes</h5>
                    <p class="uk-text-small uk-text-muted">Gestiona todos los clientes registrados.</p>
                    <a href="?c=CClientes/view" class="uk-button uk-button-primary">Ver clientes</a>
                </div>
            </div>
            <div>
                <div class="uk-card uk-card-default uk-card-body uk-height-1-1">
                    <h5 class="uk-card-title uk-margin-small-bottom">Productos</h5>
                    <p class="uk-text-small uk-text-muted">Controla tus repuestos disponibles.</p>
                    <a href="?c=CProductos/view" class="uk-button uk-button-primary">Ver productos</a>
                </div>
            </div>
            <div>
                <div class="uk-card uk-card-default uk-card-body uk-height-1-1">
                    <h5 class="uk-card-title uk-margin-small-bottom">Proveedores</h5>
                    <p class="uk-text-small uk-text-muted">Gestiona a tus proveedores.</p>
                    <a href="?c=CProveedores/view" class="uk-button uk-button-primary">Ver proveedores</a>
                </div>
            </div>
            <div>
                <div class="uk-card uk-card-default uk-card-body uk-height-1-1">
                    <h5 class="uk-card-title uk-margin-small-bottom">Categorías de Productos</h5>
                    <p class="uk-text-small uk-text-muted">Gestiona las categorías disponibles.</p>
                    <a href="?c=CCategorias/view" class="uk-button uk-button-primary">Ver categorías</a>
                </div>
            </div>
            <div>
                <div class="uk-card uk-card-default uk-card-body uk-height-1-1">
                    <h5 class="uk-card-title uk-margin-small-bottom">Ventas</h5>
                    <p class="uk-text-small uk-text-muted">Realiza y revisa ventas realizadas.</p>
                    <a href="?c=CProductos/view#ventas" class="uk-button uk-button-primary">Ver ventas</a>
                </div>
            </div>
        </div>

        <div class="uk-margin-large-top">
            <?php if (!empty($productosAlerta)): ?>
                <?php
                    $sinStock = array_filter($productosAlerta, fn($p) => $p['nivel_alerta'] === 'SIN_STOCK');
                    $criticos = array_filter($productosAlerta, fn($p) => $p['nivel_alerta'] === 'CRITICO');
                    $totalAlertas = count($productosAlerta);
                ?>

                <div class="uk-flex uk-flex-middle uk-flex-wrap uk-margin-bottom" style="gap: 12px;">
                    <h3 class="uk-margin-remove uk-text-danger">⚠️ Alertas de Inventario</h3>
                    <span class="uk-badge uk-label-danger" style="background: #dc2626; color: white; font-size: 0.8rem; padding: 0.35rem 0.75rem;"><?= $totalAlertas ?></span>
                    <?php if (count($sinStock) > 0): ?>
                        <span class="uk-badge" style="background: #111827; color: white; padding: 0.35rem 0.75rem;"><?= count($sinStock) ?> sin stock</span>
                    <?php endif; ?>
                    <?php if (count($criticos) > 0): ?>
                        <span class="uk-badge" style="background: #fbbf24; color: #111827; padding: 0.35rem 0.75rem;"><?= count($criticos) ?> críticos</span>
                    <?php endif; ?>
                </div>

                <div class="uk-card uk-card-default uk-overflow-hidden" style="border: 1px solid rgba(220, 38, 38, 0.25);">
                    <div class="uk-card-header uk-flex uk-flex-between uk-flex-middle" style="background: #fef2f2; border-bottom: 1px solid rgba(220, 38, 38, 0.18);">
                        <div class="uk-text-bold uk-text-danger">Productos con stock por debajo del mínimo</div>
                        <a href="?c=CProductos/view" class="uk-button uk-button-small uk-button-default" style="border-color: rgba(220, 38, 38, 0.35); color: #b91c1c;">Ver todos los productos →</a>
                    </div>
                    <div class="uk-card-body uk-padding-remove">
                        <div class="uk-overflow-auto">
                            <table class="uk-table uk-table-middle uk-table-divider uk-table-hover uk-margin-remove">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Categoría</th>
                                        <th>Precio</th>
                                        <th class="uk-text-center">Stock actual</th>
                                        <th class="uk-text-center">Stock mínimo</th>
                                        <th class="uk-text-center">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($productosAlerta as $p): ?>
                                        <?php
                                            if ($p['nivel_alerta'] === 'SIN_STOCK') {
                                                $rowStyle = 'background: rgba(254, 202, 202, 0.4);';
                                                $badgeStyle = 'background: #dc2626; color: white;';
                                                $badgeLabel = '🚫 Sin stock';
                                            } else {
                                                $rowStyle = 'background: rgba(254, 243, 199, 0.6);';
                                                $badgeStyle = 'background: #fbbf24; color: #111827;';
                                                $badgeLabel = '⚠️ Crítico';
                                            }
                                            $deficit = $p['stock_minimo'] - $p['stock_actual'];
                                        ?>
                                        <tr style="<?= $rowStyle ?>">
                                            <td>
                                                <div class="uk-text-bold"><?= htmlspecialchars($p['nombre']) ?></div>
                                                <?php if ($p['marca']): ?>
                                                    <div class="uk-text-small uk-text-muted"><?= htmlspecialchars($p['marca']) ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($p['categoria'] ?? '—') ?></td>
                                            <td><?= number_format($p['precio'], 2) ?> $</td>
                                            <td class="uk-text-center uk-text-bold"><?= (int) $p['stock_actual'] ?></td>
                                            <td class="uk-text-center uk-text-muted"><?= (int) $p['stock_minimo'] ?></td>
                                            <td class="uk-text-center">
                                                <span class="uk-badge" style="<?= $badgeStyle ?>; padding: 0.55rem 0.85rem; border-radius: 999px;"><?= $badgeLabel ?></span>
                                                <div class="uk-text-small uk-text-muted uk-margin-small-top">Faltan <?= $deficit ?> ud<?= $deficit !== 1 ? 's' : '' ?>.</div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <div class="uk-alert uk-alert-success" uk-alert>
                    <p class="uk-margin-remove"><strong>Inventario en orden.</strong> Todos los productos tienen stock por encima del mínimo establecido.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="uk-margin-large-top uk-text-muted uk-text-small">
            Rol: <?= htmlspecialchars($_SESSION['rol']) ?> | <?= date('d/m/Y H:i') ?>
        </div>
    </div>

    <?php require_once __DIR__ . '../complements/footer.php'; ?>
</body>
</html>