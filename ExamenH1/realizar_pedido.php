<?php
session_start();
include('cone.php');

if (!isset($_SESSION['id'])) {
    die("Error: No se ha encontrado el ID del usuario en la sesión.");
}

$id_usuario = $_SESSION['id'];
$carrito = json_decode(file_get_contents('php://input'), true);

if (!empty($carrito)) {
    foreach ($carrito as $item) {
        $id_producto = $item['id'];
        $cantidad = $item['cantidad'];

        // Insertar cada producto en la tabla 'pedido'
        $pedido_query = "INSERT INTO pedido (id_usuario, id_producto, cantidad, fecha_pedido) VALUES (?, ?, ?, NOW())";
        $pedido_stmt = $conn->prepare($pedido_query);
        $pedido_stmt->bind_param('iii', $id_usuario, $id_producto, $cantidad);
        $pedido_stmt->execute();
    }
    echo "Pedido realizado con éxito.";
} else {
    echo "Error: Carrito vacío.";
}
?>