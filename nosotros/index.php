<?php
    include("../panel/bd.php");

    $sentencia=$conect->prepare("SELECT * FROM banners WHERE pagina = 'nosotros';");
    $sentencia->execute();
    $lista_banners= $sentencia->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuentro - nosotros</title>
    <link rel="icon" href="../img/logos/ep_food.png" type="image/png">
    <link rel="stylesheet" href="../css/nav.css">
    <link rel="stylesheet" href="../css/data.css">
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="estilos.css">
    <script src="https://kit.fontawesome.com/58fc50b085.js" crossorigin="anonymous"></script>
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
        <section class="historia_cont container_seccion">
            <article class="historia_texto">
                <span class="seccion_titulo">
                    Historia
                </span>
                <span class="seccion_subtitulo">
                    Descubre la Tradición Peruana.
                </span>
                <p class="seccion_parrafo">
                    En ”Encuentro”, nuestra misión siempre ha sido ofrecer una experiencia culinaria auténtica y memorable que celebre los ricos sabores y tradiciones de la cocina peruana.
                </p>
                <p class="seccion_parrafo">
                    A lo largo de los años, Internacional ha evolucionado, integrando técnicas modernas y tendencias gastronómicas sin perder la esencia de la cocina peruana. Hemos sido reconocidos con numerosos premios y menciones en guías culinarias y revistas gastronómicas, pero nuestro mayor orgullo sigue siendo la sonrisa y satisfacción de nuestros clientes.
                </p>
                <p class="seccion_parrafo">
                    Nuestra prioridad es entender sus objetivos y trabajar incansablemente para alcanzarlos, brindando un servicio excepcional y resultados sobresalientes. Nuestra prioridad es entender sus objetivos y trabajar incansablemente para alcanzarlos, brindando un servicio excepcional y resultados sobresalientes
                </p>
            </article>
            <article class="historia_imagenes">
                <div class="historia_img">
                    <img src="../img/platos/plato (1).webp" alt="">
                </div>
                <div class="historia_img">
                    <img src="../img/platos/plato (2).webp" alt="">
                </div>
                <div class="historia_img">
                    <img src="../img/platos/plato (3).webp" alt="">
                </div>
            </article>
        </section>
        <section class="valores_cont">
            <div class="container_seccion">
                <div class="seccion_cabecera">
                    <span class="seccion_titulo">Nuestros Valores</span>
                    <span class="seccion_parrafo">
                        Nuestros valores fundamentales nos guían en cada proyecto y relación que establecemos. Estos valores son el núcleo de nuestra identidad y nos inspiran a ofrecer lo mejor a nuestros clientes.
                    </span>
                </div>
                <div class="valores">
                    <div class="valor">
                        <span class="seccion_item">Innovación</span>
                        <p class="seccion_parrafo valor_descripcion">
                            En Turqui Agencia Publicidad y Marketing, la innovación es el motor que impulsa nuestro trabajo. 

                            Nos mantenemos a la vanguardia de las tendencias y tecnologías emergentes para ofrecer soluciones creativas y efectivas que se adapten a las necesidades cambiantes de nuestros clientes.
                        </p>
                    </div>
                    <div class="valor">
                        <span class="seccion_item">Innovación</span>
                        <p class="seccion_parrafo valor_descripcion">
                            En Turqui Agencia Publicidad y Marketing, la innovación es el motor que impulsa nuestro trabajo. 

                            Nos mantenemos a la vanguardia de las tendencias y tecnologías emergentes para ofrecer soluciones creativas y efectivas que se adapten a las necesidades cambiantes de nuestros clientes.
                        </p>
                    </div>
                    <div class="valor">
                        <span class="seccion_item">Innovación</span>
                        <p class="seccion_parrafo valor_descripcion">
                            En Turqui Agencia Publicidad y Marketing, la innovación es el motor que impulsa nuestro trabajo. 

                            Nos mantenemos a la vanguardia de las tendencias y tecnologías emergentes para ofrecer soluciones creativas y efectivas que se adapten a las necesidades cambiantes de nuestros clientes.
                        </p>
                    </div>
                </div>
            </div>
        </section>
        <section class="razon_cont container_seccion">
            <article class="razon_texto">
                <span class="seccion_titulo">
                    Somos los mejores
                </span>
                <span class="seccion_subtitulo">
                    El mejor restaurante
                </span>
                <p class="seccion_parrafo">
                    Necesitas una taquería porque es el lugar perfecto para cualquier ocasión, pero la necesitas de calidad. 
                </p>
                <p class="seccion_parrafo">
                    Siempre buscas que tengan cocineros especializados, que utilicen materia prima de calidad y que sus tacos sean al más puro estilo de México. Además, estás buscando la taquería más barata de tu ciudad sin renunciar a todo lo bueno.
                </p>
                <p class="seccion_parrafo">
                    Los más conocidos son los tacos al pastor, que podrás probarlos en todas nuestras taquerías.
                </p>
                <img src="../img/platos/las-7-mejores-sopas-peruanas-para-vencer-al-invierno.jpg" alt="">
            </article>
            <article class="razon_texto">
                <div class="razon">
                    <i class="fa-solid fa-chess-queen"></i>
                    <div>
                        <span class="seccion_subtitulo">
                            Cocineros especializados:
                        </span>
                        <p class="seccion_parrafo">
                            Solo trabajamos con cocineros especializados en comida mexicana para que tu paladar viaje hasta México sin levantarte de tu silla y sin salir de tu ciudad.
                        </p>
                    </div>
                </div>
                <div class="razon">
                    <i class="fas fa-utensils"></i>
                    <div>
                        <span class="seccion_subtitulo">
                            Platos tradicionales:
                        </span>
                        <p class="seccion_parrafo">
                            Existen muchos tipos de platos, de todos los colores, sabores e ingredientes que te puedas imaginar. Los tacos tradicionales mexicanos siempre se hacen con tortillas de maíz. 
                        </p>
                        <p class="seccion_parrafo">
                            Muchos de los platos de la gastronomía peruana utilizan este ingrediente porque es uno de los principales productores de esta fruta tan carnosa. 
                        </p>
                    </div>
                </div>
                <div class="razon">
                    <i class="fas fa-carrot"></i>
                    <div>
                        <span class="seccion_subtitulo">
                            Materia prima de calidad:
                        </span>
                        <p class="seccion_parrafo">
                            Nuestro principal objetivo es ofrecer calidad al mejor precio posible en nuestras taquerías. Para conseguirlo utilizamos únicamente productos de calidad, así nuestros tacos, quesadillas, entrantes y bebidas tienen un sabor 100% auténtico.
                        </p>
                    </div>
                </div>
            </article>
        </section>
<?php
    include( '../archives/footer.php' );
?>