<?php
session_start();
require_once 'includes/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compra Exitosa - Mi Tienda Online</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .compra-exitosa {
            text-align: center;
            padding: 40px 0;
        }
        
        .compra-exitosa h1 {
            color: #28a745;
            margin-bottom: 20px;
        }
        
        .compra-exitosa p {
            margin-bottom: 20px;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="index.php" class="logo">MiTienda</a>
                <ul class="nav-links">
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="producto.php">Productos</a></li>
                    <li><a href="carrito.php">Carrito</a></li>
                    <li><a href="cerrar_sesion.php">Cerrar Sesión</a></li>
                    <li>Bienvenido, <?php echo $_SESSION['usuario_nombre']; ?></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="compra-exitosa">
            <h1>¡Compra realizada con éxito!</h1>
            <p>Tu pedido ha sido procesado correctamente.</p>
            <p>Recibirás un correo electrónico con los detalles de tu compra.</p>
            <a href="producto.php" class="btn btn-primary">Seguir Comprando</a>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2023 Mi Tienda Online. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>