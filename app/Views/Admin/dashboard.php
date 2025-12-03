<?= $this->extend('App\Views\Admin\layout') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <h2 class="mb-4">Panel de Control</h2>

    <div class="row g-4 mb-4">
        
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card stat-card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Ventas Hoy</h6>
                            <h2 class="mb-0">$<?= number_format($salesToday, 2) ?></h2>
                        </div>
                        <i class="fa-solid fa-sack-dollar fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card stat-card bg-warning text-dark h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">En Cocina</h6>
                            <h2 class="mb-0"><?= $ordersPending ?></h2>
                        </div>
                        <i class="fa-solid fa-fire-burner fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card stat-card <?= ($lowStock > 0) ? 'bg-danger' : 'bg-success' ?> text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Alertas Stock</h6>
                            <h2 class="mb-0"><?= $lowStock ?></h2>
                            <small class="d-block mt-2">De <?= $totalProducts ?> productos</small>
                        </div>
                        <i class="fa-solid fa-box-open fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card stat-card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Ocupación Sala</h6>
                            <h2 class="mb-0"><?= $occupancy['occupied'] ?> / <?= $occupancy['total'] ?></h2>
                            <small>Mesas activas</small>
                        </div>
                        <i class="fa-solid fa-chair fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-gray-800">Accesos Directos</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <a href="<?= site_url('pos') ?>" class="btn btn-outline-primary w-100 py-3">
                                <i class="fa-solid fa-desktop fa-2x mb-2"></i><br>
                                Abrir POS
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="<?= site_url('admin/products/new') ?>" class="btn btn-outline-success w-100 py-3">
                                <i class="fa-solid fa-plus-circle fa-2x mb-2"></i><br>
                                Nuevo Producto
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="<?= site_url('admin/staff/new') ?>" class="btn btn-outline-secondary w-100 py-3">
                                <i class="fa-solid fa-user-plus fa-2x mb-2"></i><br>
                                Nuevo Empleado
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Estado del Sistema</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Versión CI4
                            <span class="badge bg-secondary rounded-pill"><?= \CodeIgniter\CodeIgniter::CI_VERSION ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Entorno
                            <span class="badge bg-success rounded-pill"><?= env('CI_ENVIRONMENT') ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>