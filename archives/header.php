<?php
    $url = "http://localhost/encuentro";

    $usuario_logeado = isset($_SESSION['usuario_nombre']) && !empty($_SESSION['usuario_nombre']);
    $id_usuario = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : "";

    $consulta = $conect->prepare("SELECT cd.id_detalle, p.id_producto, p.id_categoria, p.nombre_producto, p.imagen, cd.cantidad, cd.precio_unitario, cd.precio_total, cat.nombre_categoria
        FROM carrito_detalle cd 
        INNER JOIN carrito c ON cd.id_carrito = c.id_carrito 
        INNER JOIN productos p ON cd.id_producto = p.id_producto 
        LEFT JOIN categorias cat ON p.id_categoria = cat.id_categoria
        WHERE c.id_usuario = ? AND c.estado = 'pendiente'");

    $consulta->execute([$id_usuario]);
    $productos = $consulta->fetchAll(PDO::FETCH_ASSOC);

    $consulta_total = $conect->prepare("SELECT total_pedido FROM carrito WHERE id_usuario = ? AND estado = 'pendiente' LIMIT 1");
    $consulta_total->execute([$id_usuario]);
    $total_pedido = $consulta_total->fetchColumn() ?? 0;

    $consulta_cantidad = $conect->prepare("SELECT SUM(cantidad) AS total_cantidad 
                                          FROM carrito_detalle cd 
                                          INNER JOIN carrito c ON cd.id_carrito = c.id_carrito 
                                          WHERE c.id_usuario = ? AND c.estado = 'pendiente'");
    $consulta_cantidad->execute([$id_usuario]);
    $total_cantidad = $consulta_cantidad->fetchColumn() ?? 0;

?>

<body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <header>
        <nav class="navbar">
            <div class="container_nav">
                <button id="menu-toggle" class="menu-button">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="logomenu_container">
                    <a href="<?php echo $url;?>/index.php" class="logo">
                        <img src="<?php echo $url;?>/img/logos/logo_orange_horizontal.png" alt="logo">
                    </a>
                    <ul class="menu">
                        <li><a href="<?php echo $url;?>/menu/index.php">Ver Menú</a></li>
                        <li><a href="<?php echo $url;?>/nosotros/index.php">Nosotros</a></li>
                        <li><a href="<?php echo $url;?>/contactos/index.php">Contactos</a></li>
                        <li><a href="<?php echo $url;?>/reservas/index.php">Reservar Mesa</a></li>
                    </ul>
                </div>
                <div class="icons">
                    <button class="cart">
                        <i class="fas fa-shopping-cart"></i>
                    </button>

                    <?php if ($usuario_logeado): ?>
                        <div class="user-menu">
                            <button class="user-btn">
                                <i class="fas fa-user"></i>
                                <span>
                                    <?php echo htmlspecialchars($_SESSION['usuario_nombre'] . ' ' . $_SESSION['usuario_apellido']); ?>
                                </span>
                                <i class="fas fa-caret-down"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?php echo $url;?>/cliente/perfil.php">
                                        <i class="fas fa-user"></i> Ver Perfil
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo $url;?>/cliente/pedidos.php">
                                        <i class="fas fa-box"></i> Ver Pedidos
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo $url;?>/panel/cerrar.php" class="logout">
                                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a class="login-btn" href="<?php echo $url;?>/panel/login.php">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Login</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <ul id="mobile-menu" class="mobile-menu">
                <li><a href="<?php echo $url;?>/menu/index.php">Ver Menú</a></li>
                <li><a href="<?php echo $url;?>/nosotros/index.php">Nosotros</a></li>
                <li><a href="<?php echo $url;?>/contactos/index.php">Contactos</a></li>
            </ul>
        </nav>
    </header>

    <div class="overlay" id="overlay"></div>

    <article class="carrito">
        <button class="cerrar-carrito">&times;</button>
        <?php if ($usuario_logeado): ?>
            <?php if (empty($productos)): ?>
                <h2 class="seccion_subtitulo">
                    Tu carrito está vacío.
                </h2>
                <p class="seccion_parrafo">
                    Los productos que agregues aparecerán aquí
                </p>
            <?php else: ?>
                <h2 class="seccion_subtitulo">
                    Tu Carrito <span>(<?php echo $total_cantidad; ?>)</span>
                </h2>
                <div class="carrito_productos">
                    <?php foreach ($productos as $producto_carrito): ?>
                            <article class="producto_info">
                                <a class="producto_img"  href="<?php echo $url;?>/menu/detalle_plato.php?id=<?php echo htmlspecialchars($producto_carrito['id_producto']);?>">
                                    <img src="<?php echo $url;?>/img/platos/<?php echo htmlspecialchars($producto_carrito['imagen']); ?>" class="imagen-producto" alt="Imagen del producto">
                                </a>
                                <div class="producto_texto">
                                    <h2 class="producto_nombre">
                                        <?php echo htmlspecialchars($producto_carrito['nombre_producto']); ?>
                                        <span class="cantidad_numero">(<?php echo htmlspecialchars($producto_carrito['cantidad']); ?>)</span>
                                    </h2>
                                    <a class="categoria_nombre" href="<?php echo $url;?>/menu/index.php?id_categoria=<?php echo $producto_carrito['id_categoria']; ?>">
                                        <?php echo htmlspecialchars($producto_carrito['nombre_categoria']); ?>
                                    </a>
                                    <div class="seccion_parrafo">
                                        Precio
                                        <div class="borde_precio"></div>
                                        <span>
                                            <?php echo number_format($producto_carrito['precio_unitario'], 2); ?>
                                        </span>
                                    </div>
                                    <div class="producto_numeros">
                                        <span class="producto_precio">
                                            <?php echo number_format($producto_carrito['precio_total'], 2); ?>
                                        </span>
                                        <button class="eliminar_btn" onclick="eliminarProducto(<?php echo $producto_carrito['id_detalle']; ?>)">Eliminar</button>
                                    </div>
                                </div>
                            </article>
                    <?php endforeach; ?>
                </div>
                <div class="carrito_subtotal">
                    <span class="subtotal_texto">Total Estimado:</span>
                    <span class="producto_precio_total">S/. <?php echo number_format($total_pedido, 2); ?></span>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <h2 class="seccion_subtitulo">
                Sesión no iniciada
            </h2>
            <p class="seccion_parrafo">
                Quieres pedir algunos platillos? inicia sesion o cree una cuenta.
            </p>
        <?php endif; ?>
        <?php if ($usuario_logeado): ?>
            <?php if (empty($productos)): ?>
                <a class="boton_seccion ancho_completo" href="<?php echo $url;?>/menu/index.php">Ver menú</a>
                <?php else: ?>
                <a class="boton_seccion ancho_completo" href="<?php echo $url;?>/carrito/carrito.php">Ver más Detalles</a>
            <?php endif; ?>
        <?php else: ?>
            <a class="boton_seccion ancho_completo" href="<?php echo $url;?>/panel/login.php">Iniciar sesión</a>
        <?php endif; ?>
    </article>
    <main>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const carrito = document.querySelector(".carrito");
            const carritoBtn = document.querySelector(".cart");
            const overlay = document.getElementById("overlay");
            const cerrarCarrito = document.querySelector(".cerrar-carrito");

            carritoBtn.addEventListener("click", function() {
                carrito.classList.add("activo");
                overlay.classList.add("activo");
            });

            cerrarCarrito.addEventListener("click", function() {
                carrito.classList.remove("activo");
                overlay.classList.remove("activo");
            });

            overlay.addEventListener("click", function() {
                carrito.classList.remove("activo");
                overlay.classList.remove("activo");
            });

        });

        function eliminarProducto(idDetalle) {
            Swal.fire({
                title: '¿Eliminar producto?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("<?php echo $url;?>/carrito/eliminar_carrito.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: `id_detalle=${idDetalle}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "success") {
                            Swal.fire('Eliminado', 'Producto eliminado del carrito.', 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    });
                }
            });
        }
    </script>
