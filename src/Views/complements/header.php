<?php
    use Anonymous01j\Repuestos\Models\Tasa;
    date_default_timezone_set('America/Caracas');
    $tasaValor = "0.00";
    if (class_exists('Anonymous01j\Repuestos\Models\Tasa')) {
        $tasa = new Tasa();
        $tasaData = $tasa->search();
        if ($tasaData && count($tasaData) > 0) {
            $tasaValor = number_format($tasaData[0]['valor'], 2, '.', '');
        }
    }
?>
<div id="appLoader" class="app-loader" aria-live="polite" aria-busy="true">
    <div class="app-loader-inner">
        <div class="app-loader-mark">R</div>
        <div class="app-loader-ring"></div>
        <div class="app-loader-text">Cargando sistema</div>
    </div>
</div>
<header class="app-shell-header">
    <div class="uk-container uk-container-expand app-header-inner">
        <nav class="app-header-nav" aria-label="Navegación principal">
            <a class="app-brand" href="?c=CHome/view" aria-label="Ir al inicio">
                <span class="app-brand-mark">R</span>
                <span class="app-brand-text">Inventario de Repuestos</span>
            </a>

            <div class="app-header-actions">
                <button class="theme-toggle-btn" id="themeToggleBtn" type="button" aria-label="Cambiar a tema claro" title="Cambiar tema">
                  <span uk-icon="icon: settings; ratio: 1.15" aria-hidden="true"></span>
                </button>

                <button class="uk-button uk-button-text app-bcv-trigger" id="bcvDiv" type="button" uk-toggle="target: #modalTasa" aria-label="Actualizar tasa BCV">
                    <img id="bcvIcon" src="./public/assets/img/bcv2.png" alt="BCV" width="34" height="34">
                    <span class="app-bcv-label">Dólar BCV</span>
                    <span class="uk-text-success uk-text-bold app-bcv-value" id="Dolar_bcv"><?= $tasaValor ?> Bs</span>
                    <span class="app-bcv-edit-icon" uk-icon="icon: pencil; ratio: 0.8" aria-hidden="true"></span>
                </button>

                <div class="app-user-box">
                    <span class="app-user-avatar" aria-hidden="true">
                        <?= strtoupper(substr(htmlspecialchars($_SESSION['nombre'] ?? ''), 0, 1)) ?>
                    </span>
                    <span class="app-user-meta" title="<?= htmlspecialchars($_SESSION['nombre'] ?? '') ?>">
                        <?= htmlspecialchars($_SESSION['nombre'] ?? 'Usuario') ?>
                    </span>
                    <a href="?c=CHome/logout" class="app-logout-btn" aria-label="Cerrar sesión" title="Cerrar sesión">
                        <span uk-icon="icon: sign-out; ratio: 1" aria-hidden="true"></span>
                        <span class="app-logout-label">Salir</span>
                    </a>
                </div>
            </div>
        </nav>
    </div>
</header>

<!-- Modal Tasa -->
<div id="modalTasa" uk-modal>
  <div class="uk-modal-dialog uk-modal-body uk-width-1-2@m">
    <button class="uk-modal-close-default" type="button" uk-close></button>
    <h2 class="uk-modal-title" id="modalTasaLabel">Actualizar Tasa BCV</h2>
    <form id="formTasa">
      <div class="uk-margin">
        <label for="inputTasa" class="uk-form-label">Valor actual (Bs):</label>
        <div class="uk-form-controls">
          <input type="text" step="0.01" class="uk-input" id="inputTasa" name="valor" required>
          <input type="hidden" id="inputTasaId" name="id">
        </div>
      </div>
      <div class="uk-margin-small-bottom">
        <button type="button" class="uk-button uk-button-default uk-button-small" id="btnActualizarApi">Actualizar con API BCV</button>
      </div>
      <div class="uk-margin-small-bottom">
        <label class="uk-form-label">Última actualización:</label>
        <span id="fechaActualizacion" class="uk-text-bold"></span>
      </div>
      <div class="uk-text-right">
        <button type="button" class="uk-button uk-button-default uk-modal-close">Cerrar</button>
        <button type="submit" class="uk-button uk-button-primary">Guardar</button>
      </div>
    </form>
  </div>
</div>