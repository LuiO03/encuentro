<?php 
include("../bd.php");

$errores = [];

if ($_POST) {
    $nombre = isset($_POST["nombre"]) ? trim($_POST["nombre"]) : "";
    $apellido = isset($_POST["apellido"]) ? trim($_POST["apellido"]) : "";
    $dni = isset($_POST["dni"]) ? trim($_POST["dni"]) : "";
    $correo = isset($_POST["correo"]) ? trim($_POST["correo"]) : "";
    $telefono = isset($_POST["telefono"]) ? trim($_POST["telefono"]) : "";
    $direccion = isset($_POST["direccion"]) ? trim($_POST["direccion"]) : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";
    $confirmar_password = isset($_POST["confirmar_password"]) ? $_POST["confirmar_password"] : "";
    $roles_permitidos = ["Administrador", "Empleado", "Cliente"];
    $rol = isset($_POST["rol"]) && in_array($_POST["rol"], $roles_permitidos) ? $_POST["rol"] : "Empleado";

    // Validaciones
    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,100}$/", $nombre)) {
        $errores[] = "El nombre debe tener entre 3 y 100 caracteres y solo contener letras.";
    }
    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,100}$/", $apellido)) {
        $errores[] = "El apellido debe tener entre 3 y 100 caracteres y solo contener letras.";
    }
    if (!preg_match("/^\d{8}$/", $dni)) {
        $errores[] = "El DNI debe contener exactamente 8 dígitos numéricos.";
    }
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Ingrese un correo válido.";
    }
    if (!preg_match("/^\d{9}$/", $telefono)) {
        $errores[] = "El teléfono debe contener 9 dígitos numéricos.";
    }
    if (strlen($direccion) < 5 || strlen($direccion) > 255) {
        $errores[] = "La dirección debe tener entre 5 y 255 caracteres.";
    }
    if (strlen($password) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres.";
    }
    if ($password !== $confirmar_password) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    // Validar si el correo o el DNI ya están registrados
    $stmt = $conect->prepare("SELECT id_usuario FROM usuarios WHERE correo = :correo OR dni = :dni");
    $stmt->bindParam(":correo", $correo);
    $stmt->bindParam(":dni", $dni);
    $stmt->execute();
    if ($stmt->fetch()) {
        $errores[] = "El correo o el DNI ya están registrados.";
    }

    // Validar la imagen (obligatoria)
    if (empty($_FILES['foto_perfil']['name'])) {
        $errores[] = "Debe subir una imagen de perfil.";
    } else {
        $imagen = $_FILES['foto_perfil']['name'];
        $permitidos = ["image/jpeg", "image/png", "image/gif"];
        $tamano_maximo = 3 * 1024 * 1024; // 3MB

        if (!in_array($_FILES["foto_perfil"]["type"], $permitidos)) {
            $errores[] = "El formato de la imagen debe ser JPG, PNG o GIF.";
        } elseif ($_FILES["foto_perfil"]["size"] > $tamano_maximo) {
            $errores[] = "El tamaño de la imagen no debe superar los 3MB.";
        }
    }

    if (empty($errores)) {
        // Guardar la imagen
        $fecha_imagen = new DateTime();
        $nombre_imagen = $fecha_imagen->getTimestamp() . "_" . $imagen;
        move_uploaded_file($_FILES["foto_perfil"]["tmp_name"], "../../img/usuarios/" . $nombre_imagen);

        // Hash de contraseña
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sentencia = $conect->prepare("INSERT INTO usuarios (nombre, apellido, dni, correo, telefono, direccion, password, foto_perfil, rol, creado_en) 
                                        VALUES (:nombre, :apellido, :dni, :correo, :telefono, :direccion, :password, :foto_perfil, :rol, NOW());");
        $sentencia->bindParam(":nombre", $nombre);
        $sentencia->bindParam(":apellido", $apellido);
        $sentencia->bindParam(":dni", $dni);
        $sentencia->bindParam(":correo", $correo);
        $sentencia->bindParam(":telefono", $telefono);
        $sentencia->bindParam(":direccion", $direccion);
        $sentencia->bindParam(":password", $password_hash);
        $sentencia->bindParam(":foto_perfil", $nombre_imagen);
        $sentencia->bindParam(":rol", $rol);
        $sentencia->execute();
        
        header("Location: index.php");
        exit;
    }
}

include("../recursos/header.php");
?>

<div class="card bg-dark text-light py-3 px-4">
    <div class="card-header text-center">
        <h2 class="seccion_titulo">Registrar Usuario</h2>
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
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nombre" class="form-label">Nombre:</label>
                    <input type="text" class="form-control bg-dark text-light border-secondary" name="nombre" required minlength="3" maxlength="100" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+" title="Solo letras y espacios, mínimo 3 caracteres.">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="apellido" class="form-label">Apellido:</label>
                    <input type="text" class="form-control bg-dark text-light border-secondary" name="apellido" required minlength="3" maxlength="100" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+" title="Solo letras y espacios, mínimo 3 caracteres.">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="dni" class="form-label">DNI:</label>
                    <input type="text" class="form-control bg-dark text-light border-secondary" name="dni" required pattern="\d{8}" title="Debe contener exactamente 8 dígitos numéricos.">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="correo" class="form-label">Correo:</label>
                    <input type="email" class="form-control bg-dark text-light border-secondary" name="correo" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="telefono" class="form-label">Teléfono:</label>
                    <input type="text" class="form-control bg-dark text-light border-secondary" name="telefono" required pattern="\d{9}" title="Debe contener 9 dígitos numéricos.">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="direccion" class="form-label">Dirección:</label>
                    <input type="text" class="form-control bg-dark text-light border-secondary" name="direccion" required minlength="5" maxlength="255">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="rol" class="form-label">Rol:</label>
                    <select class="form-control bg-dark text-light border-secondary" name="rol" required>
                        <option value="Administrador" selected>Administrador</option>
                        <option value="Empleado">Empleado</option>
                        <option value="Cliente">Cliente</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Contraseña:</label>
                    <input type="password" class="form-control bg-dark text-light border-secondary" name="password" required minlength="6">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="confirmar_password" class="form-label">Confirmar Contraseña:</label>
                    <input type="password" class="form-control bg-dark text-light border-secondary" name="confirmar_password" required minlength="6">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="foto_perfil" class="form-label">Foto de Perfil:</label>
                    <input type="file" class="form-control bg-dark text-light border-secondary" name="foto_perfil" accept=".jpg, .jpeg, .png, .gif" required>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success">Registrar Usuario</button>
                <a href="index.php" class="btn btn-outline-light">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include("../recursos/footer.php"); ?>
