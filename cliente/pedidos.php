<?php
include("../panel/bd.php");
$usuario_id = $_SESSION["usuario_id"] ?? null;

if (!$usuario_id) {
    header("Location: ../panel/login.php");
    exit;
}

// Obtener pedidos pendientes
$sentencia = $conect->prepare("SELECT * FROM carrito WHERE id_usuario = ? AND estado = 'pendiente' ORDER BY fecha_creacion DESC");
$sentencia->execute([$usuario_id]);
$pedidos_pendientes = $sentencia->fetchAll(PDO::FETCH_ASSOC);

// Obtener historial de pedidos
$historial = $conect->prepare("SELECT * FROM carrito WHERE id_usuario = ? AND estado IN ('finalizado', 'cancelado') ORDER BY fecha_creacion DESC");
$historial->execute([$usuario_id]);
$pedidos_historial = $historial->fetchAll(PDO::FETCH_ASSOC);

// Obtener reservas
$reservas_stmt = $conect->prepare("SELECT * FROM reservas WHERE id_usuario = ? ORDER BY fecha_reserva DESC");
$reservas_stmt->execute([$usuario_id]);
$reservas = $reservas_stmt->fetchAll(PDO::FETCH_ASSOC);

$mensaje = null;

if (isset($_SESSION['mensaje_cancelado'])) {
    $mensaje = $_SESSION['mensaje_cancelado'];
    unset($_SESSION['mensaje_cancelado']);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mis Pedidos y Reservas - Encuentro</title>
    <link rel="icon" href="../img/logos/ep_food.png" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/nav.css">
    <link rel="stylesheet" href="../css/data.css">
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="estilos.css">
    <link rel="stylesheet" href="../css/pedidos_reservas.css">
    <script src="https://kit.fontawesome.com/58fc50b085.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include('../archives/header.php'); ?>

    <section class="profile-section container_seccion">
        <div class="container">
            <aside class="profile-aside">
                <nav>
                    <ul>
                        <li><a href="perfil.php">Mi Perfil</a></li>
                        <li><a href="pedidos.php" class="active">Mis Pedidos</a></li>
                    </ul>
                </nav>
            </aside>

            <div class="profile-content">
                <h2 class="seccion_titulo">Pedidos Pendientes</h2>
                <?php if (count($pedidos_pendientes) > 0): ?>
                    <?php foreach ($pedidos_pendientes as $carrito): ?>
                        <div class="order-card animate-fade">
                            <h3>
                                Pedido #<?= $carrito["id_carrito"] ?>
                            </h3>
                            <div class="list_platos">
                                <p>
                                    <strong>Fecha:</strong> <?= date("d/m/Y H:i", strtotime($carrito["fecha_creacion"])) ?>
                                </p>
                                <p>
                                    <strong>Tipo de entrega:</strong> <?= ucfirst($carrito["tipo_entrega"]) ?>
                                </p>
                                <p>
                                    <strong>Total a pagar:</strong> S/ <?= number_format($carrito["total_pedido"], 2) ?>
                                </p>
                            </div>
                            <h3>Productos:</h3>
                            <ul class="list_platos">
                                <?php
                                $stmt_detalle = $conect->prepare("SELECT cd.*, p.nombre_producto, p.imagen FROM carrito_detalle cd INNER JOIN productos p ON cd.id_producto = p.id_producto WHERE cd.id_carrito = ?");
                                $stmt_detalle->execute([$carrito["id_carrito"]]);
                                $detalles = $stmt_detalle->fetchAll(PDO::FETCH_ASSOC);
                                ?>
                                <?php foreach ($detalles as $detalle): ?>
                                    <li class="producto-item">
                                        <?php if (!empty($detalle['imagen'])): ?>
                                            <img src="../img/platos/<?= $detalle['imagen'] ?>" alt="<?= $detalle['nombre_producto'] ?>">
                                        <?php endif; ?>
                                        <span>
                                            <?= htmlspecialchars($detalle["nombre_producto"]) ?> — S/ <?= number_format($detalle["precio_unitario"], 2) ?> x <?= $detalle["cantidad"] ?> = <strong>S/ <?= number_format($detalle["precio_total"], 2) ?></strong>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <button class="boton_seccion danger" onclick="cancelarPedido(<?= $carrito['id_carrito'] ?>)">Cancelar Pedido</button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No tienes pedidos pendientes.</p>
                <?php endif; ?>

                <h2 class="seccion_titulo">Historial de Pedidos</h2>
                <?php if (count($pedidos_historial) > 0): ?>
                    <?php foreach ($pedidos_historial as $carrito): ?>
                        <div class="order-card animate-fade">
                            <h3>Pedido #<?= $carrito["id_carrito"] ?> — <?= ucfirst($carrito["estado"]) ?></h3>
                            <div class="list_platos">
                                <p>
                                    <strong>Fecha:</strong> <?= date("d/m/Y H:i", strtotime($carrito["fecha_creacion"])) ?>
                                </p>
                                <p>
                                    <strong>Tipo de entrega:</strong> <?= ucfirst($carrito["tipo_entrega"]) ?>
                                </p>
                                <p>
                                    <strong>Total:</strong> S/ <?= number_format($carrito["total_pedido"], 2) ?>
                                </p>
                            </div>
                            <h3>Productos:</h3>
                            <ul class="list_platos">
                                <?php
                                $stmt_detalle = $conect->prepare("SELECT cd.*, p.nombre_producto, p.imagen FROM carrito_detalle cd INNER JOIN productos p ON cd.id_producto = p.id_producto WHERE cd.id_carrito = ?");
                                $stmt_detalle->execute([$carrito["id_carrito"]]);
                                $detalles = $stmt_detalle->fetchAll(PDO::FETCH_ASSOC);
                                ?>
                                <?php foreach ($detalles as $detalle): ?>
                                    <li class="producto-item">
                                        <?php if (!empty($detalle['imagen'])): ?>
                                            <img src="../img/platos/<?= $detalle['imagen'] ?>" alt="<?= $detalle['nombre_producto'] ?>" style="filter: grayscale(100%);">
                                        <?php endif; ?>
                                        <span>
                                            <?= htmlspecialchars($detalle["nombre_producto"]) ?> — S/ <?= number_format($detalle["precio_unitario"], 2) ?> x <?= $detalle["cantidad"] ?> = 
                                            <strong>
                                                S/ <?= number_format($detalle["precio_total"], 2) ?>
                                            </strong>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No tienes historial de pedidos.</p>
                <?php endif; ?>

                <h2 class="seccion_titulo">Mis Reservas</h2>
                <?php if (count($reservas) > 0): ?>
                    <?php foreach ($reservas as $reserva): ?>
                        <div class="order-card animate-fade">
                            <div class="list_platos">
                                <p>
                                    <strong>Reserva para el: </strong> <br>
                                    <?= date("d/m/Y", strtotime($reserva["fecha_reserva"])) ?> a las <?= substr($reserva["hora_reserva"], 0, 5) ?>
                                </p>
                                <p>
                                    <strong>Personas:</strong> <br>
                                    <?= $reserva["num_personas"] ?>
                                </p>
                                <p>
                                    <strong>Estado:</strong> <br>
                                    <?= ucfirst($reserva["estado"]) ?>
                                </p>
                                <?php if (!empty($reserva["comentario"])): ?>
                                    <p>
                                        <strong>Comentario:</strong> 
                                        <?= htmlspecialchars($reserva["comentario"]) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <?php if ($reserva['estado'] === 'pendiente'): ?>
                                <button class="boton_seccion danger" onclick="cancelarReserva(<?= $reserva['id_reserva'] ?>)">Cancelar Reserva</button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No tienes reservas registradas.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php if ($mensaje): ?>
<script>
    Swal.fire({
        title: '¡Hecho!',
        text: '<?php echo $mensaje; ?>',
        icon: 'success',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'OK',
        timer: 3000,
        timerProgressBar: true
    });
</script>
<?php endif; ?>
    <script>
        function cancelarPedido(id) {
            Swal.fire({
                title: '¿Cancelar este pedido?',
                html: '<strong>Se eliminará tu pedido pendiente y no se procesará.</strong>',
                icon: 'question',
                iconColor: '#d33',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, cancelar pedido',
                cancelButtonText: 'No, volver',
                backdrop: true,
                background: '#fff',
                customClass: {
                    popup: 'rounded shadow-lg'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'cancelar_pedido.php?id=' + id;
                }
            });
        }

        function cancelarReserva(id) {
            Swal.fire({
                title: '¿Cancelar esta reserva?',
                html: '<strong>Se liberarán las mesas reservadas y tu reserva será anulada.</strong>',
                icon: 'warning',
                iconColor: '#e69500',
                showCancelButton: true,
                confirmButtonColor: '#e69500',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, cancelar reserva',
                cancelButtonText: 'No, mantener',
                backdrop: true,
                background: '#fff',
                customClass: {
                    popup: 'rounded shadow-lg'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'cancelar_reserva.php?id=' + id;
                }
            });
        }
    </script>


    <?php include('../archives/footer.php'); ?>
</body>

</html>