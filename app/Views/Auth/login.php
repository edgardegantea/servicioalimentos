<?= $this->extend('App\Views\Auth\layout') ?>

<?= $this->section('main') ?>

<div class="container-fluid auth-container">
    <div class="row h-100">
        
        <div class="col-md-7 col-lg-8 bg-image d-none d-md-block">
            <div class="d-flex h-100 align-items-end p-5">
                <div class="text-white" style="background: rgba(0,0,0,0.5); padding: 20px; border-radius: 10px;">
                    <h2>Gestión Restaurantera</h2>
                    <p class="mb-0">Control total desde la comanda hasta el inventario.</p>
                </div>
            </div>
        </div>

        <div class="col-md-5 col-lg-4 login-section shadow-lg">
            <div class="w-75">
                <div class="text-center mb-4">
                    <i class="fa-solid fa-utensils fa-3x mb-3" style="color: #e67e22;"></i>
                    <h3 class="fw-bold">Iniciar Sesión</h3>
                    <p class="text-muted">Ingresa tus credenciales para acceder</p>
                </div>

                <?php if (session('error') !== null) : ?>
                    <div class="alert alert-danger" role="alert"><?= session('error') ?></div>
                <?php elseif (session('errors') !== null) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?php if (is_array(session('errors'))) : ?>
                            <?php foreach (session('errors') as $error) : ?>
                                <?= $error ?>
                                <br>
                            <?php endforeach ?>
                        <?php else : ?>
                            <?= session('errors') ?>
                        <?php endif ?>
                    </div>
                <?php endif ?>

                <?php if (session('message') !== null) : ?>
                    <div class="alert alert-success" role="alert"><?= session('message') ?></div>
                <?php endif ?>

                <form action="<?= url_to('login') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="floatingInput" name="email" placeholder="name@example.com" value="<?= old('email') ?>" required>
                        <label for="floatingInput"><i class="fa-regular fa-envelope me-2"></i>Correo Electrónico</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password" required>
                        <label for="floatingPassword"><i class="fa-solid fa-lock me-2"></i>Contraseña</label>
                    </div>

                    <?php if (setting('Auth.sessionConfig')['allowRemembering']): ?>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="remember" id="flexCheckDefault" <?php if (old('remember')): ?> checked <?php endif ?>>
                        <label class="form-check-label text-muted" for="flexCheckDefault">
                            Mantener sesión iniciada
                        </label>
                    </div>
                    <?php endif; ?>

                    <div class="d-grid gap-2 mb-4">
                        <button class="btn btn-primary btn-lg" type="submit">Entrar al Sistema</button>
                    </div>
                    
                    <div class="text-center">
                        <small class="text-muted">¿Olvidaste tu contraseña? Contacta al Gerente.</small>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>