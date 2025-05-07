<?php
    include("panel/bd.php");
    $sentencia=$conect->prepare("SELECT * FROM banners WHERE pagina = 'inicio' ORDER BY id_banner asc;");
    $sentencia->execute();
    $lista_banners= $sentencia->fetchAll(PDO::FETCH_ASSOC);

    $sentencia=$conect->prepare("SELECT * FROM categorias ORDER BY id_categoria asc;");
    $sentencia->execute();
    $lista_categorias= $sentencia->fetchAll(PDO::FETCH_ASSOC);

    $productos_baratos = $conect->prepare("SELECT * FROM productos WHERE disponible = 1 ORDER BY precio ASC LIMIT 6");
    $productos_baratos->execute();
    $lista_baratos = $productos_baratos->fetchAll(PDO::FETCH_ASSOC);

    $productos_caros = $conect->prepare("SELECT * FROM productos WHERE disponible = 1 ORDER BY precio DESC LIMIT 6");
    $productos_caros->execute();
    $lista_caros = $productos_caros->fetchAll(PDO::FETCH_ASSOC);

    
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuentro - Restaurant</title>
    <link rel="icon" href="img/logos/ep_food.png" type="image/png">
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/data.css">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/estilos.css">
    <script src="https://kit.fontawesome.com/58fc50b085.js" crossorigin="anonymous"></script>
</head>
<?php
    include( 'archives/header.php' );
?>
    <main>
        <div class="slider-container">
            <?php if (!empty($lista_banners)) { ?>
                <div class="slider">
                    <?php foreach($lista_banners as $banner) { ?>
                        <div class="item" style="background-image: url('img/banners/<?php echo htmlspecialchars($banner['imagen']); ?>');">
                            <div class="caption">
                                <span class="titulo_banner">
                                    <?php echo htmlspecialchars($banner['titulo'] ?: "Lo mejor del Perú"); ?>
                                </span>
                                <p class="descripcion_banner">
                                    <?php echo htmlspecialchars($banner['descripcion'] ?: "Descubre los mejores sabores peruanos."); ?>
                                </p>
                                <a href="<?php echo $url;?>/menu/" class="boton_banner">Ver menú</a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <p>No hay banners disponibles.</p>
            <?php } ?>
            <div class="indicators">
                <span class="dot active"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>
        </div>
        <section class="presentacion_cont container_seccion">
            <div class="contenedor_imagenes">
                <div class="imagen_presentacion">
                    <img src="img/recursos/barbecue-food-1 1.png" alt="presentacion1">
                </div>
                <div class="imagen_presentacion">
                    <img src="img/recursos/barbecue-foods-2 1.png" alt="presentacion2">
                </div>
            </div>
            <div class="contenedor_texto">
                <span class="seccion_titulo">El lugar de la mejor comida.</span>
                <span class="seccion_subtitulo">El lugar de la mejor comida.</span>
                <p class="seccion_parrafo">
                    En “Encuentro”, nuestra misión siempre ha sido ofrecer a nuestros clientes una experiencia culinaria auténtica y memorable que celebre los ricos sabores y tradiciones de la cocina peruana.
                </p>

                <a href="<?php echo $url;?>/nosotros/" class="boton_seccion">Conocer más</a>
            </div>
        </section>
        <section class="categorias_cont">
            <div class="container_seccion">

                <div class="seccion_cabecera">
                    <span class="seccion_titulo">El lugar de la mejor comida.</span>
                    <span class="seccion_subtitulo">Tenemos opciones perfectas para ti.</span>
                </div>
                <div class="categorias">

                <?php foreach($lista_categorias as $categoria) { ?>
                    <article class="categoria">
                        <a class="categoria_imagen" href="menu/index.php?id_categoria=<?php echo $categoria['id_categoria']; ?>">
                            <img src="img/categorias/<?php echo htmlspecialchars($categoria['imagen']); ?>" alt="<?php echo htmlspecialchars($categoria['nombre_categoria']); ?>">
                        </a>
                        <div class="categoria_texto">
                            <span class="seccion_item"><?php echo htmlspecialchars($categoria['nombre_categoria']); ?></span>
                            <p class="parrafo_cuerpo">
                                <?php echo htmlspecialchars($categoria['descripcion'] ?: "Sin descripción disponible."); ?>
                            </p>
                        </div>
                    </article>
                <?php } ?>
                </div>
            </div>
        </section>
        <?php
            include( 'archives/destacados.php' );
        ?>
        <section class="menu_cont">
            <div class="container_seccion">
                <div class="menu_tabs">
                    <button class="tab active" onclick="showMenu('barato')">Lo más barato</button>
                    <button class="tab" onclick="showMenu('caro')">Lo más caro</button>
                </div>

                <div id="menu_barato" class="menu_items">
                    <?php foreach ($lista_baratos as $producto) { ?>
                        <a class="dish" href="menu/detalle_plato.php?id=<?php echo htmlspecialchars($producto['id_producto']); ?>">
                            <img src="img/platos/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>">
                            <div class="dish_info">
                                <div class="dish_text">
                                    <span class="seccion_item"><?php echo htmlspecialchars($producto['nombre_producto']); ?></span>
                                    <p class="seccion_parrafo"><?php echo htmlspecialchars($producto['descripcion'] ?: "Sin descripción"); ?></p>
                                </div>
                                <span class="price">S/. <?php echo number_format($producto['precio'], 2); ?></span>
                            </div>
                        </a>
                    <?php } ?>
                </div>

                <div id="menu_caro" class="menu_items" style="display: none;">
                    <?php foreach ($lista_caros as $producto) { ?>
                        <a class="dish" href="menu/detalle_plato.php?id=<?php echo htmlspecialchars($producto['id_producto']); ?>">
                            <img src="img/platos/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>">
                            <div class="dish_info">
                                <div class="dish_text">
                                    <span class="seccion_item"><?php echo htmlspecialchars($producto['nombre_producto']); ?></span>
                                    <p class="seccion_parrafo"><?php echo htmlspecialchars($producto['descripcion'] ?: "Sin descripción"); ?></p>
                                </div>
                                <span class="price">S/. <?php echo number_format($producto['precio'], 2); ?></span>
                            </div>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </section>
        <script>
            function showMenu(type) {
                document.getElementById("menu_barato").style.display = (type === "barato") ? "flex" : "none";
                document.getElementById("menu_caro").style.display = (type === "caro") ? "flex" : "none";
    
                let tabs = document.querySelectorAll(".tab");
                tabs.forEach(tab => tab.classList.remove("active"));
                event.target.classList.add("active");
            }
        </script>
    </main>

<?php
    include( 'archives/footer.php' );
?>
