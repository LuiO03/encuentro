<?php
include("../panel/bd.php");

$id_categoria = isset($_GET['id_categoria']) ? $_GET['id_categoria'] : '';
$orden = isset($_GET['orden']) ? $_GET['orden'] : '';
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

// Consulta base
$sql = "SELECT productos.*, categorias.nombre_categoria 
        FROM productos 
        INNER JOIN categorias ON productos.id_categoria = categorias.id_categoria";

$condiciones = [];
if ($id_categoria) {
    $condiciones[] = "categorias.id_categoria = :id_categoria";
}
if ($buscar) {
    $condiciones[] = "productos.nombre_producto LIKE :buscar";
}

if (!empty($condiciones)) {
    $sql .= " WHERE " . implode(" AND ", $condiciones);
}

// Ordenamiento
switch ($orden) {
    case 'barato':
        $sql .= " ORDER BY productos.precio ASC";
        break;
    case 'nuevo':
        $sql .= " ORDER BY productos.id_producto DESC";
        break;
    case 'destacado':
        $sql .= " ORDER BY productos.destacado DESC, productos.id_producto DESC";
        break;
    default:
        $sql .= " ORDER BY categorias.nombre_categoria ASC, productos.id_producto ASC";
        break;
}

$sentencia = $conect->prepare($sql);

if ($id_categoria) {
    $sentencia->bindParam(':id_categoria', $id_categoria);
}
if ($buscar) {
    $buscar_param = "%$buscar%";
    $sentencia->bindParam(':buscar', $buscar_param);
}

$sentencia->execute();
$lista_productos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

$productos_por_categoria = [];
foreach ($lista_productos as $producto) {
    $categoria = $producto['nombre_categoria'];
    if (!isset($productos_por_categoria[$categoria])) {
        $productos_por_categoria[$categoria] = [];
    }
    $productos_por_categoria[$categoria][] = $producto;
}

// HTML
if (empty($lista_productos)) {
    echo '<p class="seccion_parrafo">No se encontraron productos.</p>';
}

foreach ($productos_por_categoria as $categoria => $productos) {
    echo '<h2 class="titulo_categoria">' .htmlspecialchars($categoria) . '</h2>';
    echo '<div class="menu_seccion">';
    foreach ($productos as $producto) {
        echo '<a class="plato" href="detalle_plato.php?id=' . htmlspecialchars($producto['id_producto']) . '">';
            echo '<div class="plato_cont"><img src="../img/platos/' . htmlspecialchars($producto['imagen']) . '" alt="' . htmlspecialchars($producto['nombre_producto']) . '"></div>';
            echo '<div class="plato_info">';
            echo '<span class="categoria">' . htmlspecialchars($producto['nombre_categoria']) . '</span>';
            echo '<h3 class="nombre_plato">' . htmlspecialchars($producto['nombre_producto']) . '</h3>';
            echo '<p class="seccion_parrafo">' . htmlspecialchars($producto['descripcion']) . '</p>';
            echo '<div class="plato_titulo"><span class="precio">S/. ' . number_format($producto['precio'], 2) . '</span></div>';
        echo '</div></a>';
    }
    echo '</div>';
}
?>
