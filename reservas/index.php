<?php
    include("../panel/bd.php");

    $id_usuario = $_SESSION['usuario_id'] ?? NULL;

    // Obtener horario del restaurante
    $query = $conect->query("SELECT hora_abierto, hora_cerrado FROM restaurant LIMIT 1");
    $horario = $query->fetch(PDO::FETCH_ASSOC);
    $hora_abierto = $horario['hora_abierto'];
    $hora_cerrado = $horario['hora_cerrado'];

    $fecha_reserva = $_POST['fecha'] ?? '';
    $hora_reserva = $_POST['hora'] ?? '';
    $num_personas = $_POST['personas'] ?? '';
    $comentario = $_POST['comentario'] ?? '';

    $obtenerReservas = $conect->prepare("SELECT r.fecha_reserva, r.hora_reserva, r.num_personas, r.comentario, m.id_mesa, m.capacidad
    FROM reservas r
    JOIN reserva_mesa rm ON r.id_reserva = rm.id_reserva
    JOIN mesas m ON rm.id_mesa = m.id_mesa
    WHERE r.id_usuario = :id_usuario");
    $obtenerReservas->bindParam(":id_usuario", $id_usuario);
    $obtenerReservas->execute();
    $reservas = $obtenerReservas->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!$id_usuario) {
            $_SESSION['alert'] = [
                'title' => '¡Debes iniciar sesión!',
                'text' => 'Por favor, inicia sesión para realizar una reserva.',
                'icon' => 'warning',
                'redirect' => '../panel/login.php'
            ];
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        // Validar hora dentro del horario de atención
        if ($hora_reserva < $hora_abierto || $hora_reserva > $hora_cerrado) {
            $_SESSION['alert'] = [
                'title' => 'Hora fuera del horario de atención',
                'text' => "Elige una hora entre $hora_abierto y $hora_cerrado.",
                'icon' => 'error',
                'redirect' => ''
            ];
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        // Verificar si el usuario ya tiene una reserva para la misma fecha y hora
        $verificarReserva = $conect->prepare("SELECT * FROM reservas 
            WHERE id_usuario = :id_usuario 
            AND fecha_reserva = :fecha 
            AND hora_reserva = :hora");
        $verificarReserva->bindParam(":id_usuario", $id_usuario);
        $verificarReserva->bindParam(":fecha", $fecha_reserva);
        $verificarReserva->bindParam(":hora", $hora_reserva);
        $verificarReserva->execute();

        if ($verificarReserva->rowCount() > 0) {
            $_SESSION['alert'] = [
                'title' => 'Ya tienes una reserva activa',
                'text' => 'Ya hiciste una reserva para esta fecha y hora.',
                'icon' => 'warning',
                'redirect' => ''
            ];
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        // Verificar si hay mesas disponibles
        $sql = "SELECT m.id_mesa, m.capacidad
                FROM mesas m
                WHERE m.estado = 'disponible' AND m.capacidad >= :capacidad
                AND NOT EXISTS (
                    SELECT 1 FROM reserva_mesa rm
                    INNER JOIN reservas r ON rm.id_reserva = r.id_reserva
                    WHERE rm.id_mesa = m.id_mesa
                    AND r.fecha_reserva = :fecha AND r.hora_reserva = :hora
                )
                ORDER BY m.capacidad ASC
                LIMIT 1";

        $stmt = $conect->prepare($sql);
        $stmt->bindParam(":capacidad", $num_personas, PDO::PARAM_INT);
        $stmt->bindParam(":fecha", $fecha_reserva);
        $stmt->bindParam(":hora", $hora_reserva);
        $stmt->execute();
        $mesa_disponible = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$mesa_disponible) {
            $_SESSION['alert'] = [
                'title' => 'Sin mesas disponibles',
                'text' => 'No hay mesas libres para esa fecha, hora y cantidad de personas.',
                'icon' => 'error',
                'redirect' => ''
            ];
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        // Registrar la reserva
        $sentencia = $conect->prepare("INSERT INTO reservas (id_usuario, fecha_reserva, hora_reserva, num_personas, comentario)
                                    VALUES (:id_usuario, :fecha_reserva, :hora_reserva, :num_personas, :comentario)");
        $sentencia->bindParam(":id_usuario", $id_usuario);
        $sentencia->bindParam(":fecha_reserva", $fecha_reserva);
        $sentencia->bindParam(":hora_reserva", $hora_reserva);
        $sentencia->bindParam(":num_personas", $num_personas);
        $sentencia->bindParam(":comentario", $comentario);

        if ($sentencia->execute()) {
            $id_reserva = $conect->lastInsertId();

            // Asignar la mesa
            $stmt2 = $conect->prepare("INSERT INTO reserva_mesa (id_reserva, id_mesa) VALUES (:id_reserva, :id_mesa)");
            $stmt2->bindParam(":id_reserva", $id_reserva);
            $stmt2->bindParam(":id_mesa", $mesa_disponible['id_mesa']);
            $stmt2->execute();

            // Cambiar estado de la mesa
            $updateMesa = $conect->prepare("UPDATE mesas SET estado = 'reservada' WHERE id_mesa = :id_mesa");
            $updateMesa->bindParam(":id_mesa", $mesa_disponible['id_mesa']);
            $updateMesa->execute();

            $_SESSION['alert'] = [
                'title' => '¡Reserva realizada con éxito!',
                'text' => 'Te esperamos en la fecha y hora seleccionadas.',
                'icon' => 'success',
                'redirect' => ''
            ];
        } else {
            $_SESSION['alert'] = [
                'title' => 'Error al reservar',
                'text' => 'Ocurrió un problema, intenta nuevamente.',
                'icon' => 'error',
                'redirect' => ''
            ];
        }




        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Encuentro - Reservas</title>
    <link rel="icon" href="../img/logos/ep_food.png" type="image/png">
    <link rel="stylesheet" href="../css/nav.css">
    <link rel="stylesheet" href="../css/data.css">
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="estilos.css">
    <script src="https://kit.fontawesome.com/58fc50b085.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const usuarioLogueado = <?= $id_usuario ? 'true' : 'false' ?>;
        function verificarSesion(event) {
            if (!usuarioLogueado) {
                event.preventDefault();
                Swal.fire({
                    title: '¡Debes iniciar sesión!',
                    text: 'Por favor, inicia sesión para realizar una reserva.',
                    icon: 'warning',
                    confirmButtonText: 'Iniciar sesión'
                }).then(() => {
                    window.location.href = '../panel/login.php';
                });
                return false;
            }
        }
    </script>
</head>
<body>
<?php include('../archives/header.php'); ?>

<section class="reserva-container container_seccion">
    <article class="reserva_form">
        <span class="seccion_titulo">Reserva tu Mesa</span>
        <p>Completa el formulario para reservar en nuestro restaurante.</p>
        <form action="" method="POST" id="reservaForm" onsubmit="return verificarSesion(event)">
        <label for="fecha">Fecha:</label>
            <input type="date" id="fecha" name="fecha" value="<?= $fecha_reserva ?>" required min="<?= date('Y-m-d'); ?>">

            <label for="hora">Hora:</label>
            <input type="time" id="hora" name="hora" value="<?= $hora_reserva ?>" required min="<?= $hora_abierto ?>" max="<?= $hora_cerrado ?>">

            <label for="personas">Número de Personas:</label>
            <input type="number" placeholder="Ingrese un número" id="personas" name="personas" value="<?= $num_personas ?>" required min="1" max="20">

            <label for="comentario">Comentario (Opcional):</label>
            <textarea id="comentario" name="comentario" placeholder="¿Alguna solicitud especial?"><?= $comentario ?></textarea>

            <button class="boton_seccion" type="submit">Reservar Ahora</button>
        </form>
    </article>
    <article class="reserva_imagen">
        <img src="../img/recursos/salon-principal.jpg" alt="">
    </article>
    <article class="mis-reservas-container">
        <span class="seccion_titulo">Mis Reservas</span>
        <?php if (count($reservas) > 0): ?>
            <div class="reservas-cards">
                <?php foreach ($reservas as $reserva): ?>
                    <div class="reserva-card">
                        <h3>Reserva para el <?= date('d/m/Y', strtotime($reserva['fecha_reserva'])) ?> a las <?= date('H:i', strtotime($reserva['hora_reserva'])) ?></h3>
                        <p><strong>Número de Personas:</strong> <?= $reserva['num_personas'] ?></p>
                        <p><strong>Mesa:</strong> <?= $reserva['id_mesa'] ?> (Capacidad: <?= $reserva['capacidad'] ?> personas)</p>
                        <p><strong>Comentario:</strong> <?= $reserva['comentario'] ?: 'Ninguno' ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No tienes reservas registradas.</p>
        <?php endif; ?>
    </article>
</section>


<?php include('../archives/footer.php'); ?>

<!-- Alerta desde PHP -->
<?php if (isset($_SESSION['alert'])): ?>
<script>
Swal.fire({
    title: '<?= $_SESSION['alert']['title'] ?>',
    text: '<?= $_SESSION['alert']['text'] ?>',
    icon: '<?= $_SESSION['alert']['icon'] ?>',
    confirmButtonText: 'Aceptar'
}).then(() => {
    <?php if ($_SESSION['alert']['redirect']): ?>
        window.location.href = '<?= $_SESSION['alert']['redirect'] ?>';
    <?php endif; ?>
});
</script>
<?php unset($_SESSION['alert']); ?>
<?php endif; ?>

<script>
// Guardar campos en localStorage
document.addEventListener("DOMContentLoaded", function () {
    ["fecha", "hora", "personas", "comentario"].forEach(id => {
        const input = document.getElementById(id);
        input.value = localStorage.getItem(id) || "";
        input.addEventListener("input", () => {
            localStorage.setItem(id, input.value);
        });
    });

    document.getElementById("reservaForm").addEventListener("submit", () => {
        ["fecha", "hora", "personas", "comentario"].forEach(key => localStorage.removeItem(key));
    });
});
</script>
