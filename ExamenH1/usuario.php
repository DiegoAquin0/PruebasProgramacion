<?php 
session_start();
include('cone.php'); // Incluir la conexión a la base de datos

// Verificar si el usuario tiene el rol adecuado (Cliente)
if ($_SESSION['rol'] != 2) {
    header("Location: login.php");
    exit;
}

// Obtener el menú de la base de datos
$query = "SELECT * FROM menu";
$result = $conn->query($query);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comentario'])) {
    // Verificar si el ID del usuario está en la sesión
    if (!isset($_SESSION['id'])) {
        die("Error: No se ha encontrado el ID del usuario en la sesión.");
    }

    $id_producto = $_POST['id_producto'];
    $comentario = $_POST['comentario'];
    $id_usuario = $_SESSION['id'];

    

    // Verificar si el producto y el usuario existen
    $usuario_query = "SELECT id FROM datos WHERE id = ?";
    $producto_query = "SELECT id_producto FROM menu WHERE id_producto = ?";
    
    $usuario_stmt = $conn->prepare($usuario_query);
    $usuario_stmt->bind_param('i', $id_usuario);
    $usuario_stmt->execute();
    $usuario_result = $usuario_stmt->get_result();

    $producto_stmt = $conn->prepare($producto_query);
    $producto_stmt->bind_param('i', $id_producto);
    $producto_stmt->execute();
    $producto_result = $producto_stmt->get_result();

    if ($usuario_result->num_rows > 0 && $producto_result->num_rows > 0) {
        // Preparar la consulta para insertar el comentario
        $query = "INSERT INTO comentarios (id_usuario, id_producto, comentario) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);

        // Verificar si la consulta se preparó correctamente
        if ($stmt === false) {
            die("Error al preparar la consulta: " . $conn->error);
        }

        // Asignar los parámetros a la consulta
        $stmt->bind_param('iis', $id_usuario, $id_producto, $comentario);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo "";
        } else {
            // Mostrar el error de ejecución
            echo "Error al enviar el comentario: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error: El usuario o el producto no existen en la base de datos.";
    }

    $usuario_stmt->close();
    $producto_stmt->close();
} else {
    echo "";
}

// Obtener los comentarios para mostrar
$comentarios_query = "SELECT c.comentario, c.fecha, d.usuario, m.nombre_producto FROM comentarios c 
                      JOIN datos d ON c.id_usuario = d.id 
                      JOIN menu m ON c.id_producto = m.id_producto 
                      ORDER BY c.fecha DESC";
$comentarios_result = $conn->query($comentarios_query);
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenida Cliente</title>
    <style>
                    body {
                font-family: 'Arial', sans-serif;
                margin: 0;
                padding: 0;
                background: url('BG1.png') repeat-y; /* Imagen de fondo repetida verticalmente */
                background-size: cover; /* Para que la imagen cubra todo el ancho */
            }

            .navbar {
                background-color: #5C4033; /* Marrón oscuro para combinar con el fondo */
                padding: 15px;
                color: white;
                text-align: center;
            }

            .navbar h1 {
                margin: 0;
                font-size: 24px;
            }

            .container {
                max-width: 1200px;
                margin: 40px auto;
                padding: 20px;
                background-color: rgba(255, 255, 255, 0.8); /* Fondo blanco con algo de transparencia */
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
                text-align: center;
            }

            textarea {
                width: 95%;
                padding: 10px;
                border-radius: 5px;
                border: 1px solid #A0522D; /* Marrón oscuro */
                resize: vertical;
                font-family: 'Arial', sans-serif;
                font-size: 14px;
                box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
                transition: border-color 0.3s ease;
            }

            textarea:focus {
                outline: none;
                border-color: #D4A017; /* Dorado */
                box-shadow: 0 0 5px rgba(212, 175, 55, 0.5);
            }

            button.btn {
                width: 100%;
                padding: 10px;
                background-color: #D4A017; /* Dorado */
                color: white;
                border: none;
                border-radius: 5px;
                font-size: 16px;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }

            button.btn:hover {
                background-color: #B8860B; /* Tono dorado más oscuro */
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            table th, table td {
                padding: 12px;
                border: 1px solid #ddd;
                text-align: left;
            }

            table th {
                background-color: #D4A017; /* Dorado */
                color: white;
            }

            table tr:nth-child(even) {
                background-color: #F5F5DC; /* Beige claro para combinar con el tono de madera */
            }

            h2 {
                color: #333;
                text-align: center;
                margin-bottom: 20px;
            }

            .comentarios {
                margin-top: 40px;
                text-align: left;
            }

            .comentario {
                background-color: #F5F5DC; /* Beige claro */
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 5px;
                margin-bottom: 10px;
            }

            .btn-right {
                display: inline-block;
                padding: 10px 20px;
                background-color: #D4A017; /* Dorado */
                color: white;
                text-decoration: none;
                border-radius: 5px;
                transition: background-color 0.3s ease;
                position: absolute;
                top: 20px;
                right: 20px;
            }

            .btn-right:hover {
                background-color: #B8860B; /* Tono dorado más oscuro */
            }

            .container {
                position: relative;
            }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    </div>

    <div class="container">
    <button class="btn-right" onclick="window.location.href='login.php';">Volver al Inicio</button>
        <h2>Menú del Restaurante</h2>
        <table>
    <tr>
        <th>Platillo</th>
        <th>Precio</th>
        <th>Acción</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['nombre_producto']); ?></td>
        <td><?php echo '$' . number_format($row['precio'], 2); ?></td>
        <td>
            <form action="" method="POST">
                <input type="hidden" name="id_producto" value="<?php echo htmlspecialchars($row['id_producto']); ?>">
                <textarea name="comentario" rows="2" required placeholder="Escribe tu comentario..."></textarea>
                <br>
                <button type="submit" class="btn">Enviar Comentario</button>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

        <div class="comentarios">
            <h2>Comentarios Recientes</h2>
            <?php while ($comentario = $comentarios_result->fetch_assoc()): ?>
                <div class="comentario">
                    <strong><?php echo htmlspecialchars($comentario['usuario']); ?> sobre <?php echo htmlspecialchars($comentario['nombre_producto']); ?>:</strong>
                    <p><?php echo htmlspecialchars($comentario['comentario']); ?></p>
                    <small><?php echo htmlspecialchars($comentario['fecha']); ?></small>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>