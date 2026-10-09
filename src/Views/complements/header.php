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
<div class="uk-navbar-container app-shell-header">
    <div class="uk-container uk-container-expand app-header-inner">
        <nav class="uk-navbar uk-navbar-container uk-navbar-transparent" uk-navbar>
            <div class="uk-navbar-left">
                <a class="app-brand" href="?c=CHome/view" aria-label="Ir al inicio">
                    <span class="app-brand-mark">R</span>
                    <span class="app-brand-text">RepuestosApp</span>
                </a>
            </div>

            <div class="uk-navbar-right">
                <div class="uk-flex uk-flex-middle uk-flex-wrap uk-flex-wrap-around app-header-actions">
                    <button class="theme-toggle-btn" id="themeToggleBtn" type="button" aria-label="Cambiar tema">
                        <span uk-icon="icon: sun; ratio: 1.2"></span>
                    </button>

                    <button class="uk-button uk-button-text app-bcv-trigger" id="bcvDiv" type="button" uk-toggle="target: #modalTasa">
                        <img src="./public/assets/img/bcv2.png" alt="BCV" width="40">
                        <span class="app-bcv-label">Dólar BCV:</span>
                        <span class="uk-text-success uk-text-bold app-bcv-value" id="Dolar_bcv"><?= $tasaValor ?> Bs</span>
                    </button>

                    <div class="app-user-box">
                        <div class="app-user-avatar">
                            <?= strtoupper(substr(htmlspecialchars($_SESSION['nombre']), 0, 1)) ?>
                        </div>
                        <div class="app-user-meta">
                            <span>Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?></span>
                        </div>
                        <a href="?c=CHome/logout" class="uk-button uk-button-small app-logout-btn">Cerrar sesión</a>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</div>

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