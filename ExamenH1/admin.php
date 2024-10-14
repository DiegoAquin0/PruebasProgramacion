<?php
session_start();
include('cone.php'); // Incluir la conexión a la base de datos

// Verificar si el usuario tiene el rol adecuado
if ($_SESSION['rol'] != 1) {
    header("Location: login.php");
    exit;
}

// Añadir producto (si el admin ha enviado el formulario)
if (isset($_POST['add_product'])) {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];

    // Validar que los campos no estén vacíos
    if (!empty($nombre) && !empty($precio)) {
        $query = "INSERT INTO menu (nombre_producto, precio) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("sd", $nombre, $precio);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "Error al preparar la consulta: " . $conn->error;
        }
    }
}

// Verificar si el usuario tiene el rol adecuado
if ($_SESSION['rol'] != 1) {
    header("Location: login.php");
    exit;
}

// Obtener todos los usuarios
$query_usuarios = "SELECT * FROM datos";
$result_usuarios = $conn->query($query_usuarios);

// Verifica si la consulta fue exitosa
if (!$result_usuarios) {
    die("Error en la consulta SQL: " . $conn->error);
}

// Actualizar el rol de un usuario
if (isset($_POST['update_role'])) {
    $id_usuario = $_POST['id_usuario'];
    $nuevo_rol = $_POST['nuevo_rol'];

    if (!empty($id_usuario) && !empty($nuevo_rol)) {
        $query = "UPDATE datos SET id_rol = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("ii", $nuevo_rol, $id_usuario);
            $stmt->execute();
            $stmt->close();
            echo "";
        } else {
            echo "Error al preparar la consulta: " . $conn->error;
        }
    }
}


// Eliminar producto
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Verificar si el ID es válido
    if (is_numeric($id)) {
        $query = "DELETE FROM menu WHERE id_producto = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                echo "Producto eliminado.";
            } else {
                echo "Error al ejecutar la eliminación: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error al preparar la consulta de eliminación: " . $conn->error;
        }
    } else {
        echo "ID inválido.";
    }
}

// Obtener todos los productos del menú
$query = "SELECT * FROM menu";
$result = $conn->query($query);

// Añadir ingrediente (si el admin ha enviado el formulario)
if (isset($_POST['add_ingredient'])) {
    $nombre_ingrediente = $_POST['nombre_ingrediente'];
    $total_almacen = $_POST['total_almacen'];

    if (!empty($nombre_ingrediente) && !empty($total_almacen)) {
        $query = "INSERT INTO inventario (nombre_ingrediente, total_almacen) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("si", $nombre_ingrediente, $total_almacen);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "Error al preparar la consulta: " . $conn->error;
        }
    }
}

// Obtener los comentarios para mostrar
$comentarios_query = "SELECT c.comentario, c.fecha, d.usuario, m.nombre_producto FROM comentarios c 
                      JOIN datos d ON c.id_usuario = d.id 
                      JOIN menu m ON c.id_producto = m.id_producto 
                      ORDER BY c.fecha DESC";
$comentarios_result = $conn->query($comentarios_query);


// Actualizar ingrediente (si el admin ha enviado el formulario)
if (isset($_POST['update_ingredient'])) {
    $id_ingrediente = $_POST['id_ingrediente'];
    $total_almacen = $_POST['total_almacen'];

    if (!empty($id_ingrediente) && !empty($total_almacen)) {
        $query = "UPDATE inventario SET total_almacen = ? WHERE id_ingrediente = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("ii", $total_almacen, $id_ingrediente);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "Error al preparar la consulta: " . $conn->error;
        }
    }
}

