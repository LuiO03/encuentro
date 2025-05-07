<?php 
include("../bd.php");

$errores = [];

if ($_POST) {
    $txtID = $_POST["txtID"];
    $nombre = trim($_POST["nombre"]);
    $apellido = trim($_POST["apellido"]);
    $correo = trim($_POST["correo"]);
    $telefono = trim($_POST["telefono"]);
    $direccion = trim($_POST["direccion"]);
    $rol = $_POST["rol"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Validaciones
    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,100}$/", $nombre)) {
        $errores[] = "El nombre debe tener entre 3 y 100 caracteres y solo contener letras.";
    }
    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,100}$/", $apellido)) {
        $errores[] = "El apellido debe tener entre 3 y 100 caracteres y solo contener letras.";
    }
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Correo inválido.";
    }
    if (!preg_match("/^\d{9}$/", $telefono)) {
        $errores[] = "El teléfono debe tener 9 dígitos.";
    }
    if (strlen($direccion) < 5 || strlen($direccion) > 255) {
        $errores[] = "Dirección entre 5 y 255 caracteres.";
    }
    if (!in_array($rol, ['administrador', 'empleado'])) {
        $errores[] = "Rol no válido.";
    }
    if (!empty($password) || !empty($confirm_password)) {
        if ($password !== $confirm_password) {
            $errores[] = "Las contraseñas no coinciden.";
        } elseif (strlen($password) < 6) {
            $errores[] = "Contraseña mínima de 6 caracteres.";
        }
    }

    // Obtener foto anterior
    $sentencia = $conect->prepare("SELECT foto_perfil FROM usuarios WHERE id_usuario = :id");
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();
    $registro_imagen = $sentencia->fetch(PDO::FETCH_ASSOC);
    $foto_anterior = $registro_imagen["foto_perfil"];

    if (empty($errores)) {
        try {
            $conect->beginTransaction();

            $sentencia = $conect->prepare("UPDATE usuarios SET nombre = :nombre, apellido = :apellido, correo = :correo, telefono = :telefono, direccion = :direccion, rol = :rol WHERE id_usuario = :id");
            $sentencia->bindParam(":nombre", $nombre);
            $sentencia->bindParam(":apellido", $apellido);
            $sentencia->bindParam(":correo", $correo);
            $sentencia->bindParam(":telefono", $telefono);
            $sentencia->bindParam(":direccion", $direccion);
            $sentencia->bindParam(":rol", $rol);
            $sentencia->bindParam(":id", $txtID);
            $sentencia->execute();

            if (!empty($password)) {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $sentencia = $conect->prepare("UPDATE usuarios SET password = :password WHERE id_usuario = :id");
                $sentencia->bindParam(":password", $password_hash);
                $sentencia->bindParam(":id", $txtID);
                $sentencia->execute();
            }

            // Procesar nueva foto si se subió
            if (!empty($_FILES['foto_perfil']['name'])) {
                $foto = $_FILES['foto_perfil']['name'];
                $tmp_foto = $_FILES['foto_perfil']['tmp_name'];
                $permitidos = ["image/jpeg", "image/png", "image/jpg"];
                $tamano_maximo = 2 * 1024 * 1024;

                if (!in_array($_FILES["foto_perfil"]["type"], $permitidos)) {
                    $errores[] = "Formato de imagen no permitido.";
                } elseif ($_FILES["foto_perfil"]["size"] > $tamano_maximo) {
                    $errores[] = "Imagen debe ser menor a 2MB.";
                } else {
                    $fecha_foto = new DateTime();
                    $nombre_foto = $fecha_foto->getTimestamp() . "_" . $foto;

                    if (move_uploaded_file($tmp_foto, "../../img/usuarios/" . $nombre_foto)) {
                        if (!empty($foto_anterior) && $foto_anterior != 'default_admin.jpg' && file_exists("../../img/usuarios/" . $foto_anterior)) {
                            unlink("../../img/usuarios/" . $foto_anterior);
                        }
                        $sentencia = $conect->prepare("UPDATE usuarios SET foto_perfil = :foto WHERE id_usuario = :id");
                        $sentencia->bindParam(":foto", $nombre_foto);
                        $sentencia->bindParam(":id", $txtID);
                        $sentencia->execute();
                    }
                }
            }

            $conect->commit();
            header("Location: index.php");
            exit();
        } catch (Exception $e) {
            $conect->rollBack();
            $errores[] = "Error: " . $e->getMessage();
        }
    }
}

if (isset($_GET['txtID'])) {
    $txtID = $_GET["txtID"];
    $sentencia = $conect->prepare("SELECT * FROM usuarios WHERE id_usuario = :id");
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();
    $registro = $sentencia->fetch(PDO::FETCH_ASSOC);
}

include("../recursos/header.php");
?>

<div class="card bg-dark text-light py-3 px-4">
    <div class="card-header text-center">
        <span class="seccion_titulo">Editar Cliente</span>
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
            <input type="hidden" name="txtID" value="<?= htmlspecialchars($txtID) ?>">

            <div class="text-center mb-4">
                <img src="../../img/usuarios/<?= $registro['foto_perfil'] ?>" alt="Foto de perfil" 
                     class="rounded-circle border border-light border-4 p-1" 
                     width="120" height="120">
                <div class="mt-2">
                    <label class="form-label">Subir nueva foto:</label>
                    <input type="file" name="foto_perfil" class="form-control bg-dark text-light">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nombre:</label>
                        <input type="text" class="form-control bg-dark text-light" name="nombre" value="<?= htmlspecialchars($registro['nombre']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo:</label>
                        <input type="email" class="form-control bg-dark text-light" name="correo" value="<?= htmlspecialchars($registro['correo']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección:</label>
                        <input type="text" class="form-control bg-dark text-light" name="direccion" value="<?= htmlspecialchars($registro['direccion']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nueva Contraseña:</label>
                        <input type="password" class="form-control bg-dark text-light" name="password" placeholder="Opcional">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Apellido:</label>
                        <input type="text" class="form-control bg-dark text-light" name="apellido" value="<?= htmlspecialchars($registro['apellido']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono:</label>
                        <input type="text" class="form-control bg-dark text-light" name="telefono" value="<?= htmlspecialchars($registro['telefono']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol:</label>
                        <select class="form-control bg-dark text-light" name="rol">
                            <option value="administrador" <?= strtolower($registro['rol']) == 'administrador' ? 'selected' : '' ?> >Administrador</option>
                            <option value="empleado" <?= strtolower($registro['rol']) == 'empleado' ? 'selected' : '' ?>>Empleado</option>
                            <option value="cliente" <?= strtolower($registro['rol']) == 'cliente' ? 'selected' : '' ?>>Cliente</option><!-- Esta línea es la nueva -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirmar Contraseña:</label>
                        <input type="password" class="form-control bg-dark text-light" name="confirm_password" placeholder="Confirme nueva contraseña">
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success">Guardar Cambios</button>
                <a href="index.php" class="btn btn-outline-light">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include("../recursos/footer.php"); ?>
