<?php
    include("../panel/bd.php");
    
    $sentencia = $conect->prepare("SELECT * FROM restaurant LIMIT 1");
    $sentencia->execute();
    $restaurant = $sentencia->fetch(PDO::FETCH_ASSOC);

    $sentencia = $conect->prepare("SELECT * FROM banners WHERE pagina = 'terminos';");
    $sentencia->execute();
    $lista_banners = $sentencia->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuentro - Términos y Condiciones</title>
    <link rel="icon" href="../img/logos/ep_food.png" type="image/png">
    <link rel="stylesheet" href="../css/nav.css">
    <link rel="stylesheet" href="../css/data.css">
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="estilos.css">
</head>
<?php include('../archives/header.php'); ?>

    <div class="slider-container">
        <?php foreach($lista_banners as $banner) { ?>
            <div class="item" style="background-image: url('../img/banners/<?php echo htmlspecialchars($banner['imagen']); ?>');">
                <div class="caption">
                    <span class="titulo_banner">
                        <?php echo htmlspecialchars($banner['titulo'] ?: "Nuestros términos y condiciones"); ?>
                    </span>
                    <p class="descripcion_banner">
                        <?php echo htmlspecialchars($banner['descripcion'] ?: "Lee atentamente las normas de uso de nuestro servicio."); ?>
                    </p>
                </div>
            </div>
        <?php } ?>
    </div>

    <section class="container_seccion">
        <div class="seccion_cabecera">
            <span class="seccion_titulo">Términos y Condiciones</span>
            <span class="seccion_subtitulo">Normas de uso y derechos de nuestros clientes</span>
        </div>

        <article class="politica_contenido">
            <h2>1. Aceptación de los términos</h2>
            <p>Al acceder y utilizar los servicios de <b><?php echo htmlspecialchars($restaurant['nombre_rest']); ?></b>, aceptas cumplir con estos términos y condiciones. Si no estás de acuerdo, te recomendamos no utilizar nuestros servicios.</p>

            <h2>2. Uso del sitio web</h2>
            <p>Nuestro sitio web está destinado para la consulta de información, reservas y pedidos en línea. No permitimos el uso indebido de nuestro contenido o recursos.</p>

            <h2>3. Reservas y pedidos</h2>
            <p>Las reservas y pedidos están sujetos a disponibilidad. Nos reservamos el derecho de cancelar cualquier reserva en caso de inconsistencias o imposibilidad de atención.</p>

            <h2>4. Precios y pagos</h2>
            <p>Los precios de los productos y servicios están sujetos a cambios sin previo aviso. Los pagos se realizan directamente en nuestro establecimiento o mediante los medios autorizados.</p>

            <h2>5. Privacidad y protección de datos</h2>
            <p>Nos comprometemos a proteger la privacidad de nuestros clientes. Consulta nuestra <a href="privacidad.php">Política de Privacidad</a> para más información sobre el tratamiento de datos.</p>

            <h2>6. Modificaciones</h2>
            <p>Nos reservamos el derecho de modificar estos términos en cualquier momento. Se recomienda revisar esta página periódicamente para conocer cualquier actualización.</p>
        </article>
    </section>

<?php include('../archives/footer.php'); ?>

