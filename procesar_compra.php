<?php
session_start();
require_once 'includes/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario_id = $_SESSION['usuario_id'];
    $producto_id = intval($_POST['producto_id']);
    $cantidad = intval($_POST['cantidad']);
    
    // Validar cantidad mínima
    if ($cantidad < 1) {
        $_SESSION['error'] = "La cantidad debe ser al menos 1";
        header('Location: producto.php');
        exit();
    }
    
    // Verificar stock disponible
    $query_stock = "SELECT stock, nombre FROM productos WHERE id = $producto_id";
    $result_stock = mysqli_query($conexion, $query_stock);
    
    if (mysqli_num_rows($result_stock) == 0) {
        $_SESSION['error'] = "Producto no encontrado";
        header('Location: producto.php');
        exit();
    }
    
    $producto = mysqli_fetch_assoc($result_stock);
    
    if ($cantidad > $producto['stock']) {
        $_SESSION['error'] = "No hay suficiente stock disponible para: " . $producto['nombre'] . ". Solo quedan " . $producto['stock'] . " unidades.";
        header('Location: producto.php');
        exit();
    }
    
    // Verificar si el producto ya está en el carrito
    $query = "SELECT * FROM compras 
              WHERE usuario_id = $usuario_id AND producto_id = $producto_id AND fecha_compra IS NULL";
    $result = mysqli_query($conexion, $query);
    
    if (mysqli_num_rows($result) > 0) {
        // Actualizar cantidad si ya existe
        $item = mysqli_fetch_assoc($result);
        $nueva_cantidad = $item['cantidad'] + $cantidad;
        
        // Verificar stock nuevamente con la nueva cantidad
        if ($nueva_cantidad > $producto['stock']) {
            $_SESSION['error'] = "No puedes agregar más unidades de " . $producto['nombre'] . ". Límite: " . $producto['stock'] . " unidades.";
            header('Location: producto.php');
            exit();
        }
        
        $query_update = "UPDATE compras SET cantidad = $nueva_cantidad 
                         WHERE id = {$item['id']}";
        
        if (mysqli_query($conexion, $query_update)) {
            header('Location: carrito.php?agregado=1');
        } else {
            $_SESSION['error'] = "Error al actualizar el carrito";
            header('Location: producto.php');
        }
    } else {
        // Agregar nuevo item al carrito
        $query_insert = "INSERT INTO compras (usuario_id, producto_id, cantidad) 
                         VALUES ($usuario_id, $producto_id, $cantidad)";
        
        if (mysqli_query($conexion, $query_insert)) {
            header('Location: carrito.php?agregado=1');
        } else {
            $_SESSION['error'] = "Error al agregar al carrito";
            header('Location: producto.php');
        }
    }
    exit();
}

header('Location: producto.php');
exit();
?>