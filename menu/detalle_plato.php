<?php
    include("../panel/bd.php");

    if (!isset($_GET['id']) || empty($_GET['id'])) {
        echo "Producto no encontrado.";
        exit;
    }

    $id_producto = $_GET['id'];

    $sentencia = $conect->prepare("SELECT productos.*, categorias.nombre_categoria 
                                FROM productos 
                                INNER JOIN categorias ON productos.id_categoria = categorias.id_categoria 
                                WHERE id_producto = ?");
    $sentencia->execute([$id_producto]);
    $producto = $sentencia->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        echo "Producto no encontrado.";
        exit;
    }

    $cantidadEnCarrito = 0;

if (isset($_SESSION['usuario_id'])) {
    $id_usuario = $_SESSION['usuario_id'];

    // Buscar carrito pendiente del usuario
    $stmtCarrito = $conect->prepare("SELECT id_carrito FROM carrito WHERE id_usuario = ? AND estado = 'pendiente'");
    $stmtCarrito->execute([$id_usuario]);
    $carrito = $stmtCarrito->fetch(PDO::FETCH_ASSOC);

    if ($carrito) {
        $id_carrito = $carrito['id_carrito'];

        // Verificar si este producto está en el carrito
        $stmtDetalle = $conect->prepare("SELECT cantidad FROM carrito_detalle WHERE id_carrito = ? AND id_producto = ?");
        $stmtDetalle->execute([$id_carrito, $id_producto]);
        $detalle = $stmtDetalle->fetch(PDO::FETCH_ASSOC);

        if ($detalle) {
            $cantidadEnCarrito = $detalle['cantidad'];
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($producto['nombre_producto']); ?> - Encuentro</title>
    <link rel="stylesheet" href="../css/nav.css">
    <link rel="stylesheet" href="../css/data.css">
    <link rel="icon" href="../img/logos/ep_food.png" type="image/png">
    <link rel="stylesheet" href="estilos.css">
    <link rel="stylesheet" href="../css/normalize.css">
    <script src="https://kit.fontawesome.com/58fc50b085.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<?php include('../archives/header.php'); ?>

<section class="seccion_plato" style="background-image: url('../img/platos/<?php echo htmlspecialchars($producto['imagen']); ?>');">
    <div class="container_seccion cont_plato">
        <section class="producto_detalle_imagen">
            <div class="detalle_imagen" style="background-image: url('../img/platos/<?php echo htmlspecialchars($producto['imagen']); ?>');">
            </div>
            <img src="../img/platos/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="">
        </section>
        <section class="producto_detalle_container">
            <div class="titulo_info">
                <div>
                    <a class="producto_detalle_categoria" href="../menu/index.php?id_categoria=<?php echo $producto['id_categoria']; ?>">
                        <?php echo htmlspecialchars($producto['nombre_categoria']); ?> 
                    </a>
                    <span class="seccion_titulo">
                        <?php echo htmlspecialchars($producto['nombre_producto']); ?>
                    </span>
                </div>
            </div>
            <p class="producto_detalle_descripcion">
                <?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?>
            </p>
            <div class="mensaje_info" style=" <?php echo ($cantidadEnCarrito > 0) ? '' : 'display: none;'; ?>">
                <?php if ($cantidadEnCarrito > 0): ?>
                    <p class=" seccion_parrafo">
                        Tienes <?php echo $cantidadEnCarrito; ?> de este producto en tu carrito.
                    </p>
                <?php endif; ?>
            </div>
            <hr>
            <div class="producto_detalle_info">
                <div class="producto_lista">
                    <article class="producto_detalle_parte ingrediente_cont">
                        <span class="producto_detalle_nombre">
                            Ingredientes:
                        </span>
                        <ul>
                            <?php
                                $ingredientes = explode(',', $producto['ingredientes']);
                                foreach ($ingredientes as $ingrediente) {
                                    echo "<li>" . htmlspecialchars(trim($ingrediente)) . "</li>";
                                }
                            ?>
                        </ul>
                    </article>
                    <article class="producto_detalle_parte">
                        <span class="producto_detalle_precio">
                            S/. <?php echo number_format($producto['precio'], 2); ?>
                        </span>
                        <div>
                            <div class="botones_cantidad">
                                <button class="cantidad_btn" id="btnMenos">
                                    <i class="fa-solid fa-minus"></i>
                                </button>
                                <span class="cantidad_numero" id="cantidad">
                                    1
                                </span>
                                <button class="cantidad_btn" id="btnMas">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="">
                            <button class="boton_seccion ancho_completo" onclick="agregarAlCarrito(<?php echo $producto['id_producto']; ?>)">Añadir al carrito</button>

                            

                        </div>
                    </article>
                </div>
            </div>
        </section>
        <div class="producto_acciones">
            <a class="boton_seccion danger" href="../carrito/carrito.php">Ver carrito</a>
            <a href="index.php" class="boton_seccion second">Volver al menú</a>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const linksConTransicion = document.querySelectorAll(".boton_seccion");

            linksConTransicion.forEach(link => {
                link.addEventListener("click", (e) => {
                    e.preventDefault(); // Evitamos que navegue de inmediato

                    const urlDestino = link.getAttribute("href") || link.getAttribute("onclick")?.match(/'([^']+)'/)?.[1];

                    if (!urlDestino) return window.location.href = "#";

                    // Iniciamos la transición
                    if (document.startViewTransition) {
                        document.startViewTransition(() => {
                            window.location.href = urlDestino;
                        });
                    } else {
                        window.location.href = urlDestino; // fallback
                    }
                });
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            const btnMenos = document.getElementById("btnMenos");
            const btnMas = document.getElementById("btnMas");
            const cantidadElemento = document.getElementById("cantidad");

            btnMenos.addEventListener("click", function () {
                let cantidad = parseInt(cantidadElemento.textContent);
                if (cantidad > 1) {
                    cantidadElemento.textContent = cantidad - 1;
                }
            });

            btnMas.addEventListener("click", function () {
                let cantidad = parseInt(cantidadElemento.textContent);
                cantidadElemento.textContent = cantidad + 1;
            });
        });
    </script>
</section>
    <?php
        include( '../archives/destacados.php' );
    ?>

<script>
    function agregarAlCarrito(idProducto) {
        const cantidad = parseInt(document.getElementById("cantidad").textContent);

        fetch("agregar_carrito.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id_producto=${idProducto}&cantidad=${cantidad}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                Swal.fire({
                    icon: 'success',
                    title: 'Producto agregado',
                    text: 'El producto se ha añadido al carrito con éxito.',
                    showCancelButton: true,
                    confirmButtonText: 'Ir al carrito',
                    cancelButtonText: 'Seguir comprando',
                    allowOutsideClick: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '../carrito/carrito.php';
                    }
                });
            } else {
                if (data.status === "error" && data.message === "Debes iniciar sesión.") {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No has iniciado sesión',
                        text: 'Debes iniciar sesión para agregar productos al carrito.',
                        confirmButtonText: 'Iniciar sesión'
                    }).then(() => {
                        window.location.href = '../panel/login.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message,
                        confirmButtonText: 'Aceptar'
                    });
                }
            }
        })
        .catch(error => console.error("Error:", error));
    }
</script>

<?php include('../archives/footer.php'); ?>
