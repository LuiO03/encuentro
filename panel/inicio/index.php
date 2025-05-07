<?php
include("../bd.php");

// Función para contar registros
function contarRegistros($tabla, $conexion, $condicion = null) {
    try {
        $sql = "SELECT COUNT(*) FROM $tabla";
        if ($condicion) {
            $sql .= " WHERE $condicion";
        }
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        return "0";
    }
}

include("../recursos/header.php");
?>

<!-- Estilos adicionales -->
<style>
    .card-custom {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 1rem;
        cursor: pointer;
    }
    .card-custom:hover {
        transform: scale(1.03);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }
    .icono {
        font-size: 2.5rem;
        color: #0d6efd;
    }
</style>

<div class="container py-3 px-4">
    <div class="card bg-dark text-white py-2 px-3 mb-4 rounded-4">
        <div class="card-header border-0 bg-dark">
            <h2 class="seccion_titulo">Bienvenido</h2>
            <p class="seccion_parrafo">Administra los módulos del restaurante Encuentro desde este panel.</p>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-4 borde_contenido py-1 px-2 m-2">
        <?php
        $modulos = [
            ['nombre' => 'Categorías', 'icono' => 'fa-solid fa-tags', 'link' => '../../categorias/index.php', 'tabla' => 'categorias'],

            ['nombre' => 'Productos', 'icono' => 'fa-solid fa-utensils', 'link' => '../productos/index.php', 'tabla' => 'productos'],

            ['nombre' => 'Banners', 'icono' => 'fa-solid fa-image', 'link' => '../banners/index.php', 'tabla' => 'banners'],

            ['nombre' => 'Mensajes', 'icono' => 'fa-solid fa-envelope', 'link' => '../mensajes/index.php', 'tabla' => 'mensajes'],

            ['nombre' => 'Usuarios', 'icono' => 'fa-solid fa-users', 'link' => '../usuarios/index.php', 'tabla' => 'usuarios', 'condicion' => "rol != 'cliente'"],

            ['nombre' => 'Clientes', 'icono' => 'fa-solid fa-user', 'link' => '../clientes/index.php', 'tabla' => 'usuarios', 'condicion' => "rol = 'cliente'"],


            ['nombre' => 'Reservas', 'icono' => 'fa-solid fa-calendar-check', 'link' => '../reservas/index.php', 'tabla' => 'reservas'],

            ['nombre' => 'Mesas', 'icono' => 'fa-solid fa-chair', 'link' => '../reservas/index.php', 'tabla' => 'mesas'],

            ['nombre' => 'Restaurante', 'icono' => 'fa-solid fa-store', 'link' => '../restaurant/index.php', 'tabla' => 'restaurante'],

            ['nombre' => 'Pedidos', 'icono' => 'fa-solid fa-receipt', 'link' => '../pedidos/index.php', 'tabla' => 'carrito']
        ];

        foreach ($modulos as $modulo): 
            $cond = isset($modulo['condicion']) ? $modulo['condicion'] : null;
            $cantidad = contarRegistros($modulo['tabla'], $conect, $cond);
        ?>
        <div class="col">
            <a href="<?= $modulo['link'] ?>" class="text-decoration-none">
                <div class="card h-100 border-0 card-custom bg-light">
                    <div class="card-body text-center">
                        <i class="<?= $modulo['icono'] ?> icono mb-2"></i>
                        <h5 class="card-title text-dark"><?= $modulo['nombre'] ?></h5>
                        <p class="card-text text-muted"><?= $cantidad ?> registro<?= $cantidad != 1 ? 's' : '' ?></p>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include("../recursos/footer.php"); ?>
