<?php
session_start();
require_once 'includes/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// Obtener productos de la base de datos
$query = "SELECT * FROM productos WHERE stock > 0 ORDER BY nombre";
$result = mysqli_query($conexion, $query);

// Procesar búsqueda si se envió el formulario
$busqueda = "";
if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
    $busqueda = mysqli_real_escape_string($conexion, $_GET['busqueda']);
    $query = "SELECT * FROM productos WHERE stock > 0 AND nombre LIKE '%$busqueda%' ORDER BY nombre";
    $result = mysqli_query($conexion, $query);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Mi Tienda Online</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .busqueda {
            margin: 20px 0;
            display: flex;
            gap: 10px;
        }
        
        .busqueda input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .busqueda button {
            padding: 10px 20px;
            background-color: #4a6de5;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .categorias {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 20px 0;
        }
        
        .categoria {
            padding: 8px 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 20px;
            cursor: pointer;
        }
        
        .categoria:hover, .categoria.active {
            background-color: #4a6de5;
            color: white;
        }
        
        .producto {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            background: white;
        }
        
        .producto-imagen {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .producto-precio {
            font-size: 1.2rem;
            color: #4a6de5;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .producto-stock {
            color: #28a745;
            margin-bottom: 15px;
        }
        
        .agotado {
            color: #dc3545;
        }
        
        .comprar-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .comprar-form input {
            width: 60px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
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
        <h1>Nuestros Productos</h1>
        
        <form method="GET" action="producto.php" class="busqueda">
            <input type="text" name="busqueda" placeholder="Buscar productos..." value="<?php echo htmlspecialchars($busqueda); ?>">
            <button type="submit">Buscar</button>
            <?php if (!empty($busqueda)): ?>
                <a href="producto.php" class="btn">Limpiar búsqueda</a>
            <?php endif; ?>
        </form>
        
        <div class="categorias">
            <div class="categoria active">Todos</div>
            <div class="categoria">Electrónica</div>
            <div class="categoria">Ropa</div>
            <div class="categoria">Hogar</div>
            <div class="categoria">Deportes</div>
        </div>
        
        <div class="grid-producto">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while($producto = mysqli_fetch_assoc($result)): ?>
                    <div class="producto">
                        <img src="<?php echo $producto['imagen'] ?: 'https://via.placeholder.com/300x200?text=Producto'; ?>" 
                             alt="<?php echo $producto['nombre']; ?>" class="producto-imagen">
                        <h3><?php echo $producto['nombre']; ?></h3>
                        <p><?php echo $producto['descripcion']; ?></p>
                        <p class="producto-precio">$<?php echo number_format($producto['precio'], 2); ?></p>
                        <p class="producto-stock <?php echo $producto['stock'] == 0 ? 'agotado' : ''; ?>">
                            <?php echo $producto['stock'] == 0 ? 'Agotado' : 'Disponibles: ' . $producto['stock']; ?>
                        </p>
                        
                        <?php if ($producto['stock'] > 0): ?>
                            <form method="POST" action="procesar_compra.php" class="comprar-form">
                                <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                <input type="number" name="cantidad" value="1" min="1" max="<?php echo $producto['stock']; ?>">
                                <button type="submit" class="btn btn-primary">Comprar</button>
                            </form>
                        <?php else: ?>
                            <button class="btn" disabled>Agotado</button>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No se encontraron productos<?php echo !empty($busqueda) ? ' para "' . htmlspecialchars($busqueda) . '"' : ''; ?>.</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2023 Mi Tienda Online. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>