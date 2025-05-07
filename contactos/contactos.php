<?php
include("../panel/bd.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nombre'])) {
    // Recibir datos del formulario con validación básica
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $apellido = htmlspecialchars(trim($_POST['apellido']));
    $telefono = htmlspecialchars(trim($_POST['telefono']));
    $correo = htmlspecialchars(trim($_POST['correo']));
    $mensaje = htmlspecialchars(trim($_POST['mensaje']));

    if (!empty($nombre) && !empty($apellido) && !empty($telefono) && !empty($correo) && !empty($mensaje)) {
        try {
            $stmt = $conect->prepare("INSERT INTO mensajes (nombre, apellido, telefono, correo, mensaje) VALUES (:nombre, :apellido, :telefono, :correo, :mensaje)");
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":apellido", $apellido);
            $stmt->bindParam(":telefono", $telefono);
            $stmt->bindParam(":correo", $correo);
            $stmt->bindParam(":mensaje", $mensaje);
            $stmt->execute();

            // Redireccionar con mensaje de éxito
            header("Location: index.php?mensaje=exito");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Error: Todos los campos son obligatorios.";
    }
}
?>
