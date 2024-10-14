<?php
// Configuración de la conexión
$servername = "localhost"; // Servidor de MySQL (generalmente es "localhost")
$username = "root"; // Nombre de usuario de MySQL
$password = ""; // Contraseña de MySQL
$dbname = "restaurante"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar si la conexión tiene errores
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
// Verificar la conexión
if (!$conn) {
    die("Error en la conexión: " . mysqli_connect_error());
}

?>