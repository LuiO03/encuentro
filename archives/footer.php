<?php
    try {
        $stmt = $conect->prepare("SELECT direccion, correo, telefono, celular FROM restaurant LIMIT 1");
        $stmt->execute();
        $restaurant = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = $conect->prepare("SELECT nombre, url, icono FROM social_media WHERE estado = 1");
        $stmt->execute();
        $social_media = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Categorías
        $sentencia = $conect->prepare("SELECT * FROM categorias ORDER BY nombre_categoria ASC limit 4;");
        $sentencia->execute();
        $lista_categorias = $sentencia->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (Exception $error) {
        echo "Error: " . $error->getMessage();
    }
?>
</main>

<footer>
    <div class="footer_columnas">

        <article class="footer_columna">
            <span class="footer_titulos">INFORMACIÓN LEGAL</span>
            <ul class="footer_enlaces">
                <li><a href="<?php echo $url; ?>/reclamaciones/">Libro de Reclamaciones</a></li>
                <li><a href="<?php echo $url; ?>/politica/">Política de privacidad</a></li>
                <li><a href="<?php echo $url; ?>/terminos/">Términos y condiciones</a></li>
            </ul>
        </article>

        <article class="footer_columna">
            <span class="footer_titulos">PRODUCTOS</span>
            <ul class="footer_enlaces">
                <?php foreach ($lista_categorias as $categoria) { ?>
                    <li>
                        <a href="<?php echo $url; ?>/menu/index.php?id_categoria=<?php echo $categoria['id_categoria']; ?>">
                            <?php echo $categoria['nombre_categoria']; ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </article>

        <article class="footer_columna">
            <span class="footer_titulos">EXPLORAR</span>
            <ul class="footer_enlaces">
                <li><a href="<?php echo $url; ?>/menu/">Menú</a></li>
                <li><a href="<?php echo $url; ?>/nosotros/">Sobre Nosotros</a></li>
                <li><a href="<?php echo $url; ?>/contactos/">Contactos</a></li>
                <li><a href="<?php echo $url; ?>/reservas/">Reservar</a></li>
            </ul>
        </article>

        <article class="footer_columna">
            <span class="footer_titulos">REDES</span>
            <ul class="footer_redes">
                <?php foreach ($social_media as $red): ?>
                    <li>
                        <a href="<?php echo $red['url']; ?>" target="_blank">
                            <i class="<?php echo $red['icono']; ?>"></i> <?php echo $red['nombre']; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </article>

        <article class="footer_columna">
            <span class="footer_titulos">CONTÁCTANOS</span>
            <ul class="footer_enlaces">
                <li><a href="#"><?php echo $restaurant['telefono'] ?? 'No disponible'; ?></a></li>
                <li><a href="#"><?php echo $restaurant['correo'] ?? 'No disponible'; ?></a></li>
                <li><a href="#"><?php echo $restaurant['direccion'] ?? 'No disponible'; ?></a></li>
            </ul>
        </article>
    </div>

    <a href="<?php echo $url; ?>/index.php" class="footer_logo">
        <img src="<?php echo $url; ?>/img/logos/logo_white_vertical.png" alt="logo">
    </a>

</footer>

<script src="<?php echo $url; ?>/js/script.js"></script>
</body>
</html>