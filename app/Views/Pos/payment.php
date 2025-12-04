<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Ticket Intenso 58mm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@500;700&family=Inconsolata:wght@700&display=swap" rel="stylesheet">

    <meta name="<?= csrf_token() ?>" content="<?= csrf_hash() ?>" class="csrf-token">

    <style>
        /* =========================================================
           ESTILOS DE PANTALLA (Previsualización)
           ========================================================= */
        body {
            background-color: #2c3e50;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Ubuntu', sans-serif;
        }

        .ticket-preview-wrapper {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
        }

        .ticket-visual {
            width: 58mm;
            min-height: 100mm;
            padding: 2mm;
            margin: 0 auto;
            border: 1px solid #ddd;
        }

        /* =========================================================
           ESTILOS DE IMPRESIÓN (INTENSO Y PEQUEÑO)
           ========================================================= */
        @media print {
            @page {
                size: 58mm auto;
                margin: 0;
            }
            body { background: white; margin: 0; padding: 0; }
            .no-print, .no-print * { display: none; height: 0; }
            .ticket-preview-wrapper { padding: 0; border: none; box-shadow: none; }

            .ticket-visual {
                width: 48mm; /* Área efectiva */
                margin-left: 0mm; /* Centrado */
                padding: 0;
                border: none;
                position: absolute; left: 0px; right: 0px; top: 0;
            }

            /* --- TIPOGRAFÍA INTENSA --- */
            * {
                font-family: 'Ubuntu', sans-serif !important;
                color: #000000 !important; /* Negro Puro */
                line-height: 1; /* Líneas pegaditas */
            }

            /* Texto General Pequeño y Negrita */
            .text-base {
                font-size: 9px; /* Reducido */
                font-weight: 300; /* Intenso */
            }

            /* Metadatos muy pequeños */
            .text-sm {
                font-size: 9px;
                font-weight: 500;
            }

            /* Números (Precios) */
            .mono {
                font-family: 'Inconsolata', monospace !important;
                font-size: 11px; /* Un poco más grande para leerse bien */
                font-weight: 700; /* Muy Negro */
                letter-spacing: -0.5px;
            }

            /* Títulos */
            .header-brand {
                font-size: 13px !important;
                font-weight: 900; /* Extra Negro */
                text-transform: uppercase;
            }

            /* Líneas divisorias Sólidas (Más visibles que dashed) */
            .separator {
                border-bottom: 1px solid #000;
                margin: 4px 0;
                width: 100%;
            }

            /* Layout de Filas */
            .row-flex { display: flex; justify-content: space-between; margin-bottom: 2px; }

            /* Columnas ajustadas para fuente pequeña */
            .col-qty { width: 10%; font-size: 10px; font-weight: 400; }
            .col-name {
                width: 55%;
                font-size: 10px;
                font-weight: 700;
                overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
            }
            .col-price { width: 35%; text-align: right; padding-right: 10px }

            /* Totales Grandes */
            .total-row { display: flex; justify-content: space-between; margin-top: 2px; }
            .grand-total {
                font-size: 14px;
                font-weight: 900;
                border-top: 2px solid #000;
                padding-top: 3px;
                margin-top: 3px;
                padding-right: 10px;
                text-align: right;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row justify-content-center">

        <div class="col-auto mb-4 mb-md-0 ticket-preview-wrapper">
            <div class="ticket-visual">

                <div class="text-center">
                    <div class="header-brand">EL QUIOSCO</div>
                    <div class="text-sm">San Juan Xiutetelco, Pue.</div>
                    <div class="text-sm">Tel: 231-151-1217</div>
                    <span class="text-sm"><?= date('d/m/y H:i') ?></span>
                    <div class="separator"></div>
                </div>

                <div class="row-flex text-sm">
                    <span>Folio: <strong><?= esc($order['order_number']) ?></strong></span>

                </div>
                <div class="row-flex text-sm">
                    <span>Le atendió: <?= esc(substr($order['waiter_name'], 0, 10)) ?></span>
                </div>
                <div class="row-flex text-sm">
                    <span><?= esc($order['table_name'] ?? 'Barra') ?></span>
                </div>

                <div class="separator"></div>

                <div class="items-list">
                    <?php foreach($items as $item): ?>
                        <div class="row-flex">
                            <div class="col-qty"><?= $item['quantity'] ?></div>
                            <div class="col-name"><?= esc($item['product_name']) ?></div>
                            <div class="col-price mono">$<?= number_format($item['price'] * $item['quantity'], 2) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="separator"></div>

                <div class="total-row text-base">
                    <span>Subtotal</span>
                    <span class="mono">$<?= number_format($calculatedTotal, 2) ?></span>
                </div>

                <div class="total-row grand-total">
                    <span>TOTAL</span>
                    <span class="mono">$<?= number_format($calculatedTotal, 2) ?></span>
                </div>

                <div class="text-center mt-3 text-sm">
                    <div style="font-weight: 700;">¡Gracias por su visita!</div>
                    <div>“La vida es corta, bebe un buen café”</div>
                    <br>
                </div>

            </div>
        </div>

        <div class="col-md-4 no-print ms-md-4">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4">
                    <button class="btn btn-dark w-100 mb-4 py-2" onclick="window.print()">
                        <i class="fa-solid fa-print me-2"></i> Imprimir Ticket
                    </button>

                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-success" onclick="processPayment('cash')">EFECTIVO</button>
                        <button class="btn btn-outline-primary" onclick="processPayment('card')">TARJETA</button>
                    </div>

                    <a href="<?= site_url('pos/tables') ?>" class="btn btn-link text-muted w-100 mt-3 text-decoration-none">Cancelar</a>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    async function processPayment(method) {
        if(!confirm(`¿Cerrar cuenta con pago en ${method.toUpperCase()}?`)) return;

        // ... (Mismo script JS de siempre) ...
        const csrfName = document.querySelector('.csrf-token').getAttribute('name');
        const csrfHash = document.querySelector('.csrf-token').getAttribute('content');

        try {
            const response = await fetch(`<?= site_url('pos/payment/'.$order['id']) ?>/pay`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfHash },
                body: JSON.stringify({ payment_method: method, [csrfName]: csrfHash })
            });
            const result = await response.json();
            if (result.status === 'success') { window.location.href = '<?= site_url('pos/tables') ?>'; }
            else { alert('Error: ' + result.message); }
        } catch (error) { console.error(error); alert('Error de conexión'); }
    }
</script>

</body>
</html>