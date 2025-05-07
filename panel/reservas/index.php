<?php
include("../bd.php");

// Eliminar una reserva
if (isset($_GET['id_eliminar'])) {
    $idReserva = $_GET['id_eliminar'];
    $sentencia = $conect->prepare("DELETE FROM reservas WHERE id_reserva = :id");
    $sentencia->bindParam(":id", $idReserva);
    $sentencia->execute();
    header("Location: index.php?reserva_eliminada=1");
    exit();
}

// Eliminar una mesa
if (isset($_GET['id_mesa_eliminar'])) {
    $idMesa = $_GET['id_mesa_eliminar'];
    $sentencia = $conect->prepare("DELETE FROM mesas WHERE id_mesa = :id");
    $sentencia->bindParam(":id", $idMesa);
    $sentencia->execute();
    header("Location: index.php?mesa_eliminada=1");
    exit();
}

// Agregar una mesa
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nombre_mesa'], $_POST['capacidad'])) {
    $nombre_mesa = $_POST["nombre_mesa"];
    $capacidad = $_POST["capacidad"];
    $stmt = $conect->prepare("INSERT INTO mesas (nombre_mesa, capacidad) VALUES (:nombre_mesa, :capacidad)");
    $stmt->bindParam(":nombre_mesa", $nombre_mesa);
    $stmt->bindParam(":capacidad", $capacidad);
    $stmt->execute();
    header("Location: index.php?mesa_agregada=1");
    exit();
}

// Obtener datos
$sentencia = $conect->prepare("
    SELECT r.*, u.nombre, u.apellido, m.nombre_mesa 
    FROM reservas r 
    INNER JOIN usuarios u ON r.id_usuario = u.id_usuario
    LEFT JOIN reserva_mesa rm ON r.id_reserva = rm.id_reserva
    LEFT JOIN mesas m ON rm.id_mesa = m.id_mesa
    ORDER BY r.creado_en DESC
");

$sentencia->execute();
$lista_reservas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

$sentencia_mesas = $conect->prepare("SELECT * FROM mesas");
$sentencia_mesas->execute();
$lista_mesas = $sentencia_mesas->fetchAll(PDO::FETCH_ASSOC);

include("../recursos/header.php");
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="card bg-dark text-white py-3 px-4">
    <div class="card-header text-center">
        <h2 class="seccion_titulo">Gestión de Reservas y Mesas</h2>
        <p class="seccion_parrafo">Edita la información del restaurante y gestiona sus redes sociales.</p>
    </div>
    <div class="card-body">

        <?php if (isset($_GET['mesa_agregada'])) { ?>
            <script>
                Swal.fire('Mesa agregada', 'La mesa se agregó correctamente.', 'success');
            </script>
        <?php } ?>
        <?php if (isset($_GET['mesa_eliminada'])) { ?>
            <script>
                Swal.fire('Mesa eliminada', 'La mesa fue eliminada.', 'warning');
            </script>
        <?php } ?>
        <?php if (isset($_GET['reserva_eliminada'])) { ?>
            <script>
                Swal.fire('Reserva eliminada', 'La reserva fue eliminada.', 'error');
            </script>
        <?php } ?>

        <div class="row borde_contenido flex-column">
            <!-- Reservas -->
            <div class="col-12 mb-5">
                <h5 class="text-light mb-3 fw-bold">Reservas</h5>
                <div class="table-responsive table-container">
                    <table class="table table-dark table-striped no-datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Fecha y Hora</th>
                                <th>Personas</th>
                                <th>Mesa</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lista_reservas as $reserva) { 
                                $fecha = date("d/m/Y", strtotime($reserva['fecha_reserva']));
                                $hora = date("h:i A", strtotime($reserva['hora_reserva']));
                            ?>
                                <tr>
                                    <td><?php echo $reserva['id_reserva']; ?></td>
                                    <td><?php echo htmlspecialchars($reserva['nombre'] . ' ' . $reserva['apellido']); ?></td>
                                    <td><?php echo "$fecha - $hora"; ?></td>
                                    <td><?php echo $reserva['num_personas']; ?></td>
                                    <td><?php echo $reserva['nombre_mesa'] ? htmlspecialchars($reserva['nombre_mesa']) : 'No asignada'; ?></td>
                                    <td><?php echo ucfirst($reserva['estado']); ?></td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" onclick="confirmarEliminacion('reserva', <?php echo $reserva['id_reserva']; ?>)">
                                            <i class="fas fa-trash"></i> Borrar
                                        </button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mesas -->
            <div class="col-12">
                <div class="row">
                    <!-- Formulario -->
                    <div class="col-md-6 mb-4">
                        <h5 class="text-light mb-3 fw-bold">Agregar Mesa</h5>
                        <form action="index.php" method="POST">
                            <div class="mb-3">
                                <label for="nombre_mesa" class="mb-2">Nombre de la Mesa:</label>
                                <input type="number" name="nombre_mesa" class="form-control" required min="1">
                            </div>
                            <div class="mb-3">
                                <label for="capacidad" class="mb-2">Capacidad:</label>
                                <input type="number" name="capacidad" class="form-control" required min="1">
                            </div>
                            <button type="submit" class="btn btn-primary">Agregar Mesa</button>
                        </form>
                    </div>

                    <!-- Tabla de mesas -->
                    <div class="col-md-6 mb-4">
                        <h5 class="text-light mb-3 fw-bold">Mesas</h5>
                        <div class="table-responsive table-container">
                            <table class="table table-dark table-striped no-datatable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Capacidad</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($lista_mesas as $mesa) { ?>
                                        <tr>
                                            <td><?php echo $mesa['id_mesa']; ?></td>
                                            <td><?php echo $mesa['nombre_mesa']; ?></td>
                                            <td><?php echo $mesa['capacidad']; ?></td>
                                            <td><?php echo ucfirst($mesa['estado']); ?></td>
                                            <td>
                                                <button class="btn btn-danger btn-sm" onclick="confirmarEliminacion('mesa', <?php echo $mesa['id_mesa']; ?>)">
                                                    <i class="fas fa-trash"></i> Borrar
                                                </button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function confirmarEliminacion(tipo, id) {
    let mensaje = (tipo === 'reserva') ? '¿Deseas eliminar esta reserva?' : '¿Deseas eliminar esta mesa?';
    let url = (tipo === 'reserva') ? `index.php?id_eliminar=${id}` : `index.php?id_mesa_eliminar=${id}`;

    Swal.fire({
        title: mensaje,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}
</script>

<?php include("../recursos/footer.php"); ?>
