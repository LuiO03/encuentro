<?php 
include("../bd.php");

$errores = [];

// Obtener las categorías disponibles
$query_categorias = $conect->prepare("SELECT id_categoria, nombre_categoria FROM categorias");
$query_categorias->execute();
$categorias = $query_categorias->fetchAll(PDO::FETCH_ASSOC);

if ($_POST) {
    $nombre_producto = isset($_POST["nombre_producto"]) ? trim($_POST["nombre_producto"]) : "";
    $descripcion = isset($_POST["descripcion"]) ? trim($_POST["descripcion"]) : "";
    $precio = isset($_POST["precio"]) ? floatval($_POST["precio"]) : 0;
    $ingredientes = isset($_POST["ingredientes"]) ? trim($_POST["ingredientes"]) : "";
    $id_categoria = isset($_POST["id_categoria"]) ? intval($_POST["id_categoria"]) : 0;

    // Validaciones
    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s]{3,50}$/", $nombre_producto)) {
        $errores[] = "El nombre del producto debe tener entre 3 y 50 caracteres y solo contener letras, números y espacios.";
    }
    if (strlen($descripcion) < 10 || strlen($descripcion) > 255) {
        $errores[] = "La descripción debe tener entre 10 y 255 caracteres.";
    }
    if (strlen($ingredientes) < 5 || strlen($ingredientes) > 255) {
        $errores[] = "Los ingredientes deben tener entre 5 y 255 caracteres.";
    }
    if ($precio <= 0) {
        $errores[] = "El precio debe ser un valor positivo.";
    }

    // Validar la categoría seleccionada
    $categoria_valida = false;
    foreach ($categorias as $categoria) {
        if ($categoria['id_categoria'] == $id_categoria) {
            $categoria_valida = true;
            break;
        }
    }
    if (!$categoria_valida) {
        $errores[] = "Debe seleccionar una categoría válida.";
    }

    // Validar la imagen
    $imagen = isset($_FILES['imagen']["name"]) ? $_FILES['imagen']["name"] : "";
    $nombre_imagen = "";
    if ($imagen != "") {
        $permitidos = ["image/jpeg", "image/png", "image/gif"];
        $tamano_maximo = 2 * 1024 * 1024; // 2MB

        if (!in_array($_FILES["imagen"]["type"], $permitidos)) {
            $errores[] = "El formato de la imagen debe ser JPG, PNG o GIF.";
        } elseif ($_FILES["imagen"]["size"] > $tamano_maximo) {
            $errores[] = "El tamaño de la imagen no debe superar los 2MB.";
        } else {
            $fecha_imagen = new DateTime();
            $nombre_imagen = $fecha_imagen->getTimestamp() . "_" . $imagen;
            move_uploaded_file($_FILES["imagen"]["tmp_name"], "../../img/platos/" . $nombre_imagen);
        }
    }

    // Si no hay errores, guardar en la base de datos
    if (empty($errores)) {
        $sentencia = $conect->prepare("INSERT INTO productos 
            (nombre_producto, imagen, descripcion, precio, ingredientes, id_categoria) 
            VALUES (:nombre_producto, :imagen, :descripcion, :precio, :ingredientes, :id_categoria)");
        
        $sentencia->bindParam(":imagen", $nombre_imagen);
        $sentencia->bindParam(":nombre_producto", $nombre_producto);
        $sentencia->bindParam(":descripcion", $descripcion);
        $sentencia->bindParam(":precio", $precio);
        $sentencia->bindParam(":ingredientes", $ingredientes);
        $sentencia->bindParam(":id_categoria", $id_categoria);
        
        $sentencia->execute();
        header("Location: index.php");
    }
}

include("../recursos/header.php"); 
?>

<div class="card bg-dark text-light py-3 px-4">
    <div class="card-header text-center">
        <span class="seccion_titulo">Agregar Producto</span>
    </div>
    <div class="card-body">

    <?php if (!empty($errores)) { ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errores as $error) { echo "<li>$error</li>"; } ?>
            </ul>
        </div>
    <?php } ?>

    <form action="" method="post" class="col-12" enctype="multipart/form-data">
        <div class="row">
            <div class="mb-3 col-lg-6">
            <label for="nombre_producto" class="form-label">Nombre del Producto:</label>
            <input type="text" class="form-control bg-dark text-light" name="nombre_producto" id="nombre_producto" required 
                    minlength="3" maxlength="50" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s]{3,50}" 
                    title="Solo letras, números y espacios, entre 3 y 50 caracteres">
            </div>

            <div class="mb-3 col-lg-6">
            <label for="descripcion" class="form-label">Descripción:</label>
            <textarea class="form-control bg-dark text-light" name="descripcion" id="descripcion" required 
                        minlength="10" maxlength="255"></textarea>
            </div>

            <div class="mb-3 col-lg-6">
            <label for="ingredientes" class="form-label">Ingredientes:</label>
            <textarea class="form-control bg-dark text-light" name="ingredientes" id="ingredientes" required 
                        minlength="5" maxlength="255"></textarea>
            </div>

            <div class="mb-3 col-lg-6">
            <label for="precio" class="form-label">Precio:</label>
            <input type="number" class="form-control bg-dark text-light" name="precio" id="precio" required 
                    step="0.01" min="0.01">
            </div>

            <div class="mb-3 col-lg-6">
            <label for="id_categoria" class="form-label">Categoría:</label>
            <select class="form-control bg-dark text-light cursor-pointer" name="id_categoria" id="id_categoria" required>
                <option value="">Seleccione una categoría</option>
                <?php foreach ($categorias as $categoria) { ?>
                    <option value="<?= $categoria['id_categoria']; ?>"><?= htmlspecialchars($categoria['nombre_categoria']); ?></option>
                <?php } ?>
            </select>
            </div>

            <div class="mb-3 col-lg-6">
            <label for="imagen" class="form-label">Imagen:</label>
            <input type="file" class="form-control bg-dark text-light" name="imagen" id="imagen" accept=".jpg, .jpeg, .png, .gif" required>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success">Agregar Producto</button>
                <a class="btn btn-outline-light" href="index.php" role="button">Cancelar</a>
            </div>
        </div>
    </form>

    </div>
</div>

<?php include("../recursos/footer.php"); ?>
