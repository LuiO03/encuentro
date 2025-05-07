<?php
include("../panel/bd.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id_detalle"])) {
    $idDetalle = $_POST["id_detalle"];

    $consulta = $conect->prepare("DELETE FROM carrito_detalle WHERE id_detalle = ?");
    if ($consulta->execute([$idDetalle])) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "No se pudo eliminar el producto."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Solicitud no válida."]);
}
?>
