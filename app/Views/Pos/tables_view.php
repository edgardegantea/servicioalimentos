<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mapa de Mesas | Restaurante</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <meta http-equiv="refresh" content="15">

    <style>
        body { background-color: #f4f6f9; padding: 20px; padding-bottom: 80px; }
        
        .table-card { 
            height: 220px; 
            border-radius: 15px; 
            transition: all 0.2s ease-in-out; 
            cursor: pointer;
            position: relative;
            border: none;
            overflow: hidden;
        }
        .table-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important; }
        
        
        .status-free { 
            background-color: #d1e7dd; 
            color: #0f5132; 
            border: 2px dashed #a3cfbb; 
        }
        .status-free .icon-bg { color: #198754; opacity: 0.1; }

        .status-occupied { 
            background-color: #fff; 
            border: 2px solid #0d6efd; 
            border-top: 10px solid #0d6efd;
        }
        
        .icon-bg {
            position: absolute;
            bottom: -20px;
            right: -20px;
            font-size: 8rem;
            z-index: 0;
            pointer-events: none;
        }
        
        .card-content { z-index: 1; position: relative; width: 100%; }

        /* Información de la Orden */
        .order-badge {
            background-color: #e9ecef;
            border-radius: 8px;
            padding: 8px;
            margin-top: 10px;
        }

        /* Botón Flotante (FAB) */
        .fab { 
            position: fixed; 
            bottom: 30px; 
            right: 30px; 
            width: 70px; 
            height: 70px; 
            border-radius: 50%; 
            font-size: 28px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.3); 
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>

    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold mb-0"><i class="fa-solid fa-map-location-dot me-2"></i>Sala Principal</h2>
            <small class="text-muted">Selecciona una mesa para comenzar</small>
        </div>
        
        <div class="d-none d-md-block">
            <span class="badge bg-success bg-opacity-25 text-success border border-success me-2 px-3 py-2">
                <i class="fa-solid fa-check me-1"></i> Libre
            </span>
            <span class="badge bg-primary text-white border border-primary px-3 py-2">
                <i class="fa-solid fa-utensils me-1"></i> Ocupada
            </span>
        </div>
        
        <a href="<?= site_url('logout') ?>" class="btn btn-outline-danger btn-sm">
            <i class="fa-solid fa-power-off"></i>
        </a>
    </div>

    <div class="row g-4">
        <?php foreach($tables as $table): ?>
            <?php 
                // Lógica de Estado
                $isOccupied = !empty($table['active_order']);
                $cardClass  = $isOccupied ? 'status-occupied shadow-sm' : 'status-free';
                
                // Lógica de Enlaces (La clave de tu solicitud)
                if ($isOccupied) {
                    // Si está ocupada -> Ir a Pagar / Ver Cuenta
                    $link = site_url('pos/payment/' . $table['active_order']['id']);
                    $title = "Ver Cuenta / Cobrar";
                } else {
                    // Si está libre -> Ir a Ocupar (Crear Orden)
                    $link = site_url('pos/tables/occupy/' . $table['id']);
                    $title = "Abrir Mesa";
                }
            ?>
            
            <div class="col-6 col-md-4 col-lg-3">
                <a href="<?= $link ?>" class="text-decoration-none text-dark" title="<?= $title ?>">
                    <div class="card table-card <?= $cardClass ?> d-flex align-items-center justify-content-center text-center p-3">
                        
                        <i class="fa-solid fa-chair icon-bg"></i>

                        <div class="card-content">
                            <h3 class="fw-bold mb-0"><?= esc($table['name']) ?></h3>
                            <div class="small text-muted mb-2">
                                Capacidad: <?= $table['capacity'] ?> <i class="fa-solid fa-user ms-1"></i>
                            </div>

                            <?php if($isOccupied): ?>
                                <div class="order-badge border border-primary text-primary bg-white">
                                    <div class="small fw-bold text-uppercase mb-1">
                                        <i class="fa-solid fa-receipt me-1"></i>
                                        #<?= esc($table['active_order']['order_number']) ?>
                                    </div>
                                    
                                    <div class="fs-4 fw-bold text-danger">
                                        $<?= number_format($table['active_order']['total'], 2) ?>
                                    </div>
                                    
                                    <?php 
                                        $statusLabels = [
                                            'pending' => 'En espera',
                                            'cooking' => 'Cocinando',
                                            'ready'   => 'Listo',
                                            'delivered' => 'Comiendo'
                                        ];
                                        $status = $table['active_order']['status'];
                                    ?>
                                    <span class="badge bg-dark mt-1"><?= $statusLabels[$status] ?? $status ?></span>
                                </div>
                            <?php else: ?>
                                <div class="mt-4 text-success fw-bold opacity-75">
                                    <i class="fa-solid fa-hand-pointer fa-2x mb-2 d-block"></i>
                                    DISPONIBLE
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <a href="<?= site_url('pos') ?>" class="btn btn-warning fab fw-bold text-dark" title="Venta Rápida / Para Llevar">
        <i class="fa-solid fa-person-running"></i>
    </a>

    <?php if(session('error')): ?>
        <div class="position-fixed bottom-0 start-50 translate-middle-x mb-4 z-index-100">
            <div class="alert alert-danger shadow-lg rounded-pill px-4">
                <i class="fa-solid fa-circle-exclamation me-2"></i> <?= session('error') ?>
            </div>
        </div>
    <?php endif; ?>

</body>
</html>