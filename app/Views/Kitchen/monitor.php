<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>KDS Cocina | Restaurante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <meta http-equiv="refresh" content="15">
    
    <meta name="<?= csrf_token() ?>" content="<?= csrf_hash() ?>" class="csrf-token">

    <style>
        body { background-color: #2c3e50; color: #ecf0f1; padding: 20px; }
        .card { border: none; border-radius: 10px; overflow: hidden; }
        .order-header { padding: 15px; font-weight: bold; font-size: 1.1rem; color: white; }
        
        /* Colores según estado */
        .status-pending .order-header { background-color: #e67e22; } /* Naranja */
        .status-cooking .order-header { background-color: #2980b9; } /* Azul */
        
        .item-list { padding: 0; margin: 0; list-style: none; }
        .item-list li { padding: 10px 15px; border-bottom: 1px solid #eee; color: #333; font-size: 1.1rem; }
        .item-list li:last-child { border-bottom: none; }
        .badge-qty { font-size: 1rem; margin-right: 10px; }
        
        .timer { font-size: 0.8rem; opacity: 0.8; font-weight: normal; }
    </style>
</head>
<body>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fa-solid fa-fire-burner me-3"></i>Monitor de Cocina</h1>
    <span class="badge bg-light text-dark fs-5" id="clock">00:00:00</span>
</div>

<div class="row g-4" id="orders-container">
    <?php if(empty($orders)): ?>
        <div class="col-12 text-center mt-5">
            <i class="fa-solid fa-check-circle fa-5x text-success mb-3"></i>
            <h3>Todo está tranquilo</h3>
            <p class="text-muted">No hay comandas pendientes</p>
        </div>
    <?php endif; ?>

    <?php foreach($orders as $order): ?>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card shadow h-100 <?= 'status-' . $order['status'] ?>">
            
            <div class="order-header d-flex justify-content-between align-items-start">
                <div>
                    <div>#<?= esc($order['order_number']) ?></div>
                    <div class="fs-4"><?= esc($order['table_name'] ?? 'Delivery') ?></div>
                    <small><i class="fa-solid fa-user me-1"></i> <?= esc($order['waiter_name'] ?? 'Web') ?></small>
                </div>
                <div class="text-end">
                    <div class="timer"><?= date('H:i', strtotime($order['created_at'])) ?></div>
                    <?php 
                        $mins = round((time() - strtotime($order['created_at'])) / 60);
                        $color = ($mins > 20) ? 'text-danger fw-bold bg-white px-1 rounded' : '';
                    ?>
                    <small class="<?= $color ?>"><?= $mins ?> min</small>
                </div>
            </div>

            <ul class="item-list bg-white flex-grow-1">
                <?php foreach($order['items'] as $item): ?>
                <li class="d-flex align-items-center">
                    <span class="badge bg-secondary badge-qty"><?= $item['quantity'] ?></span>
                    <div>
                        <?= esc($item['product_name']) ?>
                        <?php if(!empty($item['options'])): ?>
                            <br><small class="text-danger">** <?= esc($item['options']) ?></small>
                        <?php endif; ?>
                    </div>
                </li>
                <?php endforeach; ?>
                <?php if(!empty($order['notes'])): ?>
                    <li class="bg-warning bg-opacity-25 text-dark fst-italic">
                        <small>Nota: <?= esc($order['notes']) ?></small>
                    </li>
                <?php endif; ?>
            </ul>

            <div class="card-footer bg-white border-top-0 p-2">
                <?php if($order['status'] == 'pending'): ?>
                    <button class="btn btn-primary w-100 py-2 fw-bold" onclick="updateOrder(<?= $order['id'] ?>, 'cooking')">
                        <i class="fa-solid fa-fire me-2"></i> Cocinar
                    </button>
                <?php else: ?>
                    <button class="btn btn-success w-100 py-2 fw-bold" onclick="updateOrder(<?= $order['id'] ?>, 'ready')">
                        <i class="fa-solid fa-bell-concierge me-2"></i> ¡Listo!
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<script>
    // Reloj simple
    setInterval(() => {
        document.getElementById('clock').innerText = new Date().toLocaleTimeString();
    }, 1000);

    // Función AJAX para actualizar estado
    async function updateOrder(id, newStatus) {
        if(!confirm('¿Cambiar estado de la orden?')) return;

        // Obtener Tokens CSRF
        const csrfName = document.querySelector('.csrf-token').getAttribute('name');
        const csrfHash = document.querySelector('.csrf-token').getAttribute('content');

        try {
            const response = await fetch(`<?= site_url('kitchen/order') ?>/${id}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfHash
                },
                body: JSON.stringify({ status: newStatus, [csrfName]: csrfHash })
            });

            const result = await response.json();

            if(result.status === 'success') {
                // Recargar página para actualizar la lista (simple y efectivo)
                window.location.reload();
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