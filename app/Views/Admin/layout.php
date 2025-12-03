<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel Admin | Restaurante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Estilos rápidos para el sidebar */
        .sidebar { min-height: 100vh; background-color: #2c3e50; color: #fff; }
        .sidebar a { color: #bdc3c7; text-decoration: none; padding: 10px 20px; display: block; }
        .sidebar a:hover, .sidebar a.active { background-color: #34495e; color: #fff; border-left: 4px solid #e67e22; }
        .content { padding: 20px; background-color: #f8f9fa; min-height: 100vh; }
        .stat-card { border: none; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>

<div class="d-flex">
    <div class="sidebar d-flex flex-column flex-shrink-0 p-3" style="width: 250px;">
        <a href="<?= site_url('admin/dashboard') ?>" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <i class="fa-solid fa-utensils me-2 fs-4"></i>
            <span class="fs-4">Restaurante</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li>
                <a href="<?= site_url('admin/dashboard') ?>" class="active">
                    <i class="fa-solid fa-gauge me-2"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="<?= site_url('pos') ?>" target="_blank"> <i class="fa-solid fa-cash-register me-2"></i> Ir al POS
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/products') ?>">
                    <i class="fa-solid fa-burger me-2"></i> Menú y Productos
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/staff') ?>">
                    <i class="fa-solid fa-users me-2"></i> Empleados
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/reports') ?>">
                    <i class="fa-solid fa-chart-line me-2"></i> Reportes
                </a>
            </li>
        </ul>
        <hr>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-user-circle fs-4 me-2"></i>
                <strong><?= auth()->user()->username ?? 'Admin' ?></strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                <li><a class="dropdown-item" href="<?= site_url('logout') ?>">Cerrar Sesión</a></li>
            </ul>
        </div>
    </div>

    <div class="content flex-grow-1">
        <?= $this->renderSection('content') ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>