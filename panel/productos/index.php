<?php 
    include("../bd.php");

    if(isset($_GET['txtID'])){
        $txtID = $_GET["txtID"];

        $sentencia = $conect->prepare("SELECT imagen FROM productos WHERE id_producto=:id");
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();
        $registro_imagen = $sentencia->fetch(PDO::FETCH_ASSOC);

        if($registro_imagen && !empty($registro_imagen['imagen'])){
            $rutaImagen = "../../img/productos/".$registro_imagen['imagen'];
            if(file_exists($rutaImagen)){
                unlink($rutaImagen);
            }
        }

        $sentencia = $conect->prepare("DELETE FROM productos WHERE id_producto=:id");
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();

        header("Location: index.php?eliminado=1");
        exit();
    }

    $sentencia = $conect->prepare("SELECT productos.*, categorias.nombre_categoria FROM productos LEFT JOIN categorias ON productos.id_categoria = categorias.id_categoria");
    $sentencia->execute();
    $lista_productos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    include ("../recursos/header.php"); 
?>

<div class="card bg-dark text-white py-3 px-4">
    <div class="card-header text-center">
        <span class="seccion_titulo">Productos</span>
        <p class="seccion_parrafo">Administra los productos en esta sección.</p>
        <div class="d-flex justify-content-center py-2">
            <a class="btn btn-success me-2" href="crear.php" role="button">
                <i class="fas fa-plus"></i> Agregar Producto
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
                        <th>Precio</th>
                        <th>Categoría</th>
                        <th>Disponibilidad</th>
                        <th>Destacado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_productos as $producto) { ?>
                        <tr>
                            <td class="text-center"><?php echo $producto['id_producto']; ?></td>
                            <td><?php echo $producto['nombre_producto']; ?></td>
                            <td>S/. <?php echo number_format($producto['precio'], 2); ?></td>
                            <td><?php echo $producto['nombre_categoria'] ?? 'Sin categoría'; ?></td>
                            <td><?php echo $producto['disponible'] ? 'Disponible' : 'No disponible'; ?></td>
                            <td>
                                <?php if ($producto['destacado']): ?>
                                    <span class="badge bg-success">
                                        <i class="fas fa-star"></i> Destacado
                                </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">
                                        <i class="fa-solid fa-star-half"></i> No Destacado
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="acciones">
                                <a class="btn btn-info me-2" href="editar.php?txtID=<?php echo $producto['id_producto']; ?>">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a class="btn btn-danger" href="index.php?txtID=<?php echo $producto['id_producto']; ?>" onclick="confirmarEliminacion(event, 'index.php?txtID=<?php echo $producto['id_producto']; ?>')">
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
                text: 'El producto ha sido eliminado correctamente.',
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
