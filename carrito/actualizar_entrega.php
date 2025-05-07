<?php
include("../panel/bd.php");

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["status" => "error", "message" => "Usuario no autenticado."]);
    exit;
}

$id_usuario = $_SESSION['usuario_id'];
$tipo_entrega = $_POST['tipo_entrega'] ?? '';
$direccion = $_POST['direccion'] ?? '';

if ($tipo_entrega !== 'delivery' && $tipo_entrega !== 'retiro') {
    echo json_encode(["status" => "error", "message" => "Tipo de entrega no válido."]);
    exit;
}

// Actualizamos el carrito con estado pendiente del usuario
$consulta = $conect->prepare("UPDATE carrito SET tipo_entrega = :tipo_entrega WHERE id_usuario = :id_usuario AND estado = 'pendiente'");
$consulta->bindParam(':tipo_entrega', $tipo_entrega);
$consulta->bindParam(':id_usuario', $id_usuario);

if ($consulta->execute()) {
    echo json_encode(["status" => "success", "message" => "Tipo de entrega actualizado."]);
} else {
    echo json_encode(["status" => "error", "message" => "Error al actualizar."]);
}
