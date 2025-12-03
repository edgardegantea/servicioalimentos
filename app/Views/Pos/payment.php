<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Cerrar Cuenta | POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="<?= csrf_token() ?>" content="<?= csrf_hash() ?>" class="csrf-token">

    <style>
        body { background-color: #e9ecef; height: 100vh; display: flex; align-items: center; justify-content: center; }
        
        /* Estilo del Ticket Visual */
        .ticket-visual {
            background: white;
            width: 100%;
            max-width: 400px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            font-family: 'Courier New', Courier, monospace; /* Fuente tipo ticket */
            border-top: 5px solid #2c3e50;
        }
        .dashed-line { border-bottom: 2px dashed #ccc; margin: 15px 0; }
        .total-row { font-size: 1.5rem; font-weight: bold; margin-top: 10px; }
        
        /* Ocultar elementos al imprimir */
        @media print {
            body * { visibility: hidden; }
            .ticket-visual, .ticket-visual * { visibility: visible; }
            .ticket-visual { position: absolute; left: 0; top: 0; box-shadow: none; border: none; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center align-items-center">
        
        <div class="col-md-6 col-lg-5 d-flex justify-content-center mb-4 mb-md-0">
            <div class="ticket-visual">
                <div class="text-center mb-4">
                    <h4 class="fw-bold text-uppercase">EL QUIOSCO</h4>
                    <p class="mb-0">San Juan Xiutetelco, Pue.</p>
                    <small>Tel: +52 231 151 1217</small>
                </div>

                <div class="d-flex justify-content-between small text-muted">
                    <span>Folio: <?= esc($order['order_number']) ?></span>
                    <span><?= date('d/m/Y H:i') ?></span>
                </div>
                <div class="d-flex justify-content-between small text-muted mb-2">
                    <span>Mesa: <?= esc($order['table_name'] ?? 'Barra') ?></span>
                    <span>Atendió: <?= esc($order['waiter_name']) ?></span>
                </div>

                <div class="dashed-line"></div>

                <?php foreach($items as $item): ?>
                <div class="d-flex justify-content-between mb-1">
                    <span><?= $item['quantity'] ?> x <?= esc($item['product_name']) ?></span>
                    <span>$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                </div>
                <?php endforeach; ?>

                <div class="dashed-line"></div>

                <div class="d-flex justify-content-between">
                    <span>Subtotal:</span>
                    <span>$<?= number_format($calculatedTotal, 2) ?></span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>IVA (0%):</span> <span>$0.00</span>
                </div>
                
                <div class="d-flex justify-content-between total-row">
                    <span>TOTAL:</span>
                    <span>$<?= number_format($calculatedTotal, 2) ?></span>
                </div>

                <div class="text-center mt-4">
                    <small>¡Gracias por su preferencia!</small><br>
                    <small>www.cafeelquiosco.com</small>
                </div>
            </div>
        </div>

        <div class="col-md-5 col-lg-4 no-print">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <h4 class="mb-4">Cerrar Cuenta</h4>
                    
                    <button class="btn btn-secondary w-100 mb-3 py-2" onclick="window.print()">
                        <i class="fa-solid fa-print me-2"></i> Imprimir Pre-Cuenta
                    </button>

                    <hr>
                    <label class="form-label text-muted">Seleccionar Método de Pago:</label>

                    <div class="d-grid gap-3">
                        <button class="btn btn-success btn-lg py-3" onclick="processPayment('cash')">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fa-solid fa-money-bill-wave fa-2x me-3"></i>
                                <div class="text-start">
                                    <div class="fs-6">Cobrar en</div>
                                    <div class="fw-bold">EFECTIVO</div>
                                </div>
                            </div>
                        </button>

                        <button class="btn btn-primary btn-lg py-3" onclick="processPayment('card')">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fa-solid fa-credit-card fa-2x me-3"></i>
                                <div class="text-start">
                                    <div class="fs-6">Cobrar con</div>
                                    <div class="fw-bold">TARJETA</div>
                                </div>
                            </div>
                        </button>
                    </div>

                    <a href="<?= site_url('pos/tables') ?>" class="btn btn-outline-danger w-100 mt-4">
                        Cancelar / Volver
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    async function processPayment(method) {
        if(!confirm(`¿Confirmar pago en ${method.toUpperCase()} y cerrar mesa?`)) return;

        const csrfName = document.querySelector('.csrf-token').getAttribute('name');
        const csrfHash = document.querySelector('.csrf-token').getAttribute('content');

        try {
            const response = await fetch(`<?= site_url('pos/payment/'.$order['id']) ?>/pay`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfHash
                },
                body: JSON.stringify({ payment_method: method, [csrfName]: csrfHash })
            });

            const result = await response.json();

            if (result.status === 'success') {
                alert('¡Mesa cerrada y cobrada correctamente!');
                window.location.href = '<?= site_url('pos/tables') ?>'; // Volver al mapa de mesas
            } else {
                alert('Error: ' + result.message);
            }

        } catch (error) {
            console.error(error);
            alert('Error de conexión');
        }
    }
</script>

</body>
</html>