<?php
session_start();
ob_start();
include('cone.php');



if (isset($_POST['sent'])) {
    $correo = trim($_POST['email']);
    $contraseña = trim($_POST['password']);

    // Validar el correo y la contraseña
    $query = "SELECT * FROM datos WHERE correo = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($contraseña, $user['contraseña'])) {
        // Iniciar sesión
        session_regenerate_id(true);
        $_SESSION['rol'] = $user['id_rol'];
        $_SESSION['username'] = $user['usuario']; // Asegúrate de usar el campo correcto para el nombre
        $_SESSION['id'] = $user['id']; // Aquí guardamos el ID del usuario
    
        // Redirigir según el rol
        switch ($_SESSION['rol']) {
            case 1: // Admin
                header("Location: admin.php");
                break;
            case 2: // Cliente
                header("Location: usuario.php");
                break;
            default:
                header("Location: login.php");
                break;
        }
        exit;
    } else {
        echo "Correo o contraseña incorrectos";
    }
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Login</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-image: url('bg.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: rgba(255, 255, 255, 0.85); /* Fondo blanco semi-transparente */
            border: 1px solid #e0e0e0;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease, opacity 1s ease; /* Transición en opacidad */
            opacity: 0; /* Inicialmente invisible */
            max-width: 400px;
            width: 100%;
        }

        .form-container.show {
            opacity: 1; /* Cambia a visible cuando se agrega la clase show */
        }

        h2 {
            color: #C58E00; /* Color dorado similar a los ingredientes */
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
            font-size: 24px;
        }

        input[type="text"], input[type="email"], input[type="password"], select {
            width: 94%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #bbb;
            border-radius: 5px;
            background-color: #fafafa;
            color: #333;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus, select:focus {
            outline: none;
            border-color: #C58E00; /* Color dorado */
        }

        select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-color: #fafafa;
            color: #333;
            font-size: 16px;
            background-image: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 5"><path fill="%23333" d="M2 0L0 2h4zm0 5L0 3h4z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 12px 12px;
        }

        select:hover {
            border-color: #C58E00;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #C58E00; /* Dorado */
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #A67500; /* Dorado oscuro para hover */
        }

        .form-group {
            margin-bottom: 20px;
        }

        .forgot-password {
            text-align: center;
            margin-top: 10px;
        }

        .forgot-password a {
            text-decoration: none;
            color: #C58E00; /* Dorado */
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: #A67500; /* Dorado oscuro */
        }

    </style>
</head>
<body>
    <div class="form-container" id="login-form">
        <h2>Iniciar Sesión</h2>
        <form action="login.php" method="POST">
            <div class="form-group">
                <input type="email" name="email" placeholder="Correo electrónico" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Contraseña" required>
            </div>
            <div class="form-group">
                <input type="hidden" name="sent" value="1">
                <input type="submit" value="Ingresar">
            </div>
        </form>
        <div class="forgot-password">
            <p>¿No tienes cuenta? <a href="reg1.php">Regístrate</a></p>
        </div>
    </div>

    <script>
        // Espera a que la página cargue completamente
        window.addEventListener('load', function() {
            // Selecciona el contenedor del formulario
            const loginForm = document.getElementById('login-form');
            
            // Añade la clase 'show' para activar la transición
            setTimeout(() => {
                loginForm.classList.add('show');
            }, 300); // Retraso opcional de 300ms antes de mostrar el formulario
        });
    </script>
</body>
</html>