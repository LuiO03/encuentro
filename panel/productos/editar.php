<?php 
include("../bd.php");

$errores = [];

if ($_POST) {
    $txtID = isset($_POST["txtID"]) ? $_POST["txtID"] : "";
    $nombre = isset($_POST["nombre_producto"]) ? trim($_POST["nombre_producto"]) : "";
    $descripcion = isset($_POST["descripcion"]) ? trim($_POST["descripcion"]) : "";
    $precio = isset($_POST["precio"]) ? $_POST["precio"] : "";
    $ingredientes = isset($_POST["ingredientes"]) ? trim($_POST["ingredientes"]) : "";
    $disponible = isset($_POST["disponible"]) ? 1 : 0;
    $destacado = isset($_POST["destacado"]) ? 1 : 0;
    $id_categoria = isset($_POST["id_categoria"]) ? $_POST["id_categoria"] : NULL;

    // Validaciones
    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,50}$/", $nombre)) {
        $errores[] = "El nombre del producto debe tener entre 3 y 50 caracteres y solo contener letras y espacios.";
    }
    if (strlen($descripcion) < 10 || strlen($descripcion) > 255) {
        $errores[] = "La descripción debe tener entre 10 y 255 caracteres.";
    }
    if (!is_numeric($precio) || $precio <= 0) {
        $errores[] = "El precio debe ser un número positivo.";
    }
    if (empty($ingredientes)) {
        $errores[] = "Debe ingresar los ingredientes del producto.";
    }

    // Obtener imagen anterior
    $sentencia = $conect->prepare("SELECT imagen FROM productos WHERE id_producto = :id");
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();
    $registro_imagen = $sentencia->fetch(PDO::FETCH_ASSOC);
    $imagen_anterior = $registro_imagen["imagen"];

    if (empty($errores)) {
        // Actualizar datos
        $sentencia = $conect->prepare("UPDATE productos SET nombre_producto = :nombre, descripcion = :descripcion, precio = :precio, ingredientes = :ingredientes, disponible = :disponible, destacado = :destacado, id_categoria = :id_categoria WHERE id_producto = :id");
        $sentencia->bindParam(":nombre", $nombre);
        $sentencia->bindParam(":descripcion", $descripcion);
        $sentencia->bindParam(":precio", $precio);
        $sentencia->bindParam(":ingredientes", $ingredientes);
        $sentencia->bindParam(":disponible", $disponible);
        $sentencia->bindParam(":destacado", $destacado);
        $sentencia->bindParam(":id_categoria", $id_categoria);
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();

        // Manejo de imagen
        if (!empty($_FILES['imagen']['name'])) {
            $imagen = $_FILES['imagen']['name'];
            $tmp_imagen = $_FILES['imagen']['tmp_name'];
            $permitidos = ["image/jpeg", "image/png", "image/gif"];
            $tamano_maximo = 2 * 1024 * 1024; // 2MB

            if (!in_array($_FILES["imagen"]["type"], $permitidos)) {
                $errores[] = "El formato de la imagen debe ser JPG, PNG o GIF.";
            } elseif ($_FILES["imagen"]["size"] > $tamano_maximo) {
                $errores[] = "El tamaño de la imagen no debe superar los 2MB.";
            } else {
                $fecha_imagen = new DateTime();
                $nombre_imagen = $fecha_imagen->getTimestamp() . "_" . $imagen;

                if (move_uploaded_file($tmp_imagen, "../../img/productos/" . $nombre_imagen)) {
                    if (!empty($imagen_anterior) && file_exists("../../img/productos/" . $imagen_anterior)) {
                        unlink("../../img/productos/" . $imagen_anterior);
                    }
                    $sentencia = $conect->prepare("UPDATE productos SET imagen = :imagen WHERE id_producto = :id");
                    $sentencia->bindParam(":imagen", $nombre_imagen);
                    $sentencia->bindParam(":id", $txtID);
                    $sentencia->execute();
                }
            }
        }

        header("Location: index.php");
        exit();
    }
}

if (isset($_GET['txtID'])) {
    $txtID = $_GET["txtID"];
    $sentencia = $conect->prepare("SELECT * FROM productos WHERE id_producto = :id");
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();
    $registro = $sentencia->fetch(PDO::FETCH_ASSOC);
}

$sentencia_categorias = $conect->prepare("SELECT * FROM categorias");
$sentencia_categorias->execute();
$categorias = $sentencia_categorias->fetchAll(PDO::FETCH_ASSOC);

include("../recursos/header.php"); 
?>

<div class="card bg-dark text-light py-3 px-4">
    <div class="card-header text-center">
        <span class="seccion_titulo">Editar Producto</span>
    </div>
    <div class="card-body">

    <?php if (!empty($errores)) { ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errores as $error) { echo "<li>$error</li>"; } ?>
            </ul>
        </div>
    <?php } ?>

        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="txtID" value="<?php echo $txtID; ?>">

            <div class="row">
                <!-- Columna izquierda: inputs -->
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="nombre_producto" class="form-label">Nombre del Producto:</label>
                        <input type="text" class="form-control bg-dark text-light" value="<?php echo $registro['nombre_producto']; ?>" name="nombre_producto" required>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción:</label>
                        <textarea class="form-control bg-dark text-light" name="descripcion" required><?php echo $registro['descripcion']; ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="ingredientes" class="form-label">Ingredientes:</label>
                        <textarea class="form-control bg-dark text-light" name="ingredientes" required><?php echo $registro['ingredientes']; ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="id_categoria" class="form-label">Categoría:</label>
                        <select class="form-control bg-dark text-light" name="id_categoria" required>
                            <option value="">Seleccione una categoría</option>
                            <?php foreach ($categorias as $categoria) { ?>
                                <option value="<?php echo $categoria['id_categoria']; ?>" <?php echo ($categoria['id_categoria'] == $registro['id_categoria']) ? 'selected' : ''; ?>>
                                    <?php echo $categoria['nombre_categoria']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="precio" class="form-label">Precio:</label>
                        <input type="number" step="0.01" min="0" class="form-control bg-dark text-light" name="precio" value="<?php echo $registro['precio']; ?>" required>
                    </div>
                </div>

                <!-- Columna derecha: imagen -->
                <div class="col-lg-6">
                    <div class=" mb-3">
                        <label class="form-label">Subir nueva imagen:</label>
                        <input type="file" class="form-control bg-dark text-light mt-2" name="imagen">
                    </div>
                    <img class="mb-3" width="100%" src="../../img/platos/<?php echo $registro['imagen']; ?>" alt="Imagen actual" class="border rounded" >

                    <div class="row">
                        <div class="col-6">
                            <div class="form-check form-switch mb-3">
                                <label class="form-check-label text-light" for="disponible">¿Producto disponible?</label>
                                <input class="form-check-input" type="checkbox" role="switch" name="disponible" id="disponible" <?php echo $registro['disponible'] ? 'checked' : ''; ?>>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-check form-switch mb-3">
                                <label class="form-check-label text-light" for="destacado">¿Producto destacado?</label>
                                <input class="form-check-input" type="checkbox" role="switch" name="destacado" id="destacado" <?php echo $registro['destacado'] ? 'checked' : ''; ?>>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-success">Modificar Producto</button>
                <a href="index.php" class="btn btn-outline-light">Cancelar</a>
            </div>
        </form>
    </div>
</div>


<?php include("../recursos/footer.php"); ?>
