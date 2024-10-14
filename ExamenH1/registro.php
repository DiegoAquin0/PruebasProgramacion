<?php
include("cone.php");

// Verificar si la conexión a la base de datos fue exitosa
if (!$conn) {
    die("Error en la conexión a la base de datos: " . mysqli_connect_error());
}

// Validar los datos enviados por el formulario
if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['role'])) {
    $username = trim($_POST['username']);
    $correo = trim($_POST['email']);
    $contraseña = trim($_POST['password']);
    $role = intval($_POST['role']);  // Convertir el valor de rol a entero

    // Verificar si las contraseñas coinciden
    if ($_POST['password'] !== $_POST['confirm_password']) {
        echo '<script>alert("Las contraseñas no coinciden");</script>';
        exit();
    }

    // Generar el hash de la contraseña
    $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);

    // Verificar los valores que se están obteniendo (puedes remover esto cuando ya esté funcionando)
    echo "Usuario: $username, Correo: $correo, Contraseña Hash: $contraseña_hash, Role ID: $role";

    // Preparar la consulta para insertar el usuario
    $consulta = mysqli_prepare($conn, "INSERT INTO datos (usuario, correo, contraseña, id_rol) VALUES (?, ?, ?, ?)");

    if ($consulta === false) {
        die('Error al preparar la consulta: ' . mysqli_error($conn));
    }

    // Asignar parámetros a la consulta
    mysqli_stmt_bind_param($consulta, "sssi", $username, $correo, $contraseña_hash, $role);

    // Ejecutar la consulta
    $resultado = mysqli_stmt_execute($consulta);

    // Verificar si la consulta fue exitosa
    if ($resultado) {
        echo '<script>alert("Registro exitoso. ¡Bienvenido!"); window.location.href = "login.php";</script>';
    } else {
        echo '<script>alert("Error en el registro: ' . mysqli_error($conn) . '");</script>';
    }

    mysqli_stmt_close($consulta);
} else {
    echo '<script>alert("Por favor, complete todos los campos");</script>';
}
?>