<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Cierre de Caja (Arqueo)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow border-0">
                <div class="card-header bg-dark text-white text-center py-3">
                    <h4 class="mb-0"><i class="fa-solid fa-calculator me-2"></i>Arqueo de Caja</h4>
                </div>
                <div class="card-body p-4">
                    
                    <div class="alert alert-info">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Fondo Inicial:</span>
                            <strong>$<?= number_format($register['opening_amount'], 2) ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>+ Ventas Efectivo:</span>
                            <strong>$<?= number_format($cashSales, 2) ?></strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fs-5">
                            <span>Total Esperado:</span>
                            <strong class="text-primary">$<?= number_format($expectedTotal, 2) ?></strong>
                        </div>
                    </div>

                    <form action="<?= site_url('pos/register/close') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Dinero Contado (Real)</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="real_amount" class="form-control fw-bold text-end" placeholder="0.00" required>
                            </div>
                            <div class="form-text">Cuenta billetes y monedas en la caja.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Notas / Observaciones</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Ej. Se pagó $50 al proveedor de hielo..."></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-danger py-3 fs-5" onclick="return confirm('¿Seguro que deseas cerrar turno? Esta acción no se puede deshacer.')">
                                <i class="fa-solid fa-lock me-2"></i> CERRAR TURNO
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="<?= site_url('pos') ?>" class="text-muted">Volver al POS</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>