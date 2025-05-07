<?php
    $destacados = $conect->prepare(" SELECT p.*, c.nombre_categoria FROM productos p LEFT JOIN categorias c ON p.id_categoria = c.id_categoria WHERE p.disponible = 1 AND p.destacado = 1 ORDER BY RAND() LIMIT 10");
    $destacados->execute();
    $lista_destacados = $destacados->fetchAll(PDO::FETCH_ASSOC);
?>
<section class="carousel-container">
    <span class="seccion_titulo">Platos Destacados</span>
    <span class="seccion_subtitulo">Delicias que Definen Nuestra Cocina</span>
    <div class="navegacion_destacados">
        <button class="scroll-button left">◀</button>
        <ul class="carousel" id="carousel">
            <?php foreach ($lista_destacados as $plato) {?>

                <a class="plato" href="<?php echo $url;?>/menu/detalle_plato.php?id=<?php echo htmlspecialchars($plato['id_producto']); ?>">
                    <div class="plato_cont">
                        <img src="<?php echo $url;?>/img/platos/<?php echo htmlspecialchars($plato['imagen']); ?>" alt="<?php echo htmlspecialchars($plato['nombre_producto']); ?>">
                    </div>
                    <div class="plato_info">
                        <span class="categoria">
                            <?php echo htmlspecialchars($plato['nombre_categoria'] ?? ''); ?>
                        </span>
                        <h3 class="nombre_plato"><?php echo htmlspecialchars($plato['nombre_producto']); ?></h3>
                        <p class="seccion_parrafo"><?php echo htmlspecialchars($plato['descripcion'] ?? 'Sin descripción'); ?></p>
                        <div class="plato_titulo">
                            <span class="precio">S/. <?php echo number_format($plato['precio'], 2); ?></span>
                        </div>
                    </div>
                </a>
            <?php } ?>
        </ul>
        <button class="scroll-button right">▶</button>
    </div>
    <div class="carousel-dots" id="carousel-dots"></div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const carousel = document.getElementById('carousel');
        const dotsContainer = document.getElementById('carousel-dots');
        const leftBtn = document.querySelector('.scroll-button.left');
        const rightBtn = document.querySelector('.scroll-button.right');
    
        let slides = Array.from(carousel.querySelectorAll('.plato'));
        let itemsPerSlide = 1;
        let totalGroups = 1;
    
        const calculateItemsPerSlide = () => {
        const slideWidth = slides[0].offsetWidth + 20; // 20 es el `gap`
        itemsPerSlide = Math.floor(carousel.offsetWidth / slideWidth) || 1;
        totalGroups = Math.ceil(slides.length / itemsPerSlide);
        };
    
        const scrollToGroup = (groupIndex) => {
        const targetIndex = groupIndex * itemsPerSlide;
        const targetSlide = slides[targetIndex];
        if (targetSlide) {
            carousel.scrollTo({
            left: targetSlide.offsetLeft,
            behavior: 'smooth'
            });
        }
        };
    
        const createDots = () => {
        dotsContainer.innerHTML = '';
        for (let i = 0; i < totalGroups; i++) {
            const dot = document.createElement('div');
            dot.classList.add('dot');
            if (i === 0) dot.classList.add('active');
            dot.addEventListener('click', () => scrollToGroup(i));
            dotsContainer.appendChild(dot);
        }
        };
    
        const getCurrentGroupIndex = () => {
        const scrollLeft = carousel.scrollLeft;
        const slideWidth = slides[0].offsetWidth + 20;
        return Math.round(scrollLeft / (slideWidth * itemsPerSlide));
        };
    
        const updateActiveDot = () => {
        const groupIndex = getCurrentGroupIndex();
        dotsContainer.querySelectorAll('.dot').forEach(dot => dot.classList.remove('active'));
        if (dotsContainer.children[groupIndex]) {
            dotsContainer.children[groupIndex].classList.add('active');
        }
        };
    
        leftBtn.addEventListener('click', () => {
        const currentGroup = getCurrentGroupIndex();
        const prevGroup = Math.max(currentGroup - 1, 0);
        scrollToGroup(prevGroup);
        });
    
        rightBtn.addEventListener('click', () => {
        const currentGroup = getCurrentGroupIndex();
        const nextGroup = Math.min(currentGroup + 1, totalGroups - 1);
        scrollToGroup(nextGroup);
        });
    
        carousel.addEventListener('scroll', () => {
        window.requestAnimationFrame(updateActiveDot);
        });
    
        window.addEventListener('resize', () => {
        calculateItemsPerSlide();
        createDots();
        updateActiveDot();
        });
    
        // Inicializar
        calculateItemsPerSlide();
        createDots();
    });
</script>

<style>
    .plato {
    flex: 0 0 250px;
    scroll-snap-align: start;
    }
</style>