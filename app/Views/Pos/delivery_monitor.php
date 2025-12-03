<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Pedidos Listos | Pase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <meta http-equiv="refresh" content="10">
    <meta name="<?= csrf_token() ?>" content="<?= csrf_hash() ?>" class="csrf-token">

    <style>
        body { background-color: #198754; color: white; padding: 20px; } /* Fondo Verde */
        .card { border: none; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 20px rgba(0,0,0,0.2); }
        .card-header { background-color: #fff; color: #198754; font-weight: bold; font-size: 1.5rem; padding: 15px; }
        .item-list { list-style: none; padding: 0; margin: 0; color: #333; }
        .item-list li { padding: 8px 15px; border-bottom: 1px solid #eee; font-size: 1.1rem; }
        .waiting-time { font-size: 0.9rem; color: #666; font-weight: normal; }
        
        /* Animación para llamar la atención cuando recién aparece */
        @keyframes popIn {
            0% { transform: scale(0.8); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        .new-order { animation: popIn 0.5s ease-out; }
    </style>
</head>
<body>

<div class="d-flex justify-content-between align-items-center mb-5">
    <h1><i class="fa-solid fa-bell-concierge me-3"></i>Zona de Pase / Pedidos Listos</h1>
    <a href="<?= site_url('pos') ?>" class="btn btn-outline-light">Ir al POS</a>
</div>

<div class="row g-4">
    <?php if(empty($orders)): ?>
        <div class="col-12 text-center mt-5">
            <div class="opacity-50">
                <i class="fa-solid fa-utensils fa-5x mb-3"></i>
                <h3>Sin pedidos pendientes de entrega</h3>
            </div>
        </div>
    <?php endif; ?>

    <?php foreach($orders as $order): ?>
    <div class="col-md-6 col-xl-4 new-order">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><?= esc($order['table_name'] ?? 'Para Llevar') ?></span>
                <span class="badge bg-success fs-6">#<?= esc($order['order_number']) ?></span>
            </div>
            
            <div class="card-body bg-light">
                <div class="d-flex justify-content-between mb-2 text-muted small">
                    <span>Mesero: <?= esc($order['waiter_name']) ?></span>
                    <span>Listo hace: <?= round((time() - strtotime($order['updated_at'])) / 60) ?> min</span>
                </div>
                
                <ul class="item-list bg-white rounded border">
                    <?php foreach($order['items'] as $item): ?>
                        <li><?= $item['quantity'] ?>x <?= esc($item['product_name']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="card-footer bg-white p-3">
                <button class="btn btn-success w-100 py-3 fs-5 fw-bold shadow-sm" onclick="markDelivered(<?= $order['id'] ?>)">
                    <i class="fa-solid fa-check me-2"></i> ENTREGAR A MESA
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<script>
    async function markDelivered(id) {
        // Bloquear doble click
        if(!confirm('¿Confirmas que ya llevaste este pedido?')) return;

        const csrfName = document.querySelector('.csrf-token').getAttribute('name');
        const csrfHash = document.querySelector('.csrf-token').getAttribute('content');

        try {
            const response = await fetch(`<?= site_url('pos/ready') ?>/${id}/deliver`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfHash
                },
                body: JSON.stringify({ [csrfName]: csrfHash })
            });

            const result = await response.json();
            if(result.status === 'success') {
                window.location.reload();
            }
        } catch (error) {
            console.error(error);
        }
    }
</script>

</body>
</html>