// Obtener todos los ingredientes del inventario
$query_inventario = "SELECT * FROM inventario";
$result_inventario = $conn->query($query_inventario);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        .navbar {
            background-color: #333;
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
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h2 {
            color: #333;
            text-align: center;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #d4af37;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        

        .btn:hover {
            background-color: #b38e2c;
        }

            table {
            width: 80%; /* Reducir el ancho de la tabla */
            margin: 20px auto; /* Centrando la tabla */
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 8px; /* Reducir el padding para hacer la tabla más compacta */
            border: 1px solid #ddd;
            text-align: left;
            font-size: 14px; /* Ajustar el tamaño de fuente */
        }

        table th {
            background-color: #d4af37;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1; /* Agregar efecto hover para mejor interacción */
        }

        form input, form select {
            font-size: 14px; /* Reducir el tamaño de las entradas de texto */
            padding: 6px; /* Hacer los campos más compactos */
            margin-right: 10px; /* Espacio entre los campos */
        }

        form input[type="submit"] {
            padding: 6px 12px;
        }


        .actions {
            text-align: center;
        }

        .actions a {
            padding: 6px 12px;
            background-color: #333;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            margin: 0 5px;
        }

        .actions a:hover {
            background-color: #555;
        }

        .add-product {
            margin-bottom: 20px;
        }

        .comentarios {
            margin-top: 40px;
            text-align: left;
        }

        .comentario {
            background-color: #f9f9f9;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .navbar {
            background-color: #333;
            padding: 15px;
            color: white;
            text-align: center;
        }

        .container1 {
            max-width: auto;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .btn-right {
            display: inline-block;
            padding: 10px 20px;
            background-color: #d4af37;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .btn-right:hover {
            background-color: #b38e2c;
        }

        .container1 {
            position: relative; /* Añadir esto para posicionar el botón dentro de la sección */
        }

                form {
            width: 80%; /* El mismo ancho que las tablas */
            margin: 20px auto; /* Mismos márgenes para alinear con las tablas */
            padding: 10px 0; /* Añadir espacio interior */
        }

        form input, form select {
            font-size: 14px;
            padding: 6px;
            margin-right: 10px;
        }

        form input[type="submit"] {
            padding: 6px 12px;
            margin-top: 10px; /* Espacio superior para el botón */
        }

                .comentarios {
            width: 80%; /* El mismo ancho que las tablas y formularios */
            margin: 20px auto; /* Centrar y añadir espacio arriba y abajo */
        }

        .comentario {
            background-color: #f9f9f9;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px; /* Separar cada comentario */
        }

        .comentario strong {
            display: block; /* Mostrar el nombre del usuario en su propia línea */
            margin-bottom: 5px; /* Añadir un pequeño espacio debajo */
        }

        .comentario p {
            margin: 0; /* Eliminar el margen predeterminado del párrafo */
        }

        .comentario small {
            color: #888;
            font-size: 12px;
            display: block;
            margin-top: 5px; /* Separar la fecha del comentario */
        }
    </style>
</head>
<body>
        <div class="navbar">
            <!-- Mostrar el nombre del usuario desde la sesión -->
            <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        </div>

        <div class="container1">
             <button class="btn-right" onclick="window.location.href='login.php';">Volver al Inicio</button>
        </div>
    <h2>Gestión de Productos</h2>
    
    <form action="admin.php" method="POST">
        <input type="text" name="nombre" placeholder="Nombre del producto" required>
        <input type="number" step="0.01" name="precio" placeholder="Precio" required>
        <input type="submit" name="add_product" value="Añadir Producto">
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Acciones</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id_producto']; ?></td>
            <td><?php echo $row['nombre_producto']; ?></td>
            <td><?php echo $row['precio']; ?></td>
            <td>
                <a href="admin.php?delete=<?php echo $row['id_producto']; ?>">Eliminar</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Gestión de Inventario</h2>
    <form action="admin.php" method="POST">
        <input type="text" name="nombre_ingrediente" placeholder="Nombre del ingrediente" required>
        <input type="number" name="total_almacen" placeholder="Total en almacén" required>
        <input type="submit" name="add_ingredient" value="Añadir Ingrediente">
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Nombre Ingrediente</th>
            <th>Total en Almacén</th>
        </tr>
        <?php while($row_inventario = $result_inventario->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row_inventario['id_ingrediente']; ?></td>
            <td><?php echo $row_inventario['nombre_ingrediente']; ?></td>
            <td>
                <form action="admin.php" method="POST" style="display:inline;">
                    <input type="hidden" name="id_ingrediente" value="<?php echo $row_inventario['id_ingrediente']; ?>">
                    <input type="number" name="total_almacen" value="<?php echo $row_inventario['total_almacen']; ?>" required>
                    <span>kg</span> <!-- Aquí se agrega el texto "kg" -->
                    <input type="submit" name="update_ingredient" value="Actualizar">
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Gestión de Roles de Usuarios</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Nombre de Usuario</th>
        <th>Correo Electrónico</th>
        <th>Rol Actual</th>
    </tr>
    <?php while ($usuario = $result_usuarios->fetch_assoc()): ?>
    <tr>
        <td><?php echo $usuario['id']; ?></td>
        <td><?php echo $usuario['usuario']; ?></td>
        <td><?php echo $usuario['correo']; ?></td>
        <td>
            <form action="admin.php" method="POST">
                <input type="hidden" name="id_usuario" value="<?php echo $usuario['id']; ?>">
                <select name="nuevo_rol">
                    <option value="1" <?php if ($usuario['id_rol'] == 1) echo 'selected'; ?>>Admin</option>
                    <option value="2" <?php if ($usuario['id_rol'] == 2) echo 'selected'; ?>>Usuario</option>
                </select>
                <input type="submit" name="update_role" value="Actualizar Rol">
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

</body>
</html>
