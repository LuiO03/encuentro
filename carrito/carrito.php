<?php
    include("../panel/bd.php");

    if (!isset($_SESSION['usuario_id'])) {
        header("Location: ../menu/index.php");
        exit;
    }

    $id_usuario = $_SESSION['usuario_id'];
    $consulta_direccion = $conect->prepare("SELECT direccion FROM usuarios WHERE id_usuario = :id_usuario");

    $consulta_direccion->bindParam(':id_usuario', $id_usuario);
    $consulta_direccion->execute();
    $direccion_usuario = $consulta_direccion->fetchColumn();

    $consulta_restaurant = $conect->prepare("SELECT direccion FROM restaurant WHERE id = 1");
    $consulta_restaurant->execute();
    $restaurante = $consulta_restaurant->fetch(PDO::FETCH_ASSOC);

    $direccion_restaurante = $restaurante['direccion'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito - Encuentro</title>
    <link rel="icon" href="../img/logos/ep_food.png" type="image/png">
    <link rel="stylesheet" href="../css/nav.css">
    <link rel="stylesheet" href="../css/data.css">
    <link rel="stylesheet" href="estilos.css">
    <link rel="stylesheet" href="../css/normalize.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/58fc50b085.js" crossorigin="anonymous"></script>

</head>
<body>
<?php include('../archives/header.php'); ?>
    <section class="contenedor-carrito container_seccion">
        <h2 class="seccion_titulo">Tu Carrito</h2>

        <?php if (empty($productos)): ?>
            <p class="carrito-vacio">Tu carrito está vacío.</p><br><br><br><br>
            <div class="botones-carrito">
                <a href="../menu/index.php" class="boton_seccion second">Ir al menú</a>
            </div>
        <?php else: ?>
            <table class="tabla-carrito">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Imagen</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($producto['nombre_producto']); ?>
                        </td>
                        <td class="imagen_carrito">
                            <img src="../img/platos/<?php echo htmlspecialchars($producto['imagen']); ?>" class="imagen-producto" alt="Imagen del producto">
                        </td>
                        <td>
                            <div class="botones_cantidad">
                                <button class="cantidad_btn btn-restar" onclick="actualizarCantidad(<?php echo $producto['id_detalle']; ?>, -1)">
                                    <i class="fa-solid fa-minus"></i>
                                </button>
                                <input class="cantidad_numero" id="cantidad_<?php echo $producto['id_detalle']; ?>" value="<?php echo $producto['cantidad']; ?>"readonly>
                                </input>
                                <button class="cantidad_btn btn-sumar" onclick="actualizarCantidad(<?php echo $producto['id_detalle']; ?>, 1)">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </td>
                        <td>
                            S/. <?php echo number_format($producto['precio_unitario'], 2); ?>
                        </td>
                        <td id="total_<?php echo $producto['id_detalle']; ?>">
                            S/. <?php echo number_format($producto['precio_total'], 2); ?>
                        </td>
                        <td>
                            <button class="btn-eliminar" onclick="eliminarProducto(<?php echo $producto['id_detalle']; ?>)">
                                <i class="fas fa-trash"></i>
                                Eliminar
                            </button>
                            <a href="../menu/detalle_plato.php?id=<?php echo $producto['id_producto'];?>" class="btn-detalles">
                                <i class="fa-solid fa-bowl-rice"></i>
                                Ver Plato
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h3 class="total-carrito">Total: S/. <span id="totalCarrito"><?php echo number_format($total_pedido, 2); ?></span></h3>
            <div class="producto_acciones">
                <button class="boton_seccion success" onclick="elegirMetodoEntrega()">¿Cómo Quieres Tu Pedido?</button>
                <a onclick="window.history.back(); return false;" class="boton_seccion second">Volver atrás</a>
            </div>
        <?php endif; ?>
    </section>
    <script>
        function actualizarCantidad(idDetalle, cambio) {
            let cantidadElemento = document.getElementById(`cantidad_${idDetalle}`);
            let totalElemento = document.getElementById(`total_${idDetalle}`);
            let totalCarritoElemento = document.getElementById("totalCarrito");
    
            let cantidadActual = parseInt(cantidadElemento.value) || 0;
            let nuevaCantidad = cantidadActual + cambio;
    
            if (nuevaCantidad < 1) return;
    
            fetch("actualizar_carrito.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `id_detalle=${idDetalle}&cantidad=${nuevaCantidad}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    // Actualiza el valor en el input
                    cantidadElemento.value = nuevaCantidad;
    
                    // Actualiza totales
                    let nuevoTotalProducto = parseFloat(data.nuevo_total) || 0;
                    let nuevoTotalCarrito = parseFloat(data.total_carrito) || 0;
    
                    totalElemento.innerText = `S/. ${nuevoTotalProducto.toFixed(2)}`;
                    totalCarritoElemento.innerText = `${nuevoTotalCarrito.toFixed(2)}`;
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error("Error al actualizar el carrito:", error);
                Swal.fire('Error', 'No se pudo actualizar el carrito.', 'error');
            });
        }
    
        function elegirMetodoEntrega() {
            Swal.fire({
                title: '¿Cómo quieres tu pedido?',
                showDenyButton: true,
                confirmButtonText: 'Delivery',
                denyButtonText: 'Retiro',
                icon: 'question'
            }).then((result) => {
                if (result.isConfirmed) {
                    // DELIVERY
                    const direccionUsuario = <?php echo json_encode($direccion_usuario); ?>;
                    actualizarEntrega('delivery', direccionUsuario);
                } else if (result.isDenied) {
                    // RETIRO
                    const direccionRest = <?php echo json_encode($direccion_restaurante); ?>;
                    actualizarEntrega('retiro', direccionRest);
                }
            });
        }
    
        function actualizarEntrega(tipo, direccion) {
            fetch('actualizar_entrega.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `tipo_entrega=${encodeURIComponent(tipo)}&direccion=${encodeURIComponent(direccion)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: tipo === 'delivery' ? '¡Gracias!' : '¡Perfecto!',
                        html: tipo === 'delivery' 
                            ? `<p>Tu pedido llegará a:</p><strong>${direccion}</strong><br><br>Por favor, espera un momento mientras lo preparamos.` 
                            : `<p>Puedes recoger tu pedido en:</p><strong>${direccion}</strong><br><br>Te avisaremos cuando esté listo.`,
                        confirmButtonText: 'Ver mis pedidos'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '../cliente/pedidos.php';
                        }
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'No se pudo actualizar el tipo de entrega.', 'error');
            });
        }

    
    </script>
<?php include('../archives/footer.php'); ?>
