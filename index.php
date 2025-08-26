<?php
session_start();
require_once 'includes/conexion.php';
require_once 'includes/funcion.php';

$productos = obtenerProductos($conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Tienda Online</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1 class="logo">Mi Tienda Online</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="producto.php">Productos</a></li>
                    <?php if(isset($_SESSION['usuario_id'])): ?>
                        <li><a href="carrito.php">Carrito</a></li>
                        <li><a href="cerrar_sesion.php">Cerrar Sesión</a></li>
                        <li>Bienvenido, <?php echo $_SESSION['usuario_nombre']; ?></li>
                    <?php else: ?>
                        <li><a href="login.php">Iniciar Sesión</a></li>
                        <li><a href="registro.php">Registrarse</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section class="hero">
            <h2>Bienvenido a nuestra tienda</h2>
            <p>Encuentra los mejores productos a precios increíbles</p>
        </section>

        <section class="productos-destacados">
            <h2>Productos Destacados</h2>
            <div class="grid-productos">
                <?php while($producto = mysqli_fetch_assoc($productos)): ?>
                    <div class="producto">
                        <img src="<?php echo $producto['imagen']; ?>" alt="<?php echo $producto['nombre']; ?>">
                        <h3><?php echo $producto['nombre']; ?></h3>
                        <p class="precio">$<?php echo $producto['precio']; ?></p>
                        <p class="stock">Disponibles: <?php echo $producto['stock']; ?></p>
                        <?php if(isset($_SESSION['usuario_id'])): ?>
                            <form action="procesar_compra.php" method="POST">
                                <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                <input type="number" name="cantidad" value="1" min="1" max="<?php echo $producto['stock']; ?>">
                                <button type="submit">Comprar</button>
                            </form>
                        <?php else: ?>
                            <p>Inicia sesión para comprar</p>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2023 Mi Tienda Online. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>