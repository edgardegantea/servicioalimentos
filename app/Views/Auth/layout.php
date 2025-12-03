<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acceso al Sistema | Restaurante</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body, html { height: 100%; }
        .auth-container { height: 100vh; overflow: hidden; }
        .bg-image {
            /* Imagen de fondo izquierda: Un plato de comida elegante o el interior del restaurante */
            background-image: url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=1000&auto=format&fit=crop'); 
            background-size: cover;
            background-position: center;
        }
        .login-section {
            background-color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-primary {
            background-color: #2c3e50; /* Azul oscuro de tu imagen */
            border-color: #2c3e50;
        }
        .btn-primary:hover {
            background-color: #e67e22; /* Naranja de tu imagen al pasar el mouse */
            border-color: #e67e22;
        }
        .form-control:focus {
            border-color: #e67e22;
            box-shadow: 0 0 0 0.25rem rgba(230, 126, 34, 0.25);
        }
    </style>
</head>
<body>

    <?= $this->renderSection('main') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>