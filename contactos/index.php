<?php
    include("../panel/bd.php");
    
    $sentencia = $conect->prepare("SELECT * FROM restaurant LIMIT 1");
    $sentencia->execute();
    $restaurant = $sentencia->fetch(PDO::FETCH_ASSOC);

    $sentencia = $conect->prepare("SELECT nombre, url, icono FROM social_media WHERE estado = 1 AND restaurant_id = :restaurant_id");
    $sentencia->bindParam(":restaurant_id", $restaurant['id'], PDO::PARAM_INT);
    $sentencia->execute();
    $redes = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    $sentencia=$conect->prepare("SELECT * FROM banners WHERE pagina = 'contactos';");
    $sentencia->execute();
    $lista_banners= $sentencia->fetchAll(PDO::FETCH_ASSOC);

    $hora_abierto = date("h:i A", strtotime($restaurant['hora_abierto']));
    $hora_cerrado = date("h:i A", strtotime($restaurant['hora_cerrado']));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuentro - contactos</title>
    <link rel="icon" href="../img/logos/ep_food.png" type="image/png">
    <link rel="stylesheet" href="../css/nav.css">
    <link rel="stylesheet" href="../css/data.css">
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="estilos.css">
    <script src="https://kit.fontawesome.com/58fc50b085.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<?php
    include( '../archives/header.php' );
?>
        <div class="slider-container">
            <?php foreach($lista_banners as $banner) { ?>
                <div class="item" style="background-image: url('../img/banners/<?php echo htmlspecialchars($banner['imagen']); ?>');">
                    <div class="caption">
                        <span class="titulo_banner">
                            <?php echo htmlspecialchars($banner['titulo'] ?: "Lo mejor del Perú"); ?>
                        </span>
                        <p class="descripcion_banner">
                            <?php echo htmlspecialchars($banner['descripcion'] ?: "Descubre los mejores sabores peruanos."); ?>
                        </p>
                    </div>
                </div>
            <?php } ?>
        </div>
        <section class="container_seccion">
            <div class="seccion_cabecera">
                <span class="seccion_titulo">Estamos aquí para ti</span>
                <span class="seccion_subtitulo">No dudes en escribirnos para cualquier consulta.</span>
            </div>

            <div class="contacto">
                <!-- Mapa -->
                <article class="contacto_mapa">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d975.4838471746764!2d-75.2238552695521!3d-12.047966560883719!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x910e96228b0bf6cf%3A0x41a3a9ccc4ca5b21!2sRestaurante%20Huancahuasi%20Huancayo!5e0!3m2!1ses-419!2spe!4v1741835741050!5m2!1ses-419!2spe" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </article>

                <!-- Formulario -->
                <article class="contacto_formulario">
                    <span class="seccion_titulo">Formulario</span>
                    <form id="contactoForm" action="contactos.php" method="POST">
                    <input type="text" name="nombre" id="nombre" placeholder="Nombre Completo" required pattern="[A-Za-zÁáÉéÍíÓóÚúÑñ\s]+" title="Solo se permiten letras y espacios">
                    <input type="text" name="apellido" id="apellido" placeholder="Apellido Completo" required pattern="[A-Za-zÁáÉéÍíÓóÚúÑñ\s]+" title="Solo se permiten letras y espacios">
                    <input type="tel" name="telefono" id="telefono" placeholder="Ingrese Teléfono" required pattern="[0-9]{9}" title="Debe contener 9 dígitos numéricos">
                    <input type="email" name="correo" id="correo" placeholder="Correo Electrónico" required>
                    <textarea name="mensaje" id="mensaje" placeholder="Escribe tu mensaje (mínimo 10 caracteres)" required minlength="10"></textarea>

                    <div class="form_checkbox">
                        <input type="checkbox" id="acepto" required>
                        <label for="acepto">Acepto los términos y condiciones</label>
                    </div>

                    <button class="boton_seccion" type="submit">Enviar</button>
                </form>

                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const urlParams = new URLSearchParams(window.location.search);
                        if (urlParams.has('mensaje') && urlParams.get('mensaje') === 'exito') {
                            Swal.fire({
                                title: 'Mensaje enviado',
                                text: 'Tu mensaje ha sido enviado correctamente. Nos pondremos en contacto contigo pronto.',
                                icon: 'success',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    });
                </script>

                </article>
            </div>
        </section>
        <hr>
        <section class="container_seccion">
            <div class="seccion_cabecera">
                <span class="seccion_titulo">Te Esperamos</span>
                <span class="seccion_subtitulo">Encuentranos en cualquiera de los siguientes canales:</span>
            </div>
            <div class="contacto_contenedor">
                <!-- Columna 1: Info de contacto -->
                <article class="contacto_info">
                    <div class="info_item">
                        <i class="fas fa-clock"></i>
                        <div class="info_texto">
                            <span class="seccion_item">Hora de Atención</span>
                            <p class="seccion_parrafo"><?php echo $hora_abierto; ?> a <?php echo $hora_cerrado; ?></p>
                        </div>
                    </div>
                    <div class="info_item">
                        <i class="fas fa-envelope"></i>
                        <div class="info_texto">
                            <span class="seccion_item">Correo</span>
                            <p class="seccion_parrafo"><?php echo htmlspecialchars($restaurant['correo']); ?></p>
                        </div>
                    </div>
                    <div class="info_item">
                        <i class="fas fa-phone-alt"></i>
                        <div class="info_texto">
                            <span class="seccion_item">Números</span>
                            <p class="seccion_parrafo"><?php echo htmlspecialchars($restaurant['telefono']); ?> / <?php echo htmlspecialchars($restaurant['celular']); ?></p>
                        </div>
                    </div>
                    <div class="info_item">
                        <i class="fa-solid fa-location-dot"></i>
                        <div class="info_texto">
                            <span class="seccion_item">Dirección</span>
                            <p class="seccion_parrafo"><?php echo htmlspecialchars($restaurant['direccion']); ?> </p>
                        </div>
                    </div>
                </article>

                <article class="contacto_redes">
                    <?php foreach ($redes as $red) { ?>
                        <div class="info_item">
                            <i class="<?php echo htmlspecialchars($red['icono']); ?>"></i>
                            <div class="info_texto">
                                <span class="seccion_item"><?php echo ucfirst($red['nombre']); ?></span>
                                <a class="seccion_parrafo" href="<?php echo htmlspecialchars($red['url']); ?>" target="_blank">
                                    <?php echo htmlspecialchars($red['url']); ?>
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                </article>
            </div>

            <!-- Imagen inferior -->
            <article class="contacto_imagen">
                <img src="../img/recursos/Hands Phone.png" alt="Contacto">
            </article>
        </section>
<?php
    include( '../archives/footer.php' );
?>