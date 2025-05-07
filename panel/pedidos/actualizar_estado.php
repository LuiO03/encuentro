<?php
include("../bd.php");

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idPedido = $_POST['id_pedido'] ?? null;
    $nuevoEstado = $_POST['nuevo_estado'] ?? null;

    if ($idPedido && $nuevoEstado) {
        $sentencia = $conect->prepare("UPDATE carrito SET estado = :estado WHERE id_carrito = :id");
        $sentencia->bindParam(":estado", $nuevoEstado);
        $sentencia->bindParam(":id", $idPedido);

        if ($sentencia->execute()) {
            echo json_encode(["status" => "success"]);
            exit();
        }
    }
}

echo json_encode(["status" => "error"]);
exit();
?>
