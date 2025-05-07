<?php 
    include("../bd.php");

    if(isset($_GET['txtID'])){
        $txtID = $_GET["txtID"];

        // Buscar la imagen de la categoría antes de eliminarla
        $sentencia = $conect->prepare("SELECT imagen FROM categorias WHERE id_categoria=:id");
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();
        $registro_imagen = $sentencia->fetch(PDO::FETCH_ASSOC);

        if($registro_imagen && !empty($registro_imagen['imagen'])){
            $rutaImagen = "../../img/categorias/".$registro_imagen['imagen'];
            if(file_exists($rutaImagen)){
                unlink($rutaImagen); //
            }
        }

        $sentencia = $conect->prepare("DELETE FROM categorias WHERE id_categoria=:id");
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();

        header("Location: index.php?eliminado=1");
        exit();
    }

    $sentencia = $conect->prepare("SELECT * FROM categorias");
    $sentencia->execute();
    $lista_categorias = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    include ("../recursos/header.php"); 
?>

<div class="card bg-dark text-white py-3 px-4">
    <div class="card-header text-center">
        <span class="seccion_titulo">Categorías</span>
        <p class="seccion_parrafo">Administra las categorías de productos en esta sección.</p>
        <div class="d-flex justify-content-center py-2">
            <a class="btn btn-success me-2" href="crear.php" role="button">
                <i class="fas fa-plus"></i> Agregar Categoría
            </a>
        </div>
    </div>

    <div class="container py-4">
        <div class="row borde_contenido">
            <?php foreach ($lista_categorias as $categoria) { ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 my-3">
                    <div class="card bg-dark text-white shadow-lg h-100 border-0">
                        <div class="card-body text-center d-flex flex-column justify-content-between">
                            <div class="mb-3">
                                <img src="../../img/categorias/<?php echo $categoria['imagen']; ?>" class="img-fluid rounded" style="height: 200px; object-fit: cover;">
                            </div>
                            <h5 class="card-title"><?php echo $categoria['nombre_categoria']; ?></h5>
                            <div class="d-flex flex-column gap-2 justify-content-center">
                                <a class="btn btn-info w-100" href="editar.php?txtID=<?php echo $categoria['id_categoria']; ?>">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a class="btn btn-danger w-100" href="index.php?txtID=<?php echo $categoria['id_categoria']; ?>" onclick="confirmarEliminacion(event, 'index.php?txtID=<?php echo $categoria['id_categoria']; ?>')">
                                    <i class="fas fa-trash"></i> Borrar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
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
                text: 'La categoría ha sido eliminada correctamente.',
                icon: 'success',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                // Eliminar el parámetro de la URL sin recargar la página
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        }
    });
</script>


<?php include ("../recursos/footer.php"); ?>
