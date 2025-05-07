<?php
include("../panel/bd.php");

$id_carrito = $_GET['id'] ?? null;
$usuario_id = $_SESSION['usuario_id'] ?? null;

if ($id_carrito && $usuario_id) {
    $stmt = $conect->prepare("UPDATE carrito SET estado = 'cancelado' WHERE id_carrito = ? AND id_usuario = ?");
    $stmt->execute([$id_carrito, $usuario_id]);

    // Mensaje flash para mostrar al volver a la página
    $_SESSION['mensaje_cancelado'] = "El pedido fue cancelado exitosamente.";
}

header("Location: pedidos.php");
exit;
?>
