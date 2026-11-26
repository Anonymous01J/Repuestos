<!DOCTYPE html>
<html lang="es">
<?php $licenseFeedback = $licenseFeedback ?? null; ?>
<?php require_once __DIR__ . '/complements/head.php'; ?>
<?php require_once __DIR__ . '/complements/header.php'; ?>

<main class="uk-container uk-container-large app-dashboard-shell">
    <div class="uk-flex uk-flex-between uk-flex-middle uk-flex-wrap uk-margin-bottom" style="gap: 12px;">
        <div>
            <h1 class="app-section-title uk-margin-remove">Configuración de licencia</h1>
            <p class="uk-text-muted uk-margin-small-top">Consulta la vigencia y activa el token de renovación entregado por el proveedor.</p>
        </div>
        <a href="?c=CHome/view" class="uk-button uk-button-default">Volver al panel</a>
    </div>

    <section class="uk-card uk-card-default uk-card-body" aria-labelledby="licenseStatusTitle">
        <h2 id="licenseStatusTitle" class="uk-card-title">Estado actual</h2>
        <p>
            Estado: <strong><?= ($licenseSummary['status'] ?? '') === 'active' ? 'Activa' : 'Revisar licencia' ?></strong>
            <?php if (!empty($licenseSummary['expires_at'])): ?>
                <br>Vencimiento: <?= gmdate('d/m/Y H:i', (int) $licenseSummary['expires_at']) ?> UTC
                <br>Tiempo restante: <?= (int) $licenseSummary['days_remaining'] ?> días
            <?php endif; ?>
        </p>

        <?php if (!empty($licenseSummary['alert_message'])): ?>
            <div class="uk-alert-<?= ($licenseSummary['alert_level'] ?? '') === 'critical' ? 'danger' : 'warning' ?>" uk-alert>
                <p class="uk-margin-remove"><?= htmlspecialchars($licenseSummary['alert_message'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        <?php endif; ?>

        <?php if ($licenseFeedback): ?>
            <div class="uk-alert-danger" uk-alert>
                <p class="uk-margin-remove"><?= htmlspecialchars($licenseFeedback, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        <?php endif; ?>

        <form action="?c=CLicense/activate" method="POST" class="uk-form-stacked uk-margin-top">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['license_csrf'], ENT_QUOTES, 'UTF-8') ?>">
            <div class="uk-margin">
                <label class="uk-form-label" for="renewalToken">Token de renovación</label>
                <div class="uk-form-controls">
                    <textarea id="renewalToken" name="token" class="uk-textarea" rows="5" required autocomplete="off"></textarea>
                </div>
                <p class="uk-text-meta">La renovación empieza en la fecha de emisión del token. Solicítalo al proveedor cuando corresponda.</p>
            </div>
            <button type="submit" class="uk-button uk-button-primary">Activar renovación</button>
        </form>
    </section>
</main>

<?php require_once __DIR__ . '/complements/footer.php'; ?>
</body>
</html>