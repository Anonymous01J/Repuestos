<!DOCTYPE html>
<html lang="es">
<?php
include_once __DIR__ . '/complements/head.php';
include_once __DIR__ . '/complements/header.php';

$rankings = $rankings ?? [];
$categorias = $categorias ?? [];
$periodo = $periodo ?? 'MES';
$fechaDesde = $fechaDesde ?? '';
$fechaHasta = $fechaHasta ?? '';
$categoriaId = $categoriaId ?? null;
$estadisticaError = $estadisticaError ?? null;

$rankingConfig = [
    'mas_vendidos' => ['title' => 'Más vendidos', 'metric' => 'unidades_vendidas', 'label' => 'Unidades'],
    'menos_vendidos' => ['title' => 'Menos vendidos', 'metric' => 'unidades_vendidas', 'label' => 'Unidades'],
    'mas_rentables' => ['title' => 'Mayor rentabilidad', 'metric' => 'rentabilidad_pct', 'label' => 'Rentabilidad'],
    'menos_rentables' => ['title' => 'Menor rentabilidad', 'metric' => 'rentabilidad_pct', 'label' => 'Rentabilidad'],
    'mayor_ganancia' => ['title' => 'Mayor ganancia bruta', 'metric' => 'ganancia_bruta_usd', 'label' => 'Ganancia USD'],
    'menor_ganancia' => ['title' => 'Menor ganancia bruta', 'metric' => 'ganancia_bruta_usd', 'label' => 'Ganancia USD'],
];

$formatUsd = static fn($value) => '$ ' . number_format((float) $value, 2, ',', '.');
$formatInteger = static fn($value) => number_format((float) $value, 0, ',', '.');
$formatMetric = static function ($value, string $metric) use ($formatUsd, $formatInteger): string {
    if ($value === null) return 'Sin costo conocido';
    if ($metric === 'rentabilidad_pct') return number_format((float) $value, 2, ',', '.') . '%';
    if ($metric === 'ganancia_bruta_usd') return $formatUsd($value);
    if ($metric === 'unidades_vendidas') return $formatInteger($value);
    return number_format((float) $value, 2, ',', '.');
};

$chartMasVendidos = array_slice($rankings['mas_vendidos'] ?? [], 0, 5);
$chartMayorGanancia = array_slice($rankings['mayor_ganancia'] ?? [], 0, 5);
?>

