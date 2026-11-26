<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Licencia - RepuestosApp</title>
    <link rel="icon" type="image/svg+xml" href="public/assets/img/favicon.svg">
    <link rel="manifest" href="site.webmanifest">
    <link rel="apple-touch-icon" href="public/assets/img/apple-touch-icon.png">
    <link rel="stylesheet" href="public/assets/libs/uikit/uikit.min.css">
    <link rel="stylesheet" href="public/assets/css/uikit-theme.css">
    <link rel="stylesheet" href="public/assets/css/style.css">
</head>
<body class="app-auth-body uk-height-viewport uk-flex uk-flex-middle">
    <main class="uk-container uk-container-small">
        <section class="uk-card uk-card-default uk-card-body app-auth-card">
            <h1 class="uk-card-title">Licencia de RepuestosApp</h1>
            <p><?= htmlspecialchars($licenseMessage, ENT_QUOTES, 'UTF-8') ?></p>

            <?php if ($licenseFeedback): ?>
                <div class="uk-alert uk-alert-danger" role="alert">
                    <?= htmlspecialchars($licenseFeedback, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <?php if ($licenseStatus === 'active'): ?>
                <a href="?c=CHome/view" class="uk-button uk-button-primary">Continuar</a>
            <?php elseif (!in_array($licenseStatus, ['storage_unavailable', 'signing_key_missing'], true)): ?>
                <form action="?c=CLicense/activate" method="POST" class="uk-form-stacked">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['license_csrf'], ENT_QUOTES, 'UTF-8') ?>">
                    <div class="uk-margin">
                        <label class="uk-form-label" for="licenseToken">Token</label>
                        <textarea id="licenseToken" name="token" class="uk-textarea" rows="5" required autocomplete="off"></textarea>
                    </div>
                    <button type="submit" class="uk-button uk-button-primary">Activar licencia</button>
                </form>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>