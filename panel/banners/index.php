<?php 
    include("../bd.php");

    $sentencia = $conect->prepare("SELECT * FROM banners");
    $sentencia->execute();
    $lista_banners = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    include ("../recursos/header.php"); 
?>

<div class="container px-5 py-3 text-light">
    <div class="text-center mb-4">
        <h2 class="seccion_titulo">Banners</h2>
        <p class="seccion_parrafo">Administra los banners del sitio en esta sección.</p>
    </div>
    
    <div class="row borde_contenido py-4">
        <?php foreach ($lista_banners as $banner) { ?>
            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card bg-dark text-white h-100">
                    <div class="img-container">
                        <img src="../../img/banners/<?php echo $banner['imagen']; ?>" class="card-img-top" alt="Imagen del Banner">
                    </div>
                    <div class="card-body d-flex flex-column">
                        <span class="card-title seccion_subtitulo"> <?php echo $banner['titulo']; ?> </span>
                        <p class="card-text flex-grow-1 seccion_parrafo"> <?php echo $banner['descripcion']; ?> </p>
                        <a class="btn btn-info mt-auto" href="editar.php?txtID=<?php echo $banner['id_banner']; ?>">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<style>
    .img-container {
        width: 100%;
        height: 200px; /* Ajusta la altura deseada */
        overflow: hidden;
    }

    .img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Hace que la imagen cubra el espacio sin deformarse */
    }
</style>

<?php include ("../recursos/footer.php"); ?>
