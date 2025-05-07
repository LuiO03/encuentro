<?php
include("../bd.php");

if (!isset($_GET['id_carrito'])) {
    echo json_encode(["error" => "ID de pedido no especificado"]);
    exit();
}

$idPedido = $_GET['id_carrito'];

$sentencia = $conect->prepare("SELECT cd.*, p.nombre_producto FROM carrito_detalle cd 
                               INNER JOIN productos p ON cd.id_producto = p.id_producto
                               WHERE cd.id_carrito = :id");
$sentencia->bindParam(":id", $idPedido);
$sentencia->execute();
$detalles = $sentencia->fetchAll(PDO::FETCH_ASSOC);

if (!$detalles) {
    echo json_encode(["error" => "No hay productos en este pedido"]);
    exit();
}

echo json_encode($detalles);
?>
