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
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 py-2">
    <div class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between">
        <div class="d-flex align-items-center mb-2 mb-md-0" id="bcvDiv" style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#modalTasa">
            <img class="img-bcv me-2" src="./public/assets/img/bcv2.png" alt="BCV" width="40">
            <span class="text-white fw-semibold me-2">Dólar BCV:</span>
            <span class="text-info fw-bold" id="Dolar_bcv"><?= $tasaValor ?> Bs</span>
        </div>
        <div class="d-flex align-items-center">
            <a class="navbar-brand me-4" href="?c=CHome/view">RepuestosApp</a>
            <span class="text-white me-3">
                Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?>
            </span>
            <a href="?c=CHome/logout" class="btn btn-sm btn-outline-light">Cerrar sesión</a>
        </div>
    </div>
</nav>
<!-- Modal Tasa -->
<div class="modal fade" id="modalTasa" tabindex="-1" aria-labelledby="modalTasaLabel" aria-hidden="true">
  <div class="modal-dialog">
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