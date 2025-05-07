<?php 
include("../bd.php");

$errores = [];

if ($_POST) {
    $txtID = isset($_POST["txtID"]) ? $_POST["txtID"] : "";
    $titulo = isset($_POST["titulo"]) ? trim($_POST["titulo"]) : "";
    $descripcion = isset($_POST["descripcion"]) ? trim($_POST["descripcion"]) : "";

    // Validaciones
    if (strlen($titulo) < 3 || strlen($titulo) > 50) {
        $errores[] = "El título debe tener entre 3 y 50 caracteres.";
    }
    if (strlen($descripcion) < 10 || strlen($descripcion) > 255) {
        $errores[] = "La descripción debe tener entre 10 y 255 caracteres.";
    }

    // Obtener imagen anterior
    $sentencia = $conect->prepare("SELECT imagen FROM banners WHERE id_banner = :id");
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();
    $registro_imagen = $sentencia->fetch(PDO::FETCH_ASSOC);
    $imagen_anterior = $registro_imagen["imagen"];

    if (empty($errores)) {
        // Actualizar datos
        $sentencia = $conect->prepare("UPDATE banners SET titulo = :titulo, descripcion = :descripcion WHERE id_banner = :id");
        $sentencia->bindParam(":titulo", $titulo);
        $sentencia->bindParam(":descripcion", $descripcion);
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

                if (move_uploaded_file($tmp_imagen, "../../img/banners/" . $nombre_imagen)) {
                    if (!empty($imagen_anterior) && file_exists("../../img/banners/" . $imagen_anterior)) {
                        unlink("../../img/banners/" . $imagen_anterior);
                    }
                    $sentencia = $conect->prepare("UPDATE banners SET imagen = :imagen WHERE id_banner = :id");
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
    $sentencia = $conect->prepare("SELECT * FROM banners WHERE id_banner = :id");
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();
    $registro = $sentencia->fetch(PDO::FETCH_ASSOC);
}

include("../recursos/header.php"); 
?>

<div class="card bg-dark text-light py-3 px-4">
    <div class="card-header text-center">
        <span class="seccion_titulo">Editar Banner</span>
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

            <div class="mb-3">
                <label for="titulo" class="form-label">Título:</label>
                <input type="text" class="form-control bg-secondary text-light" value="<?php echo $registro['titulo']; ?>" name="titulo" required>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción:</label>
                <textarea class="form-control bg-secondary text-light" name="descripcion" required><?php echo $registro['descripcion']; ?></textarea>
            </div>

            <div class="mb-3">
                <label for="imagen" class="form-label">Imagen:</label>
                <br/>
                <img width="100" src="../../img/banners/<?php echo $registro['imagen']; ?>" alt="Imagen actual" class="border rounded">
                <input type="file" class="form-control bg-secondary text-light mt-2" name="imagen">
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success">Modificar Banner</button>
                <a href="index.php" class="btn btn-outline-light">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include("../recursos/footer.php"); ?>