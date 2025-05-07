const hamBurger = document.querySelector(".toggle-btn");
const icon = hamBurger.querySelector("i"); // Ícono dentro del botón
const sidebar = document.querySelector("#sidebar"); // Sidebar

// Función para alternar el sidebar y el icono
function toggleSidebar() {
  sidebar.classList.toggle("expand");
  
  // Alternar entre los iconos
  if (sidebar.classList.contains("expand")) {
    icon.classList.remove("fa-angles-right");
    icon.classList.add("fa-angles-left");
  } else {
    icon.classList.remove("fa-angles-left");
    icon.classList.add("fa-angles-right");
  }
}

// Evento para el botón de menú
hamBurger.addEventListener("click", toggleSidebar);

// Evento para ajustar el sidebar según el tamaño de pantalla
function adjustSidebarOnResize() {
  if (window.innerWidth <= 768) {
    sidebar.classList.remove("expand");
    icon.classList.remove("fa-angles-left");
    icon.classList.add("fa-angles-right");
  } else {
    sidebar.classList.add("expand");
    icon.classList.remove("fa-angles-right");
    icon.classList.add("fa-angles-left");
  }
}

// Ajustar el sidebar cuando cambia el tamaño de la pantalla
window.addEventListener("resize", adjustSidebarOnResize);

// Ajuste inicial al cargar la página
adjustSidebarOnResize();
