<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menú Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero { background: url('https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80') center/cover; height: 300px; display: flex; align-items: center; justify-content: center; }
        .hero-title { background: rgba(0,0,0,0.6); color: white; padding: 20px; border-radius: 10px; }
        .price-tag { color: #e67e22; font-weight: bold; font-size: 1.2rem; }
    </style>
</head>
<body>

    <header class="hero mb-5">
        <div class="text-center hero-title">
            <h1>Bienvenidos</h1>
            <p class="lead">Disfruta de nuestra selección gastronómica</p>
            <?php if(auth()->loggedIn()): ?>
                <a href="<?= site_url('logout') ?>" class="btn btn-sm btn-outline-light">Cerrar Sesión</a>
            <?php else: ?>
                <a href="<?= site_url('login') ?>" class="btn btn-sm btn-outline-light">Acceso Staff</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="container mb-5">
        <?php foreach($menu as $category => $items): ?>
            <h3 class="border-bottom pb-2 mb-4 text-uppercase text-primary"><?= esc($category) ?></h3>
            
            <div class="row g-4 mb-5">
                <?php foreach($items as $item): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="row g-0 h-100">
                            <div class="col-4">
                                <?php if($item['image']): ?>
                                    <img src="<?= base_url('uploads/products/'.$item['image']) ?>" class="img-fluid rounded-start h-100 object-fit-cover" style="min-height: 120px;">
                                <?php else: ?>
                                    <div class="bg-light h-100 d-flex align-items-center justify-content-center text-muted">Sin Foto</div>
                                <?php endif; ?>
                            </div>
                            <div class="col-8">
                                <div class="card-body py-2">
                                    <h5 class="card-title"><?= esc($item['name']) ?></h5>
                                    <p class="card-text small text-muted"><?= esc($item['description']) ?></p>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span class="price-tag">$<?= number_format($item['price'], 2) ?></span>
                                        <button class="btn btn-sm btn-outline-dark rounded-pill">Agregar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <footer class="bg-dark text-white text-center py-3">
        <p class="mb-0">© <?= date('Y') ?> Sistema Restaurantero</p>
    </footer>

</body>
</html>