<?php
session_start();
require_once 'includes/conexion.php';
require_once 'includes/funcion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// Obtener el carrito del usuario desde la base de datos
$usuario_id = $_SESSION['usuario_id'];
$query = "SELECT c.*, p.nombre, p.precio, p.imagen, p.stock 
          FROM compras c 
          JOIN productos p ON c.producto_id = p.id 
          WHERE c.usuario_id = $usuario_id AND c.fecha_compra IS NULL
          ORDER BY c.id DESC";
$result_carrito = mysqli_query($conexion, $query);

// Calcular total y guardar items en array
$total = 0;
$items_carrito = [];
while($item = mysqli_fetch_assoc($result_carrito)) {
    $items_carrito[] = $item;
    $total += $item['precio'] * $item['cantidad'];
}

// Procesar compra si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['finalizar_compra'])) {
    // Verificar stock antes de procesar
    $stock_suficiente = true;
    $items_sin_stock = [];
    
    foreach ($items_carrito as $item) {
        if ($item['cantidad'] > $item['stock']) {
            $stock_suficiente = false;
            $items_sin_stock[] = $item['nombre'];
        }
    }
    
    if ($stock_suficiente) {
        // Procesar cada item del carrito
        foreach ($items_carrito as $item) {
            realizarCompra($conexion, $usuario_id, $item['producto_id'], $item['cantidad']);
        }
        
        // Redirigir a página de éxito
        header('Location: compra_exitosa.php');
        exit();
    } else {
        $error = "No hay suficiente stock para: " . implode(", ", $items_sin_stock);
    }
}

// Procesar eliminación de item
if (isset($_GET['eliminar'])) {
    $item_id = intval($_GET['eliminar']);
    $query = "DELETE FROM compras WHERE id = $item_id AND usuario_id = $usuario_id";
    mysqli_query($conexion, $query);
    header('Location: carrito.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito - Mi Tienda Online</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        .carrito-vacio {
            text-align: center;
            padding: 40px 0;
        }
        
        .carrito-item {
            display: flex;
            border-bottom: 1px solid #ddd;
            padding: 20px 0;
            align-items: center;
        }
        
        .carrito-item-imagen {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 20px;
        }
        
        .carrito-item-info {
            flex: 1;
        }
        
        .carrito-item-precio {
            font-weight: bold;
            color: #4a6de5;
            margin: 5px 0;
        }
        
        .carrito-item-cantidad {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
        }
        
        .carrito-item-subtotal {
            font-weight: bold;
            margin-left: 20px;
        }
        
        .carrito-total {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            text-align: right;
            font-size: 1.2rem;
            font-weight: bold;
        }
        
        .eliminar-item {
            color: #dc3545;
            background: none;
            border: none;
            cursor: pointer;
            margin-left: 20px;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
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
        <h1>Tu Carrito de Compras</h1>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (count($items_carrito) == 0): ?>
            <div class="carrito-vacio">
                <p>Tu carrito está vacío</p>
                <a href="producto.php" class="btn btn-primary">Seguir Comprando</a>
            </div>
        <?php else: ?>
            <?php foreach ($items_carrito as $item): ?>
                <div class="carrito-item">
                    <img src="<?php echo $item['imagen'] ?: 'https://via.placeholder.com/100x100?text=Producto'; ?>" 
                         alt="<?php echo $item['nombre']; ?>" class="carrito-item-imagen">
                    <div class="carrito-item-info">
                        <h3><?php echo $item['nombre']; ?></h3>
                        <p class="carrito-item-precio">$<?php echo number_format($item['precio'], 2); ?> c/u</p>
                        <div class="carrito-item-cantidad">
                            <span>Cantidad: <?php echo $item['cantidad']; ?></span>
                            <span class="carrito-item-subtotal">
                                Subtotal: $<?php echo number_format($item['precio'] * $item['cantidad'], 2); ?>
                            </span>
                        </div>
                    </div>
                    <a href="carrito.php?eliminar=<?php echo $item['id']; ?>" class="eliminar-item">Eliminar</a>
                </div>
            <?php endforeach; ?>
            
            <div class="carrito-total">
                Total: $<?php echo number_format($total, 2); ?>
            </div>
            
            <form method="POST" action="carrito.php">
                <button type="submit" name="finalizar_compra" class="btn btn-primary" style="margin-top: 20px; width: 100%;">
                    Finalizar Compra
                </button>
            </form>
        <?php endif; ?>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2023 Mi Tienda Online. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>