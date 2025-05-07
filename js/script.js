


document.addEventListener("DOMContentLoaded", function () {
    const menuToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIcon = menuToggle.querySelector("i");

    menuToggle.addEventListener('click', function (event) {
        event.stopPropagation(); // Evita que el clic en el botón cierre el menú inmediatamente
        mobileMenu.classList.toggle('active');

        // Alternar entre los iconos de hamburguesa y equis
        if (mobileMenu.classList.contains('active')) {
            menuIcon.classList.remove("fa-bars");
            menuIcon.classList.add("fa-times");
        } else {
            menuIcon.classList.remove("fa-times");
            menuIcon.classList.add("fa-bars");
        }
    });

    // Cerrar el menú al hacer clic fuera de él
    document.addEventListener('click', function (event) {
        if (!mobileMenu.contains(event.target) && !menuToggle.contains(event.target)) {
            mobileMenu.classList.remove('active');
            menuIcon.classList.remove("fa-times");
            menuIcon.classList.add("fa-bars");
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const slider = document.querySelector(".slider");
    const dots = document.querySelectorAll(".dot");
    let currentIndex = 0;
    const totalSlides = document.querySelectorAll(".item").length;
    let sliderInterval;

    function nextSlide() {
        currentIndex = (currentIndex + 1) % totalSlides;
        updateSlide();
    }

    function updateSlide() {
        const scrollAmount = currentIndex * slider.clientWidth;
        slider.scrollTo({
            left: scrollAmount,
            behavior: "smooth"
        });

        // Actualizar indicadores
        dots.forEach(dot => dot.classList.remove("active"));
        dots[currentIndex].classList.add("active");
    }

    function restartSliderInterval() {
        clearInterval(sliderInterval); // Detener el temporizador actual
        sliderInterval = setInterval(nextSlide, 5000); // Reiniciar el temporizador
    }

    // Inicia el slider automático cada 5 segundos
    sliderInterval = setInterval(nextSlide, 5000);

    // Pausa al pasar el mouse
    slider.addEventListener("mouseenter", () => clearInterval(sliderInterval));
    slider.addEventListener("mouseleave", () => restartSliderInterval());

    // Configurar los indicadores manualmente
    dots.forEach((dot, index) => {
        dot.addEventListener("click", () => {
            currentIndex = index;
            updateSlide();
            restartSliderInterval(); // Reiniciar el temporizador al hacer clic en un dot
        });
    });
});


