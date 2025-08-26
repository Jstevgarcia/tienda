<?php
session_start();
require_once 'includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Verificar si el email ya existe
    $query = "SELECT id FROM usuarios WHERE email = '$email'";
    $result = mysqli_query($conexion, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $error = "El correo electrónico ya está registrado";
    } else {
        $query = "INSERT INTO usuarios (nombre, email, password) VALUES ('$nombre', '$email', '$password')";
        
        if (mysqli_query($conexion, $query)) {
            $_SESSION['usuario_id'] = mysqli_insert_id($conexion);
            $_SESSION['usuario_nombre'] = $nombre;
            header('Location: index.php');
            exit();
        } else {
            $error = "Error al crear la cuenta: " . mysqli_error($conexion);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Mi Tienda Online</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1 class="logo">Mi Tienda Online</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="productos.php">Productos</a></li>
                    <li><a href="login.php">Iniciar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section class="formulario">
            <h2>Crear Cuenta</h2>
            <?php if(isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="campo">
                    <label for="nombre">Nombre Completo:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="campo">
                    <label for="email">Correo Electrónico:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="campo">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Registrarse</button>
            </form>
            <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
        </section>
    </main>
</body>
</html>