<?php
include("../panel/bd.php");

$id_reserva = $_GET['id'] ?? null;
$usuario_id = $_SESSION['usuario_id'] ?? null;

if ($id_reserva && $usuario_id) {
    // Cambiar estado de la reserva
    $stmt = $conect->prepare("UPDATE reservas SET estado = 'cancelada' WHERE id_reserva = ? AND id_usuario = ?");
    $stmt->execute([$id_reserva, $usuario_id]);

    // Liberar las mesas asociadas
    $mesas = $conect->prepare("SELECT id_mesa FROM reserva_mesa WHERE id_reserva = ?");
    $mesas->execute([$id_reserva]);
    $mesas_asociadas = $mesas->fetchAll(PDO::FETCH_ASSOC);

    foreach ($mesas_asociadas as $mesa) {
        $conect->prepare("UPDATE mesas SET estado = 'disponible' WHERE id_mesa = ?")->execute([$mesa['id_mesa']]);
    }

    // Opcional: eliminar relación reserva-mesa
    $conect->prepare("DELETE FROM reserva_mesa WHERE id_reserva = ?")->execute([$id_reserva]);

    $_SESSION['mensaje_cancelado'] = "La reserva fue cancelada exitosamente.";
}

header("Location: pedidos.php");
exit;
?>
