<?= $this->extend('App\Views\Admin\layout') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800">Menú y Productos</h2>
        <a href="<?= site_url('admin/products/new') ?>" class="btn btn-primary">
            <i class="fa-solid fa-plus me-2"></i> Nuevo Producto
        </a>
    </div>

    <?php if (session('message')) : ?>
        <div class="alert alert-success"><?= session('message') ?></div>
    <?php endif ?>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">Img</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Estado</th>
                            <th style="width: 150px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $prod): ?>
                            <tr>
                                <td>
                                    <?php if($prod['image']): ?>
                                        <img src="<?= base_url('uploads/products/'.$prod['image']) ?>" class="img-thumbnail" style="height: 50px;">
                                    <?php else: ?>
                                        <span class="text-muted"><i class="fa-solid fa-image"></i></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= esc($prod['name']) ?></strong><br>
                                    <small class="text-muted"><?= esc(substr($prod['description'], 0, 50)) ?>...</small>
                                </td>
                                <td><span class="badge bg-secondary"><?= esc($prod['category_name']) ?></span></td>
                                <td>$<?= number_format($prod['price'], 2) ?></td>
                                <td>
                                    <?php if(!$prod['track_stock']): ?>
                                        <span class="text-muted">∞ Serv</span>
                                    <?php elseif($prod['stock'] < 5): ?>
                                        <span class="text-danger fw-bold"><?= $prod['stock'] ?> (Bajo)</span>
                                    <?php else: ?>
                                        <span class="text-success"><?= $prod['stock'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $prod['is_visible'] ? '<span class="text-success"><i class="fa-solid fa-eye"></i></span>' : '<span class="text-muted"><i class="fa-solid fa-eye-slash"></i></span>' ?>
                                </td>
                                <td>
                                    <a href="<?= site_url('admin/products/'.$prod['id'].'/edit') ?>" class="btn btn-sm btn-warning">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <a href="<?= site_url('admin/products/'.$prod['id'].'/delete') ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este producto?');">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>