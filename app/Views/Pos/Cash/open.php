<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Apertura de Caja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">

    <div class="card shadow p-4 text-center" style="width: 400px;">
        <div class="mb-4">
            <span class="fa-stack fa-2x">
                <i class="fas fa-circle fa-stack-2x text-primary"></i>
                <i class="fas fa-cash-register fa-stack-1x fa-inverse"></i>
            </span>
            <h3 class="mt-2">Iniciar Turno</h3>
            <p class="text-muted">Ingresa el monto inicial en caja (Fondo)</p>
        </div>

        <form action="<?= site_url('pos/register/open') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="form-floating mb-3">
                <input type="number" step="0.01" class="form-control fs-3 text-center fw-bold" id="amount" name="amount" placeholder="0.00" required autofocus>
                <label for="amount">Monto Inicial ($)</label>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fs-5">
                ABRIR CAJA
            </button>
        </form>
        
        <div class="mt-3">
            <a href="<?= site_url('logout') ?>" class="text-decoration-none text-muted small">Cancelar y Salir</a>
        </div>
    </div>

</body>
</html>