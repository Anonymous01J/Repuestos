<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 py-2">
    <div class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between">
        <div class="d-flex align-items-center mb-2 mb-md-0">
            <img class="img-bcv me-2" src="./public/assets/img/bcv2.png" alt="BCV" width="40">
            <span class="text-white fw-semibold me-2">Dólar BCV:</span>
            <span class="text-info fw-bold" id="Dolar_bcv">0.00 Bs</span>
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