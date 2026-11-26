<!DOCTYPE html>
<html lang="es">
<<<<<<< HEAD
<?php $productosAlerta = $productosAlerta ?? []; ?>
=======
>>>>>>> 427dc3a866328b94a630a4682abb9d435b139c9d
    <?php require_once __DIR__ . '/complements/head.php'; ?>
    <?php require_once __DIR__ . '/complements/header.php'; ?>

    <div class="app-dashboard-shell uk-container uk-container-large uk-padding-large">
        <div class="uk-margin-bottom">
            <div class="app-section-title">Panel principal</div>
        </div>

        <div class="app-dashboard-grid uk-grid uk-grid-small uk-child-width-1-1 uk-child-width-1-2@m uk-child-width-1-3@l" uk-grid>
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

        <?php
            $sinStock = array_filter($productosAlerta, static fn($producto) => ($producto['nivel_alerta'] ?? '') === 'SIN_STOCK');
            $stockBajo = array_filter($productosAlerta, static fn($producto) => ($producto['nivel_alerta'] ?? '') !== 'SIN_STOCK');
        ?>
        <section class="uk-card uk-card-default uk-card-body uk-margin-large-top" aria-labelledby="stockSummaryTitle">
            <div class="uk-flex uk-flex-between uk-flex-middle uk-flex-wrap" style="gap: 16px;">
                <div>
                    <h3 id="stockSummaryTitle" class="uk-card-title uk-margin-small-bottom">Alertas de inventario</h3>
                    <p class="uk-text-muted uk-margin-remove">Resumen de productos que requieren reposición.</p>
                </div>
                <a href="?c=CStockBajo/view" class="uk-button uk-button-primary">
                    Ver detalles <span uk-icon="icon: arrow-right"></span>
                </a>
            </div>
            <div class="app-stock-summary-grid">
                <div><div class="app-stock-summary-stat"><div class="app-stock-summary-label">Total de alertas</div><strong class="app-stock-summary-value"><?= count($productosAlerta) ?></strong></div></div>
                <div><div class="app-stock-summary-stat"><div class="app-stock-summary-label">Sin stock</div><strong class="app-stock-summary-value app-stock-summary-value--danger"><?= count($sinStock) ?></strong></div></div>
                <div><div class="app-stock-summary-stat"><div class="app-stock-summary-label">Stock bajo</div><strong class="app-stock-summary-value app-stock-summary-value--warning"><?= count($stockBajo) ?></strong></div></div>
            </div>
            <?php if (empty($productosAlerta)): ?>
                <div class="uk-alert-success uk-margin-top" uk-alert><p class="uk-margin-remove">Inventario en orden: no hay productos que requieran reposición.</p></div>
            <?php endif; ?>
        </section>

        <section class="uk-card uk-card-default uk-card-body uk-margin-large-top app-home-statistics" aria-labelledby="homeStatisticsTitle">
            <div class="uk-flex uk-flex-between uk-flex-middle uk-flex-wrap" style="gap: 16px;">
                <div>
                    <h3 id="homeStatisticsTitle" class="uk-card-title uk-margin-small-bottom">Resumen de ventas del mes</h3>
                    <p class="uk-text-muted uk-margin-remove">Indicadores rápidos; los rankings y filtros están en el módulo de estadísticas.</p>
                </div>
                <a href="?c=CHome/estadisticas" class="uk-button uk-button-primary">
                    Ver estadísticas <span uk-icon="icon: arrow-right"></span>
                </a>
            </div>
            <?php $resumenEstadistico = $resumenEstadistico ?? []; ?>
            <div class="app-stock-summary-grid app-home-statistics-grid">
                <div><div class="app-stock-summary-stat"><div class="app-stock-summary-label">Ventas del mes</div><strong class="app-stock-summary-value">$ <?= number_format((float) ($resumenEstadistico['ventas_usd'] ?? 0), 2, ',', '.') ?></strong></div></div>
                <div><div class="app-stock-summary-stat"><div class="app-stock-summary-label">Unidades vendidas</div><strong class="app-stock-summary-value"><?= number_format((float) ($resumenEstadistico['unidades_vendidas'] ?? 0), 0, ',', '.') ?></strong></div></div>
                <div><div class="app-stock-summary-stat"><div class="app-stock-summary-label">Ganancia bruta estimada</div><strong class="app-stock-summary-value"><?= ($resumenEstadistico['ganancia_bruta_usd'] ?? null) === null ? 'Sin costo completo' : '$ ' . number_format((float) $resumenEstadistico['ganancia_bruta_usd'], 2, ',', '.') ?></strong></div></div>
            </div>
        </section>

        <div class="uk-margin-large-top uk-text-muted uk-text-small">
            Rol: <?= htmlspecialchars($_SESSION['rol']) ?> | <?= date('d/m/Y H:i') ?>
        </div>
    </div>

    <?php require_once __DIR__ . '/complements/footer.php'; ?>
</body>
</html>