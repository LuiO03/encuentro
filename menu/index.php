<?php
  include("../panel/bd.php");

  $sentencia = $conect->prepare("SELECT * FROM categorias ORDER BY nombre_categoria ASC;");
  $sentencia->execute();
  $lista_categorias = $sentencia->fetchAll(PDO::FETCH_ASSOC);

  $sentencia = $conect->prepare("SELECT * FROM banners WHERE pagina = 'menu';");
  $sentencia->execute();
  $lista_banners= $sentencia->fetchAll(PDO::FETCH_ASSOC);

  $sentencia = $conect->prepare("SELECT * FROM restaurant LIMIT 1");
  $sentencia->execute();
  $restaurant = $sentencia->fetch(PDO::FETCH_ASSOC);

  $id_categoria_seleccionada = isset($_GET['id_categoria']) ? $_GET['id_categoria'] : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Encuentro - Menú</title>
  <link rel="icon" href="../img/logos/ep_food.png" type="image/png">
  <link rel="stylesheet" href="../css/nav.css">
  <link rel="stylesheet" href="../css/data.css">
  <link rel="stylesheet" href="../css/normalize.css">
  <link rel="stylesheet" href="estilos.css">
  <script src="https://kit.fontawesome.com/58fc50b085.js" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<?php include('../archives/header.php'); ?>

<!-- BANNERS -->
<div class="slider-container">
  <?php foreach($lista_banners as $banner) { ?>
    <div class="item" style="background-image: url('../img/banners/<?php echo htmlspecialchars($banner['imagen']); ?>');">
      <div class="caption">
        <span class="titulo_banner"><?php echo htmlspecialchars($banner['titulo'] ?: "Lo mejor del Perú"); ?></span>
      </div>
    </div>
  <?php } ?>
</div>

<!-- FILTROS Y BÚSQUEDA -->
<section class="acciones_cont">
  <div class="seccion_cabecera">
    <span class="seccion_titulo">El lugar de la mejor comida.</span>
    <span class="seccion_subtitulo">Tenemos opciones perfectas para ti.</span>
  </div>

  <form id="form-filtros" method="GET" action="" class="buscador_cont" onsubmit="return false">
    <div class="buscador_wrap" style="position: relative;">
        <i class="fas fa-search icono-busqueda"></i>
        <input type="text" class="buscador" name="buscar" placeholder="Buscar Platillo..." autocomplete="off">
        <span id="limpiar-busqueda" title="Limpiar" style="display: none; position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; font-weight: bold;">
            <i class="fa-solid fa-xmark"></i>
        </span>
    </div>

    <div class="select_wrap">
      <i class="fas fa-filter"></i>
      <select class="filtro" name="id_categoria">
        <option value="">
          Todo
        </option>
        <?php foreach ($lista_categorias as $categoria) { ?>
          <option value="<?= $categoria['id_categoria']; ?>" <?= ($categoria['id_categoria'] == $id_categoria_seleccionada) ? 'selected' : ''; ?> >
            <?= htmlspecialchars($categoria['nombre_categoria']); ?>
          </option>
        <?php } ?>
      </select>
    </div>
  </form>
  <div class="botones_orden">
    <a href="#" class="boton_orden seccion_parrafo" data-orden="barato">Lo más barato</a>
    <a href="#" class="boton_orden seccion_parrafo" data-orden="nuevo">Lo más nuevo</a>
    <a href="#" class="boton_orden seccion_parrafo" data-orden="destacado">Lo más destacado</a>
  </div>
</section>

<!-- PRODUCTOS (DINÁMICO) -->
<section class="tienda_cont container_seccion">
  <div id="contenido_productos">
    <!-- Aquí se cargarán los productos con AJAX -->
  </div>
</section>

<?php include('../archives/footer.php'); ?>

<!-- SCRIPTS AJAX -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const inputBuscar = $('input[name="buscar"]');
    const limpiarBtn = $('#limpiar-busqueda');
    const productosContenedor = $('#contenido_productos');
    const selectCategoria = $('select[name="id_categoria"]');

    const cargarProductos = () => {
      const categoria = selectCategoria.val();
      const buscar = inputBuscar.val();
      const orden = $('.boton_orden.activo').data('orden') || '';

      productosContenedor.addClass('zoom-out');

      setTimeout(() => {
        $.ajax({
          url: 'filtrar_productos.php',
          method: 'GET',
          data: { id_categoria: categoria, buscar: buscar, orden: orden },
          success: function (data) {
            productosContenedor
              .html(data)
              .removeClass('zoom-out')
              .addClass('zoom-in');

            setTimeout(() => productosContenedor.removeClass('zoom-in'), 300);
          }
        });
      }, 300);
    };

    // Mostrar botón "x" cuando hay texto
    inputBuscar.on('input', function () {
      limpiarBtn.toggle(!!$(this).val());
      cargarProductos();
    });

    // Limpiar buscador
    limpiarBtn.on('click', function () {
      inputBuscar.val('');
      limpiarBtn.hide();
      cargarProductos();
    });

    // Filtro por categoría (actualiza URL)
    selectCategoria.on('change', function () {
      const categoria = $(this).val();
      const params = new URLSearchParams(window.location.search);

      if (categoria) {
        params.set('id_categoria', categoria);
      } else {
        params.delete('id_categoria');
      }

      const nuevaUrl = window.location.pathname + '?' + params.toString();
      history.pushState(null, '', nuevaUrl);

      cargarProductos();
    });

    // Ordenamiento
    $('.boton_orden').on('click', function (e) {
      e.preventDefault();
      $('.boton_orden').removeClass('activo');
      $(this).addClass('activo');
      cargarProductos();
    });

    // Soporte para botón atrás/adelante
    window.addEventListener('popstate', function () {
      const urlParams = new URLSearchParams(window.location.search);
      const idCategoria = urlParams.get('id_categoria') || '';
      selectCategoria.val(idCategoria);
      cargarProductos();
    });

    // Al cargar la página
    cargarProductos();
  });

</script>