<main class="uk-container uk-container-large app-dashboard-shell app-statistics-page">
    <div class="uk-flex uk-flex-between uk-flex-middle uk-flex-wrap uk-margin-bottom" style="gap: 12px;">
        <div>
            <h1 class="app-section-title uk-margin-remove">Estadísticas de ventas</h1>
            <p class="uk-text-muted uk-margin-small-top">Rendimiento, rentabilidad y ganancia bruta por producto.</p>
        </div>
        <a href="?c=CHome/view" class="uk-button uk-button-default">Volver al panel</a>
    </div>

    <form method="GET" class="uk-card uk-card-default uk-card-body app-statistics-filters uk-margin-bottom">
        <input type="hidden" name="c" value="CHome/estadisticas">
        <div class="uk-grid-small uk-child-width-1-2@s uk-child-width-1-4@m" uk-grid>
            <div>
                <label class="uk-form-label" for="periodoEstadisticas">Período</label>
                <select id="periodoEstadisticas" name="periodo" class="uk-select">
                    <option value="MES" <?= $periodo === 'MES' ? 'selected' : '' ?>>Este mes</option>
                    <option value="ULTIMOS_6_MESES" <?= $periodo === 'ULTIMOS_6_MESES' ? 'selected' : '' ?>>Últimos 6 meses</option>
                    <option value="ANIO" <?= $periodo === 'ANIO' ? 'selected' : '' ?>>Este año</option>
                    <option value="INTERVALO" <?= $periodo === 'INTERVALO' ? 'selected' : '' ?>>Intervalo personalizado</option>
                    <option value="TODO" <?= $periodo === 'TODO' ? 'selected' : '' ?>>Todo el historial</option>
                </select>
            </div>
            <div class="statistics-date-field">
                <label class="uk-form-label" for="fechaDesdeEstadisticas">Desde</label>
                <input id="fechaDesdeEstadisticas" type="date" name="fecha_desde" class="uk-input" value="<?= htmlspecialchars((string) $fechaDesde, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="statistics-date-field">
                <label class="uk-form-label" for="fechaHastaEstadisticas">Hasta</label>
                <input id="fechaHastaEstadisticas" type="date" name="fecha_hasta" class="uk-input" value="<?= htmlspecialchars((string) $fechaHasta, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div>
                <label class="uk-form-label" for="categoriaEstadisticas">Categoría</label>
                <select id="categoriaEstadisticas" name="categoria_id" class="uk-select select2-categoria-estadisticas">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= (int) $categoria['id'] ?>" <?= (string) $categoriaId === (string) $categoria['id'] ? 'selected' : '' ?>><?= htmlspecialchars($categoria['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="uk-flex uk-flex-right uk-margin-top" style="gap: 8px;">
            <a href="?c=CHome/estadisticas" class="uk-button uk-button-default">Limpiar</a>
            <button type="submit" class="uk-button uk-button-primary">Aplicar filtros</button>
        </div>
    </form>

    <?php if ($estadisticaError): ?>
        <div class="uk-alert-danger" uk-alert><p><?= htmlspecialchars($estadisticaError, ENT_QUOTES, 'UTF-8') ?></p></div>
    <?php endif; ?>

    <div class="uk-grid-small uk-child-width-1-1 uk-child-width-1-2@m uk-margin-bottom" uk-grid>
        <section class="uk-card uk-card-default app-statistics-chart-card">
            <div class="uk-card-body">
                <h2 class="uk-card-title">Top 5 por unidades vendidas</h2>
                <div class="app-statistics-chart-wrap">
                    <canvas id="chartMasVendidos"></canvas>
                </div>
            </div>
        </section>
        <section class="uk-card uk-card-default app-statistics-chart-card">
            <div class="uk-card-body">
                <h2 class="uk-card-title">Top 5 por ganancia bruta</h2>
                <div class="app-statistics-chart-wrap">
                    <canvas id="chartMayorGanancia"></canvas>
                </div>
            </div>
        </section>
    </div>

    <div class="uk-grid-small uk-child-width-1-1 uk-child-width-1-2@m" uk-grid>
        <?php foreach ($rankingConfig as $key => $config): ?>
            <?php $rows = $rankings[$key] ?? []; ?>
            <section class="uk-card uk-card-default uk-card-body app-statistics-ranking" aria-labelledby="ranking-<?= htmlspecialchars($key) ?>">
                <h2 id="ranking-<?= htmlspecialchars($key) ?>" class="uk-card-title"><?= htmlspecialchars($config['title']) ?></h2>
                <?php if (empty($rows)): ?>
                    <p class="uk-text-muted uk-margin-remove-bottom">No hay datos para los filtros seleccionados.</p>
                <?php else: ?>
                    <div class="uk-overflow-auto">
                        <table class="uk-table uk-table-small uk-table-divider uk-table-middle app-statistics-table">
                            <thead><tr><th>Producto</th><th><?= htmlspecialchars($config['label']) ?></th><th>Ventas</th></tr></thead>
                            <tbody>
                                <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($row['producto'] ?? 'Producto', ENT_QUOTES, 'UTF-8') ?></strong>
                                            <span class="uk-display-block uk-text-meta"><?= htmlspecialchars($row['categoria'] ?? 'Sin categoría', ENT_QUOTES, 'UTF-8') ?></span>
                                        </td>
                                        <td><?= htmlspecialchars($formatMetric($row[$config['metric']] ?? null, $config['metric']), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= $formatUsd($row['ventas_usd'] ?? 0) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        <?php endforeach; ?>
    </div>

    <p class="uk-text-meta uk-margin-top">La ganancia es bruta estimada y utiliza el costo promedio ponderado de las entradas registradas hasta la fecha de cada venta. Las ventas sin costo conocido no se incluyen en rentabilidad ni ganancia.</p>
</main>

<?php include_once __DIR__ . '/complements/footer.php'; ?>
<script>
    if (window.jQuery && jQuery.fn.select2) {
        jQuery('.select2-categoria-estadisticas').select2({
            width: '100%',
            placeholder: 'Todas las categorías',
            allowClear: true
        });
    }

    const periodSelect = document.getElementById('periodoEstadisticas');
    const dateFields = document.querySelectorAll('.statistics-date-field');
    function updateCustomDateFields() {
        const showDates = periodSelect.value === 'INTERVALO';
        dateFields.forEach((field) => field.classList.toggle('uk-hidden', !showDates));
    }
    periodSelect.addEventListener('change', updateCustomDateFields);
    updateCustomDateFields();

    const topProductosUnidades = <?= json_encode(array_map(static function ($row) {
        return [
            'label' => (string) ($row['producto'] ?? 'Producto'),
            'value' => (float) ($row['unidades_vendidas'] ?? 0),
        ];
    }, $chartMasVendidos), JSON_THROW_ON_ERROR) ?>;

    const topProductosGanancia = <?= json_encode(array_map(static function ($row) {
        return [
            'label' => (string) ($row['producto'] ?? 'Producto'),
            'value' => (float) ($row['ganancia_bruta_usd'] ?? 0),
        ];
    }, $chartMayorGanancia), JSON_THROW_ON_ERROR) ?>;

    const chartPalette = ['#007D35', '#2ABB00', '#129740', '#8ecae6', '#9800b3'];

    if (topProductosUnidades.length) {
        new Chart(document.getElementById('chartMasVendidos'), {
            type: 'bar',
            data: {
                labels: topProductosUnidades.map(item => item.label),
                datasets: [{
                    label: 'Unidades vendidas',
                    data: topProductosUnidades.map(item => item.value),
                    borderRadius: 6,
                    backgroundColor: chartPalette,
                    borderColor: chartPalette,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    if (topProductosGanancia.length) {
        new Chart(document.getElementById('chartMayorGanancia'), {
            type: 'bar',
            data: {
                labels: topProductosGanancia.map(item => item.label),
                datasets: [{
                    label: 'Ganancia bruta USD',
                    data: topProductosGanancia.map(item => item.value),
                    borderRadius: 6,
                    backgroundColor: ['#129740','#007D35','#2ABB00','#f4d35e','#9800b3'],
                    borderColor: ['#129740','#007D35','#2ABB00','#f4d35e','#9800b3'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        ticks: {
                            callback: value => '$ ' + Number(value).toLocaleString('es-VE', { minimumFractionDigits: 0, maximumFractionDigits: 2 })
                        }
                    }
                }
            }
        });
    }
</script>
</body>
</html>
