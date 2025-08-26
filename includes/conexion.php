<?php
$servidor = "localhost";
$usuario = "root"; // Cambia según tu configuración
$password = ""; // Cambia según tu configuración
$basedatos = "tienda";

$conexion = mysqli_connect($servidor, $usuario, $password, $basedatos);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>