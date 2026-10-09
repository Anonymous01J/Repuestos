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
<div class="uk-navbar-container uk-navbar-transparent" style="background: rgba(15, 23, 42, 0.96); box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);">
    <div class="uk-container uk-container-expand" style="padding-left: 32px; padding-right: 32px;">
        <nav class="uk-navbar uk-navbar-container uk-navbar-transparent" uk-navbar>
            <div class="uk-navbar-left">
                <a class="uk-logo uk-text-bold uk-text-white" href="?c=CHome/view" style="font-size: 1.15rem; letter-spacing: 0.02em;">RepuestosApp</a>
            </div>

            <div class="uk-navbar-right">
                <div class="uk-flex uk-flex-middle uk-flex-wrap uk-flex-wrap-around">
                    <button class="uk-button uk-button-text uk-margin-small-right uk-flex uk-flex-middle uk-text-white" id="bcvDiv" type="button" data-bs-toggle="modal" data-bs-target="#modalTasa" style="padding: 0; min-height: auto;">
                        <img src="./public/assets/img/bcv2.png" alt="BCV" width="40" style="border-radius: 8px;">
                        <span class="uk-margin-small-left uk-text-bold">Dólar BCV:</span>
                        <span class="uk-text-success uk-margin-small-left uk-text-bold" id="Dolar_bcv"><?= $tasaValor ?> Bs</span>
                    </button>

                    <div class="uk-flex uk-flex-middle uk-margin-left">
                        <span class="uk-text-white uk-text-small uk-margin-small-right">
                            Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?>
                        </span>
                        <a href="?c=CHome/logout" class="uk-button uk-button-default uk-button-small" style="border-color: rgba(255,255,255,0.35); color: #fff;">Cerrar sesión</a>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</div>

<!-- Modal Tasa -->
<div class="modal fade" id="modalTasa" tabindex="-1" aria-labelledby="modalTasaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="formTasa">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTasaLabel">Actualizar Tasa BCV</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="inputTasa" class="form-label">Valor actual (Bs):</label>
            <input type="text" step="0.01" class="form-control" id="inputTasa" name="valor" required>
            <input type="hidden" id="inputTasaId" name="id">
          </div>
          <div class="mb-2">
            <button type="button" class="btn btn-outline-primary btn-sm" id="btnActualizarApi">Actualizar con API BCV</button>
          </div>
          <div>
            <label class="form-label">Última actualización:</label>
            <span id="fechaActualizacion" class="fw-bold"></span>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </form>
    </div>
  </div>
</div>