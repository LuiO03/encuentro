<?php 
    include("../bd.php");

    if(isset($_GET['txtID'])){
        $txtID = $_GET["txtID"];

        // Obtener la foto del usuario
        $sentencia = $conect->prepare("SELECT foto_perfil FROM usuarios WHERE id_usuario=:id AND rol='cliente'");
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();
        $registro_foto = $sentencia->fetch(PDO::FETCH_ASSOC);

        if($registro_foto && !empty($registro_foto['foto_perfil']) && $registro_foto['foto_perfil'] !== 'default_admin.jpg'){
            $rutaFoto = "../../img/usuarios/".$registro_foto['foto_perfil'];
            if(file_exists($rutaFoto)){
                unlink($rutaFoto);
            }
        }

        // Eliminar usuario
        $sentencia = $conect->prepare("DELETE FROM usuarios WHERE id_usuario=:id AND rol='cliente'");
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();

        header("Location: index.php?eliminado=1");
        exit();
    }

    $sentencia = $conect->prepare("SELECT id_usuario, nombre, apellido, correo, telefono, direccion, foto_perfil, rol FROM usuarios WHERE rol='cliente'");
    $sentencia->execute();
    $lista_usuarios = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    include ("../recursos/header.php"); 
?>

<div class="card bg-dark text-white py-3 px-4">
    <div class="card-header text-center">
        <span class="seccion_titulo">Clientes</span>
        <p class="seccion_parrafo">Administra los clientes en esta sección.</p>
        <div class="d-flex justify-content-center py-2">
            <a class="btn btn-success me-2" href="crear.php" role="button">
                <i class="fas fa-user-plus"></i> Agregar Cliente
            </a>
            <a class="btn btn-warning me-2" href="excel.php" role="button">
                <i class="fas fa-file-excel"></i> Excel
            </a>
            <a class="btn btn-danger" href="pdf.php" role="button">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive table-container borde_contenido">
            <table id="table" class="table table-dark table-striped">
                <thead>
                    <tr>
                        <th class="text-center">ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_usuarios as $usuario) { ?>
                        <tr>
                            <td class="text-center"><?php echo $usuario['id_usuario']; ?></td>
                            <td><?php echo $usuario['nombre']; ?> <?php echo $usuario['apellido']; ?></td>
                            <td><?php echo $usuario['correo']; ?></td>
                            <td><?php echo $usuario['telefono']; ?></td>
                            <td><?php echo $usuario['direccion']; ?></td>
                            <td class="acciones">
                                <a class="btn btn-info me-2" href="editar.php?txtID=<?php echo $usuario['id_usuario']; ?>">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a class="btn btn-danger" href="index.php?txtID=<?php echo $usuario['id_usuario']; ?>" onclick="confirmarEliminacion(event, 'index.php?txtID=<?php echo $usuario['id_usuario']; ?>')">
                                    <i class="fas fa-trash"></i> Borrar
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-muted"></div>
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
                text: 'El cliente ha sido eliminado correctamente.',
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
