<?php
include("../panel/bd.php");

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["status" => "error", "message" => "Debes iniciar sesión."]);
    exit;
}

$id_usuario = $_SESSION['usuario_id'];
$id_producto = $_POST['id_producto'] ?? null;
$cantidad = $_POST['cantidad'] ?? 1;

if (!$id_producto || $cantidad <= 0) {
    echo json_encode(["status" => "error", "message" => "Datos inválidos."]);
    exit;
}

// Verificar si el usuario ya tiene un carrito
$consulta = $conect->prepare("SELECT id_carrito FROM carrito WHERE id_usuario = ? AND estado = 'pendiente'");
$consulta->execute([$id_usuario]);
$carrito = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$carrito) {
    // Si no tiene carrito, crear uno nuevo
    $insertarCarrito = $conect->prepare("INSERT INTO carrito (id_usuario) VALUES (?)");
    $insertarCarrito->execute([$id_usuario]);
    $id_carrito = $conect->lastInsertId();
} else {
    $id_carrito = $carrito['id_carrito'];
}

// Verificar si el producto ya está en el carrito
$consultaProducto = $conect->prepare("SELECT id_detalle, cantidad FROM carrito_detalle WHERE id_carrito = ? AND id_producto = ?");
$consultaProducto->execute([$id_carrito, $id_producto]);
$productoEnCarrito = $consultaProducto->fetch(PDO::FETCH_ASSOC);

if ($productoEnCarrito) {
    // Si el producto ya está en el carrito, actualizar cantidad
    $nuevaCantidad = $productoEnCarrito['cantidad'] + $cantidad;
    $actualizar = $conect->prepare("UPDATE carrito_detalle SET cantidad = ? WHERE id_detalle = ?");
    $actualizar->execute([$nuevaCantidad, $productoEnCarrito['id_detalle']]);
} else {
    // Obtener el precio del producto
    $consultaPrecio = $conect->prepare("SELECT precio FROM productos WHERE id_producto = ?");
    $consultaPrecio->execute([$id_producto]);
    $producto = $consultaPrecio->fetch(PDO::FETCH_ASSOC);
    
    if (!$producto) {
        echo json_encode(["status" => "error", "message" => "Producto no encontrado."]);
        exit;
    }

    $precio_unitario = $producto['precio'];
    $precio_total = $precio_unitario * $cantidad;

    // Insertar el producto en el carrito
    $insertarDetalle = $conect->prepare("INSERT INTO carrito_detalle (id_carrito, id_producto, cantidad, precio_unitario, precio_total) VALUES (?, ?, ?, ?, ?)");
    $insertarDetalle->execute([$id_carrito, $id_producto, $cantidad, $precio_unitario, $precio_total]);
}

echo json_encode(["status" => "success", "message" => "Producto agregado al carrito."]);
?>
