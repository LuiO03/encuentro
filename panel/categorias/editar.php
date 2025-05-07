<?php 
include("../bd.php");

$errores = [];

if ($_POST) {
    $txtID = isset($_POST["txtID"]) ? $_POST["txtID"] : "";
    $nombre = isset($_POST["nombre_categoria"]) ? trim($_POST["nombre_categoria"]) : "";
    $descripcion = isset($_POST["descripcion"]) ? trim($_POST["descripcion"]) : "";

    // Validar el nombre de la categoría
    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,50}$/", $nombre)) {
        $errores[] = "El nombre de la categoría debe tener entre 3 y 50 caracteres y solo contener letras y espacios.";
    }

    // Validar la descripción
    if (strlen($descripcion) < 10 || strlen($descripcion) > 255) {
        $errores[] = "La descripción debe tener entre 10 y 255 caracteres.";
    }

    // Obtener imagen anterior
    $sentencia = $conect->prepare("SELECT imagen FROM categorias WHERE id_categoria = :id");
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();
    $registro_imagen = $sentencia->fetch(PDO::FETCH_ASSOC);
    $imagen_anterior = $registro_imagen["imagen"];

    if (empty($errores)) {
        // Actualizar nombre y descripción
        $sentencia = $conect->prepare("UPDATE categorias SET nombre_categoria = :nombre, descripcion = :descripcion WHERE id_categoria = :id");
        $sentencia->bindParam(":nombre", $nombre);
        $sentencia->bindParam(":descripcion", $descripcion);
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();

        // Validar imagen si se ha subido
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

                if (move_uploaded_file($tmp_imagen, "../../img/categorias/" . $nombre_imagen)) {
                    if (!empty($imagen_anterior) && file_exists("../../img/categorias/" . $imagen_anterior)) {
                        unlink("../../img/categorias/" . $imagen_anterior);
                    }
                    $sentencia = $conect->prepare("UPDATE categorias SET imagen = :imagen WHERE id_categoria = :id");
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
    $sentencia = $conect->prepare("SELECT * FROM categorias WHERE id_categoria = :id");
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();
    $registro = $sentencia->fetch(PDO::FETCH_ASSOC);

    $nombre = $registro["nombre_categoria"];
    $descripcion = $registro["descripcion"];
    $imagen = $registro["imagen"];
}

include("../recursos/header.php"); 
?>

<div class="card bg-dark text-light py-3 px-4">
    <div class="card-header text-center">
        <span class="seccion_titulo">Editar Categoría</span>
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

        <div class="row mb-3"">

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="nombre_categoria" class="form-label">Nombre de la Categoría:</label>
                    <input type="text" class="form-control bg-dark text-light" value="<?php echo $nombre; ?>" 
                        name="nombre_categoria" id="nombre_categoria" required 
                        pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,50}" 
                        title="Solo letras y espacios, entre 3 y 50 caracteres">
                </div>
    
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción:</label>
                    <textarea class="form-control bg-dark text-light" name="descripcion" id="descripcion" 
                            required minlength="10" maxlength="255" rows="3"><?php echo $descripcion; ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="imagen" class="form-label">Subir Nueva Imagen:</label>
                    <input type="file" class="form-control bg-dark text-light" name="imagen" id="imagen" 
                        accept=".jpg, .jpeg, .png, .gif">
                </div>
            </div>
    
            <div class="col-md-6">
                <div >
                    <label class="form-label">Imagen Actual:</label><br>
                    <img width="100%"  src="../../img/categorias/<?php echo $imagen; ?>" alt="Imagen actual" class="border rounded img-fluid">
                </div>
            </div>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-success">Modificar Categoría</button>
            <a href="index.php" class="btn btn-outline-light">Cancelar</a>
        </div>
    </form>

    </div>
</div>

<?php include("../recursos/footer.php"); ?>
