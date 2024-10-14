
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-image: url('bg.png'); /* Aquí puedes agregar tu imagen de fondo */
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
            font-weight: normal;
            font-size: 24px;
        }

        input[type="text"], input[type="email"], input[type="password"], select {
            width: 100%;
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
    <div class="form-container" id="form">
        <h2>Registro</h2>
        <form action="registro.php" method="POST"> <!-- Apunta a tu script de registro -->
            <div class="form-group">
                <input type="text" name="username" placeholder="Nombre de usuario" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Correo electrónico" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Contraseña" required>
            </div>
            <div class="form-group">
                <input type="password" name="confirm_password" placeholder="Confirmar contraseña" required>
            </div>
            <!-- Campo oculto para role con valor 2 -->
            <input type="hidden" name="role" value="2">
            <div class="form-group">
                <input type="submit" name="sent" value="Registrarse">
            </div>
        </form>
        <div class="forgot-password">
            <a href="login.php">¿Ya tienes una cuenta?</a>
        </div>
    </div>

    <script>
        // Espera a que la página cargue completamente
        window.addEventListener('load', function() {
            // Selecciona el contenedor del formulario
            const formContainer = document.getElementById('form');
            
            // Añade la clase 'show' para activar la transición
            setTimeout(() => {
                formContainer.classList.add('show');
            }, 300); // Retraso opcional de 300ms antes de mostrar el formulario
        });
    </script>
</body>
</html>