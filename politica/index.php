<?php
    include("../panel/bd.php");
    
    $sentencia = $conect->prepare("SELECT * FROM restaurant LIMIT 1");
    $sentencia->execute();
    $restaurant = $sentencia->fetch(PDO::FETCH_ASSOC);

    $sentencia = $conect->prepare("SELECT * FROM banners WHERE pagina = 'privacidad';");
    $sentencia->execute();
    $lista_banners = $sentencia->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuentro - Política de Privacidad</title>
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
                        <?php echo htmlspecialchars($banner['titulo'] ?: "Tu privacidad, nuestra prioridad"); ?>
                    </span>
                    <p class="descripcion_banner">
                        <?php echo htmlspecialchars($banner['descripcion'] ?: "Conoce cómo protegemos tus datos personales."); ?>
                    </p>
                </div>
            </div>
        <?php } ?>
    </div>

    <section class="container_seccion">
        <div class="seccion_cabecera">
            <span class="seccion_titulo">Política de Privacidad</span>
            <span class="seccion_subtitulo">Protegemos tu información con total transparencia</span>
        </div>

        <article class="politica_contenido">
            <h2>1. Recopilación de información</h2>
            <p>En <b><?php echo htmlspecialchars($restaurant['nombre_rest']); ?></b>, recopilamos información personal cuando realizas una reserva, te suscribes a nuestro boletín, participas en promociones o completas formularios en nuestro sitio web.</p>

            <h2>2. Uso de la información</h2>
            <p>La información recopilada se utiliza para:</p>
            <ul>
                <li>Gestionar reservas y pedidos</li>
                <li>Enviar información sobre promociones y novedades</li>
                <li>Mejorar nuestros servicios y experiencia del cliente</li>
            </ul>

            <h2>3. Protección de datos</h2>
            <p>Implementamos medidas de seguridad para proteger tu información personal. No compartimos ni vendemos tus datos a terceros sin tu consentimiento.</p>

            <h2>4. Derechos del usuario</h2>
            <p>Como usuario, tienes derecho a acceder, rectificar o eliminar tus datos personales. Para ejercer estos derechos, contáctanos en <b><?php echo htmlspecialchars($restaurant['correo']); ?></b>.</p>

            <h2>5. Cambios en la política</h2>
            <p>Nos reservamos el derecho de modificar esta política en cualquier momento. Se recomienda revisar periódicamente esta página para estar informado sobre cualquier actualización.</p>
        </article>
    </section>


<?php include('../archives/footer.php'); ?>
</html>
