<?= $this->extend('App\Views\Admin\layout') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800"><?= $title ?></h2>
        <a href="<?= site_url('admin/products') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i> Volver
        </a>
    </div>

    <?php if (session('errors')) : ?>
        <div class="alert alert-danger">
            <ul>
            <?php foreach (session('errors') as $error) : ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
            </ul>
        </div>
    <?php endif ?>

    <div class="card shadow mb-4">
        <div class="card-body">
            <?php $actionUrl = ($product) ? site_url('admin/products/'.$product['id']) : site_url('admin/products') ?>
            
            <form action="<?= $actionUrl ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <?php if($product): ?>
                    <input type="hidden" name="_method" value="PUT">
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Nombre del Platillo/Producto</label>
                            <input type="text" class="form-control" name="name" value="<?= old('name', $product['name'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" name="description" rows="3"><?= old('description', $product['description'] ?? '') ?></textarea>
                            <div class="form-text">Se mostrará en el menú digital y la app de delivery.</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Precio Venta ($)</label>
                                <input type="number" step="0.01" class="form-control" name="price" value="<?= old('price', $product['price'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Costo Insumos ($)</label>
                                <input type="number" step="0.01" class="form-control" name="cost" value="<?= old('cost', $product['cost'] ?? '') ?>">
                                <div class="form-text">Solo para reportes de ganancia interna.</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Categoría</label>
                            <select class="form-select" name="category_id" required>
                                <option value="">Selecciona...</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($product && $product['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                        <?= esc($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="card-title">Inventario</h6>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="track_stock" id="trackStock" value="1" <?= (!isset($product) || $product['track_stock']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="trackStock">Descontar Stock</label>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label">Cantidad Disponible</label>
                                    <input type="number" class="form-control" name="stock" value="<?= old('stock', $product['stock'] ?? 0) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Imagen</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                            <?php if(isset($product['image']) && $product['image']): ?>
                                <div class="mt-2">
                                    <small>Actual:</small><br>
                                    <img src="<?= base_url('uploads/products/'.$product['image']) ?>" width="100" class="rounded">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_visible" id="isVisible" value="1" <?= (!isset($product) || $product['is_visible']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isVisible">Visible en Tienda Online</label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fa-solid fa-save me-2"></i> Guardar Producto
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>