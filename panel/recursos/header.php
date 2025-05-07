<?php
    $url_base = "http://localhost/encuentro/panel/";
    // Verificar si el usuario está logueado
    if (!isset($_SESSION['usuario_nombre'])||$_SESSION['usuario_rol'] == 'Cliente') {
        header("Location: ../cerrar.php");
        exit;
    }

    // Verificar si un 'Empleado' está intentando acceder a una página restringida
    $pagina_actual = trim(str_replace("/encuentro/panel/", "", $_SERVER['PHP_SELF']), "/");

    // Definir las páginas restringidas (ruta relativa)
    $paginas_restringidas = ['usuarios/index.php', 'clientes/index.php', 'restaurant/index.php'];

    if ($_SESSION['usuario_rol'] == 'Empleado' && in_array($pagina_actual, $paginas_restringidas)) {
        header("Location: ../restriccion.php");
        exit;
    }


    // Si el usuario es 'Empleado', restringir las opciones de menú
    $menu_items = [
        "Perfil" => ["link" => "../perfil/", "icon" => "fa-user"],
        "Inicio" => ["link" => "../inicio/", "icon" => "fa-house"],
        "Categorías" => ["link" => "../categorias/", "icon" => "fa-table"],
        "Productos" => ["link" => "../productos/", "icon" => "fa-plate-wheat"],
        "Banners" => ["link" => "../banners/", "icon" => "fa-image"],
        "Pedidos" => ["link" => "../pedidos/", "icon" => "fa-box"],
        "Reservas" => ["link" => "../reservas/", "icon" => "fa-book-open"],
        "Mensajes" => ["link" => "../mensajes/", "icon" => "fa-envelope-open-text"],
        "Usuarios" => ["link" => "../usuarios/", "icon" => "fa-users"],
        "Clientes" => ["link" => "../clientes/", "icon" => "fa-user-tie"],
        "Restaurant" => ["link" => "../restaurant/", "icon" => "fa-gear"]
    ];

    // Si el usuario es 'Empleado', restringir las opciones de menú
    if ($_SESSION['usuario_rol'] == 'Empleado') {
        unset($menu_items['Usuarios']);
        unset($menu_items['Clientes']);
        unset($menu_items['Restaurant']);
    }

    error_reporting(0);
    
?>




<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuentro - Admin</title>
    <link rel="stylesheet" href="../../css/data.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="../recursos/header.css">
    <link rel="icon" href="../../img/logos/ep_food.png" type="image/png">
    <script src="https://kit.fontawesome.com/58fc50b085.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</head>

<body class="wrapper">
    <aside id="sidebar" class="expand">
        <div class="d-flex">
            <div class="sidebar-logo w-100 text-center d-flex flex-column justify-content-center align-items-center">
                <button class="toggle-btn" type="button">
                    <i class="fa-solid fa-angles-left"></i>
                </button>
                <a href="../../" class="logo_panel">
                    <img src="../../img/logos/logo_white_horizontal.png" alt="">
                </a>
            </div>
        </div>
        <ul class="sidebar-nav">
            <!-- Menú de navegación dinámico basado en el rol del usuario -->
            <?php foreach ($menu_items as $item_name => $item) { ?>
                <li class="sidebar-item">
                    <a href="<?php echo $item['link']; ?>" class="sidebar-link">
                        <i class="fa-solid <?php echo $item['icon']; ?>"></i>
                        <span><?php echo $item_name; ?></span>
                    </a>
                </li>
            <?php } ?>
        </ul>
        <div class="sidebar-footer">
            <a href="../cerrar.php" class="sidebar-link">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Cerrar Sesión</span>
            </a>
        </div>
    </aside>
    
    <main class="main">
      <nav class="nav_admin">
        <div class="usuario_info">
            <img src="../../img/usuarios/<?php echo $_SESSION['usuario_foto']; ?>" alt="">
            <div class="usuario_datos">
                <span class="usuario_nombre">
                    <?php echo $_SESSION['usuario_nombre']; ?>
                </span>
                <span class="usuario_rol">
                    <?php echo $_SESSION['usuario_rol']; ?>
                </span>
            </div>
        </div>
        <span class="titulo_panel">Panel Administrativo</span>
        <div class="boton-superiores">
            <a class="ver_sitio" href="../../index.php" class="sidebar-link" target="_blank">
                <i class="fa-solid fa-eye"></i>
                <span>Ver Sitio</span>
            </a>
        </div>
      </nav>
