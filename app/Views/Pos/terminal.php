<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <meta name="<?= csrf_token() ?>" content="<?= csrf_hash() ?>" class="csrf-token">
    
    <title>POS | Terminal de Venta</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background-color: #f0f2f5; height: 100vh; overflow: hidden; }
        
        /* Estilos de Tarjeta de Producto */
        .product-card { 
            cursor: pointer; 
            transition: all 0.2s; 
            height: 100%; 
            border: none; 
            overflow: hidden;
        }
        .product-card:active { transform: scale(0.95); }
        .product-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important; }
        .product-img { height: 110px; object-fit: cover; width: 100%; }
        
        /* Panel del Ticket (Derecha) */
        .ticket-panel { 
            background: white; 
            height: 100vh; 
            border-left: 1px solid #ddd; 
            display: flex; 
            flex-direction: column; 
        }
        .ticket-items { flex-grow: 1; overflow-y: auto; padding: 15px; background-color: #fff; }
        
        /* Scroll de Categorías */
        .category-scroll { 
            overflow-x: auto; 
            white-space: nowrap; 
            padding-bottom: 5px; 
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
        .category-scroll::-webkit-scrollbar { display: none; } /* Chrome */
        
        .cat-btn { border-radius: 20px; padding: 8px 20px; margin-right: 8px; font-weight: 500; }
        .cat-btn.active { background-color: #212529; color: white; border-color: #212529; }
        
        /* Overlay de Agotado */
        .sold-out-overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex; align-items: center; justify-content: center;
            color: #dc3545; font-weight: bold; font-size: 1.2rem;
            transform: rotate(-15deg);
            z-index: 10;
        }
    </style>
</head>
<body>

<div class="container-fluid h-100 p-0">
    <div class="row h-100 g-0">
        
        <div class="col-md-7 col-lg-8 p-3 d-flex flex-column bg-light">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                 <div class="category-scroll d-flex align-items-center mask-image" style="max-width: 80%;">
                    <button class="btn btn-outline-dark cat-btn active" onclick="filterCategory('all', this)">Todo</button>
                    <?php foreach($categories as $cat): ?>
                        <button class="btn btn-outline-secondary cat-btn" onclick="filterCategory(<?= $cat['id'] ?>, this)">
                            <?= esc($cat['name']) ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div>
                    <a href="<?= site_url('pos/register/close') ?>" class="btn btn-danger btn-sm me-2 shadow-sm" title="Corte de Caja">
                        <i class="fa-solid fa-file-invoice-dollar me-1"></i> Corte
                    </a>
                    
                    <a href="<?= site_url('pos/tables') ?>" class="btn btn-primary btn-sm shadow-sm" title="Ver Mesas">
                        <i class="fa-solid fa-map me-1"></i> Mesas
                    </a>
                </div>
            </div>

            <div class="row g-3 overflow-auto p-1" style="flex-grow: 1;" id="product-grid">
                <?php foreach($products as $prod): ?>
                    <div class="col-6 col-md-4 col-lg-3 product-item" data-category="<?= $prod['category_id'] ?>">
                        
                        <?php 
                            $hasStock = ($prod['track_stock'] == 0 || $prod['stock'] > 0);
                            $onClick  = $hasStock ? "addToCart({$prod['id']}, '".addslashes($prod['name'])."', {$prod['price']})" : "";
                        ?>

                        <div class="card product-card shadow-sm position-relative" onclick="<?= $onClick ?>">
                            
                            <?php if(!$hasStock): ?>
                                <div class="sold-out-overlay border border-danger">AGOTADO</div>
                            <?php endif; ?>

                            <?php if($prod['image']): ?>
                                <img src="<?= base_url('uploads/products/'.$prod['image']) ?>" class="product-img">
                            <?php else: ?>
                                <div class="product-img bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center text-muted">
                                    <i class="fa-solid fa-utensils fa-2x opacity-50"></i>
                                </div>
                            <?php endif; ?>

                            <div class="card-body p-2 text-center d-flex flex-column justify-content-between">
                                <h6 class="card-title mb-1 small text-truncate" title="<?= esc($prod['name']) ?>"><?= esc($prod['name']) ?></h6>
                                <p class="card-text fw-bold text-primary mb-0">$<?= number_format($prod['price'], 2) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="col-md-5 col-lg-4 ticket-panel shadow">
            
            <div class="p-3 bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold"><i class="fa-solid fa-receipt me-2"></i>Ticket Actual</h5>
                        <?php if(isset($activeOrder) && $activeOrder): ?>
                            <small class="opacity-75">Folio: <?= esc($activeOrder['order_number']) ?></small>
                        <?php else: ?>
                            <small class="opacity-75">Nueva Orden</small>
                        <?php endif; ?>
                    </div>
                    
                    <?php if(isset($activeOrder) && $activeOrder): ?>
                        <div class="text-end">
                            <span class="badge bg-white text-primary fs-6 px-3 py-1 shadow-sm mb-1 d-block">
                                <?= esc($activeOrder['table_name']) ?>
                            </span>
                        </div>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark border border-dark shadow-sm">
                            <i class="fa-solid fa-person-running me-1"></i> Rápida / Delivery
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="ticket-items" id="cart-container">
                <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                    <i class="fa-solid fa-basket-shopping fa-3x mb-3 opacity-25"></i>
                    <p>Selecciona productos del menú</p>
                </div>
            </div>

            <div class="p-3 bg-light border-top">
                <div class="d-flex justify-content-between mb-1 text-muted small">
                    <span>Subtotal:</span>
                    <span id="cart-subtotal">$0.00</span>
                </div>
                <div class="d-flex justify-content-between h3 fw-bold mb-3">
                    <span>Total:</span>
                    <span id="cart-total" class="text-primary">$0.00</span>
                </div>
                
                <div class="row g-2">
                    <div class="col-4">
                        <button class="btn btn-outline-danger w-100 py-3" onclick="clearCart()">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                    <div class="col-8">
                        <button id="btn-checkout" class="btn btn-success w-100 py-3 fw-bold shadow-sm" onclick="processSale()">
                            COBRAR <i class="fa-solid fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('js/pos.js') ?>"></script>

<script>
    /**
     * Script rápido para filtrar productos por categoría sin recargar
     */
    function filterCategory(catId, btn) {
        // 1. Actualizar estilo de botones
        document.querySelectorAll('.cat-btn').forEach(b => {
            b.classList.remove('active', 'btn-outline-dark');
            b.classList.add('btn-outline-secondary');
        });
        btn.classList.add('active', 'btn-outline-dark');
        btn.classList.remove('btn-outline-secondary');

        // 2. Filtrar Grid
        const items = document.querySelectorAll('.product-item');
        
        items.forEach(item => {
            if (catId === 'all' || item.dataset.category == catId) {
                item.classList.remove('d-none');
            } else {
                item.classList.add('d-none');
            }
        });
    }

    // Funciones auxiliares para el botón Cancelar
    function clearCart() {
        if(confirm('¿Vaciar carrito?')) {
            cart = [];
            renderCart();
        }
    }
</script>

</body>
</html>