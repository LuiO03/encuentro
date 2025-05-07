<?php
include("../panel/bd.php");

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Usuario no autenticado']);
    exit;
}

$id_usuario = $_SESSION['usuario_id'];
$id_detalle = $_POST['id_detalle'];
$nueva_cantidad = $_POST['cantidad'];

// Obtener el detalle actual
$consulta_detalle = $conect->prepare("SELECT precio_unitario, id_carrito FROM carrito_detalle WHERE id_detalle = :id_detalle");
$consulta_detalle->execute([':id_detalle' => $id_detalle]);
$detalle = $consulta_detalle->fetch(PDO::FETCH_ASSOC);

if (!$detalle) {
    echo json_encode(['status' => 'error', 'message' => 'Detalle no encontrado']);
    exit;
}

$precio_unitario = $detalle['precio_unitario'];
$id_carrito = $detalle['id_carrito'];
$nuevo_total = $precio_unitario * $nueva_cantidad;

// Actualizar el detalle del producto
$update_detalle = $conect->prepare("UPDATE carrito_detalle SET cantidad = :cantidad, precio_total = :precio_total WHERE id_detalle = :id_detalle");
$update_detalle->execute([
    ':cantidad' => $nueva_cantidad,
    ':precio_total' => $nuevo_total,
    ':id_detalle' => $id_detalle
]);

// Recalcular el total del carrito
$consulta_total = $conect->prepare("SELECT SUM(precio_total) FROM carrito_detalle WHERE id_carrito = :id_carrito");
$consulta_total->execute([':id_carrito' => $id_carrito]);
$total_carrito = $consulta_total->fetchColumn();

// Actualizar total en la tabla carrito
$update_carrito = $conect->prepare("UPDATE carrito SET total_pedido = :total WHERE id_carrito = :id_carrito");
$update_carrito->execute([
    ':total' => $total_carrito,
    ':id_carrito' => $id_carrito
]);

// Responder con los nuevos totales
echo json_encode([
    'status' => 'success',
    'nuevo_total' => $nuevo_total,
    'total_carrito' => $total_carrito
]);
?>
