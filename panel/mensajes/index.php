<?php 
    include("../bd.php");

    // Verificar si se ha solicitado eliminar un mensaje
    if(isset($_GET['id'])) {
        $id = $_GET["id"];

        // Eliminar el mensaje de la base de datos
        $sentencia = $conect->prepare("DELETE FROM mensajes WHERE id=:id");
        $sentencia->bindParam(":id", $id);
        $sentencia->execute();

        header("Location: index.php?eliminado=1");
        exit();
    }

    // Obtener los mensajes de contacto
    $sentencia = $conect->prepare("SELECT id, nombre, apellido, telefono, correo, mensaje, fecha_envio FROM mensajes ORDER BY fecha_envio DESC");
    $sentencia->execute();
    $mensajes = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    include ("../recursos/header.php"); 
?>

<div class="card bg-dark text-white py-3 px-4">
    <div class="card-header text-center">
        <span class="seccion_titulo">Mensajes de Contacto</span>
        <p class="seccion_parrafo">Administra los mensajes enviados desde el formulario de contacto.</p>
    </div>
    <div class="card-body">
        <div class="table-responsive table-container borde_contenido">
            <table id="table" class="table table-dark table-striped">
                <thead>
                    <tr>
                        <th class="text-center">ID</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Mensaje</th>
                        <th>Fecha de Envío</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mensajes as $mensaje) { ?>
                        <tr>
                            <td class="text-center"><?php echo $mensaje['id']; ?></td>
                            <td><?php echo $mensaje['nombre'] . " " . $mensaje['apellido']; ?></td>
                            <td><?php echo $mensaje['telefono']; ?></td>
                            <td><?php echo $mensaje['correo']; ?></td>
                            <td><?php echo nl2br(htmlspecialchars($mensaje['mensaje'])); ?></td>
                            <td><?php echo $mensaje['fecha_envio']; ?></td>
                            <td class="acciones text-center">
                                <a class="btn btn-danger" href="index.php?id=<?php echo $mensaje['id']; ?>" 
                                    onclick="confirmarEliminacion(event, 'index.php?id=<?php echo $mensaje['id']; ?>')">
                                    <i class="fas fa-trash"></i> Borrar
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function confirmarEliminacion(event, url) {
        event.preventDefault();

        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('eliminado')) {
            Swal.fire({
                title: '¡Eliminado!',
                text: 'El mensaje ha sido eliminado correctamente.',
                icon: 'success',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        }
    });
</script>

<?php include ("../recursos/footer.php"); ?>
