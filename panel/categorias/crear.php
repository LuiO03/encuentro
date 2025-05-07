<?php 
include("../bd.php");

$errores = [];

if ($_POST) {
    $nombre_categoria = isset($_POST["nombre_categoria"]) ? trim($_POST["nombre_categoria"]) : "";
    $descripcion = isset($_POST["descripcion"]) ? trim($_POST["descripcion"]) : "";

    // Validar el nombre de la categoría
    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,50}$/", $nombre_categoria)) {
        $errores[] = "El nombre de la categoría debe tener entre 3 y 50 caracteres y solo contener letras y espacios.";
    }

    // Validar la descripción
    if (strlen($descripcion) < 10 || strlen($descripcion) > 255) {
        $errores[] = "La descripción debe tener entre 10 y 255 caracteres.";
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
            move_uploaded_file($_FILES["imagen"]["tmp_name"], "../../img/categorias/" . $nombre_imagen);
        }
    }

    // Si no hay errores, guardar en la base de datos
    if (empty($errores)) {
      $sentencia = $conect->prepare("INSERT INTO `categorias` (`id_categoria`, `nombre_categoria`, `imagen`, `descripcion`) 
                                      VALUES (NULL, :nombre_categoria, :imagen, :descripcion);");
      $sentencia->bindParam(":imagen", $nombre_imagen);
      $sentencia->bindParam(":nombre_categoria", $nombre_categoria);
      $sentencia->bindParam(":descripcion", $descripcion);
      $sentencia->execute();
      header("Location: index.php");
    }
}

include ("../recursos/header.php"); 
?>

<div class="card bg-dark text-light py-3 px-4">
    <div class="card-header text-center">
        <span class="seccion_titulo">Agregar Categoría</span>
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

        <div class="mb-3">
          <label for="nombre_categoria" class="form-label">Nombre de la Categoría:</label>
          <input type="text" class="form-control bg-dark text-light" name="nombre_categoria" id="nombre_categoria" minlength="3" maxlength="25"
                 placeholder="Nombre de la categoría" required pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,50}" 
                 title="Solo letras y espacios, entre 3 y 25 caracteres">
        </div>

        <div class="mb-3">
          <label for="descripcion" class="form-label">Descripción:</label>
          <textarea class="form-control bg-dark text-light" name="descripcion" id="descripcion" rows="3" 
                    placeholder="Descripción de la categoría" required minlength="10" maxlength="255"></textarea>
        </div>

        <div class="mb-3">
          <label for="imagen" class="form-label">Imagen:</label>
          <input type="file" class="form-control bg-dark text-light" name="imagen" id="imagen" 
                 accept=".jpg, .jpeg, .png, .gif" required>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-success">Agregar Categoría</button>
            <a class="btn btn-outline-light" href="index.php" role="button">Cancelar</a>
        </div>

    </form>

    </div>
</div>

<?php include ("../recursos/footer.php"); ?>
