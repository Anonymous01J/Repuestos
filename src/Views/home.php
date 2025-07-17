
<!DOCTYPE html>
<html lang="es">
    <?php
        require_once __DIR__ . '../complements/head.php';
    ?>
    <?php
        require_once __DIR__ . '../complements/header.php';
    ?>
    <div class="container py-5">
        <h2 class="mb-4">Panel principal</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Clientes</h5>
                        <p class="card-text">Gestiona todos los clientes registrados.</p>
                        <a href="?c=CClientes/view" class="btn btn-primary">Ver clientes</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Productos</h5>
                        <p class="card-text">Controla tus repuestos disponibles.</p>
                        <a href="?c=CProductos/view" class="btn btn-primary">Ver productos</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Ventas</h5>
                        <p class="card-text">Realiza y revisa ventas realizadas.</p>
                        <a href="?c=CVentas/view" class="btn btn-primary">Ver ventas</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5 text-muted">
            <small>Rol: <?= $_SESSION['rol'] ?> | <?= date('d/m/Y H:i') ?></small>
        </div>
    </div>
    <?php
        require_once __DIR__ . '../complements/footer.php';
    ?>
</body>
</html>
