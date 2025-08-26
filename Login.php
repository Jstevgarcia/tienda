<?php
session_start();
require_once 'includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $query = "SELECT * FROM usuarios WHERE email = '$email'";
    $result = mysqli_query($conexion, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $usuario = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $usuario['password'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            header('Location: index.php');
            exit();
        } else {
            $error = "Contraseña incorrecta";
        }
    } else {
        $error = "Usuario no encontrado";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Mi Tienda Online</title>
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
                    <li><a href="registro.php">Registrarse</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section class="formulario">
            <h2>Iniciar Sesión</h2>
            <?php if(isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="campo">
                    <label for="email">Correo Electrónico:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="campo">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Iniciar Sesión</button>
            </form>
            <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
        </section>
    </main>
</body>
</html>