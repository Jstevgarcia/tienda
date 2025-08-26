<?php
function obtenerProductos($conexion) {
    $query = "SELECT * FROM productos WHERE stock > 0";
    return mysqli_query($conexion, $query);
}

function realizarCompra($conexion, $usuario_id, $producto_id, $cantidad) {
    // Verificar stock
    $query = "SELECT stock FROM productos WHERE id = $producto_id";
    $result = mysqli_query($conexion, $query);
    $producto = mysqli_fetch_assoc($result);
    
    if ($producto['stock'] >= $cantidad) {
        // Reducir stock
        $nuevo_stock = $producto['stock'] - $cantidad;
        $query = "UPDATE productos SET stock = $nuevo_stock WHERE id = $producto_id";
        mysqli_query($conexion, $query);
        
        // Registrar compra con fecha
        $query = "UPDATE compras SET fecha_compra = NOW() 
                  WHERE usuario_id = $usuario_id AND producto_id = $producto_id AND fecha_compra IS NULL";
        return mysqli_query($conexion, $query);
    }
    
    return false;
}


?>