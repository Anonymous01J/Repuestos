<?php
// Views/login.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Sistema de Repuestos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="public/assets/libs/uikit/uikit.min.css">
    <link rel="stylesheet" href="public/assets/css/uikit-theme.css">
    <link rel="stylesheet" href="public/assets/css/style.css">
</head>
<body class="uk-background-muted uk-height-viewport uk-flex uk-flex-middle">
    <div class="uk-container uk-container-small">
        <div class="uk-flex uk-flex-center">
            <div class="uk-width-1-1 uk-width-medium@m">
                <div class="uk-card uk-card-default uk-card-body uk-box-shadow-large" style="border-radius: 22px;">
                    <h3 class="uk-card-title uk-text-center uk-margin-small-bottom">Iniciar Sesión</h3>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="uk-alert uk-alert-danger uk-margin-small-bottom" role="alert">
                            Usuario o contraseña incorrectos
                        </div>
                    <?php endif; ?>

                    <form action="?c=CHome/login" method="POST" class="uk-form-stacked">
                        <div class="uk-margin">
                            <label for="usuario" class="uk-form-label">Usuario</label>
                            <div class="uk-form-controls">
                                <input type="text" class="uk-input" id="usuario" name="usuario" required autofocus>
                            </div>
                        </div>
                        <div class="uk-margin">
                            <label for="clave" class="uk-form-label">Contraseña</label>
                            <div class="uk-form-controls">
                                <input type="password" class="uk-input" id="clave" name="clave" required>
                            </div>
                        </div>
                        <div class="uk-margin-small-bottom">
                            <label class="uk-flex uk-flex-middle" for="recordar">
                                <input class="uk-checkbox" type="checkbox" id="recordar" name="recordar">
                                <span class="uk-margin-small-left">Recordarme</span>
                            </label>
                        </div>
                        <div class="uk-margin-top">
                            <button type="submit" class="uk-button uk-button-primary uk-width-1-1">Ingresar</button>
                        </div>
                    </form>
                </div>
                <p class="uk-text-center uk-text-muted uk-margin-small-top">© <?= date("Y") ?> Repuestos App</p>
            </div>
        </div>
    </div>

    <script src="public/assets/libs/uikit/uikit.min.js"></script>
    <script src="public/assets/libs/uikit/uikit-icons.min.js"></script>
</body>
</html>
