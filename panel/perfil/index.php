<?php 
    include("../bd.php");

    $usuario_id = $_SESSION["usuario_id"];

    $sentencia = $conect->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
    $sentencia->execute([$usuario_id]);
    $usuario = $sentencia->fetch(PDO::FETCH_ASSOC);

    // Variables para mensajes
    $mensaje = "";
    $tipo_mensaje = "";

    // Procesar actualización de perfil
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_profile"])) {
        $nombre = $_POST["nombre"];
        $apellido = $_POST["apellido"];
        $dni = $_POST["dni"];
        $correo = $_POST["correo"];
        $telefono = $_POST["telefono"];
        $direccion = $_POST["direccion"];

        if (!empty($_FILES["foto_perfil"]["name"])) {
            $foto_perfil = $_FILES["foto_perfil"]["name"];
            $ruta_destino = "../../img/usuarios/" . $foto_perfil;
            move_uploaded_file($_FILES["foto_perfil"]["tmp_name"], $ruta_destino);
        } else {
            $foto_perfil = $usuario["foto_perfil"];
        }

        $sentencia = $conect->prepare("UPDATE usuarios SET nombre=?, apellido=?, dni=?, correo=?, telefono=?, direccion=?, foto_perfil=? WHERE id_usuario=?");
        $sentencia->execute([$nombre, $apellido, $dni, $correo, $telefono, $direccion, $foto_perfil, $usuario_id]);

        $mensaje = "¡Perfil actualizado correctamente!";
        $tipo_mensaje = "success";
    }

    // Procesar cambio de contraseña
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_password"])) {
        $new_password = $_POST["new_password"];
        $confirm_password = $_POST["confirm_password"];

        if ($new_password && $confirm_password && $new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sentencia = $conect->prepare("UPDATE usuarios SET password=? WHERE id_usuario=?");
            $sentencia->execute([$hashed_password, $usuario_id]);

            $mensaje = "¡Contraseña actualizada correctamente!";
            $tipo_mensaje = "success";
        } else {
            $mensaje = "Las contraseñas no coinciden o están vacías.";
            $tipo_mensaje = "error";
        }
    }

    include("../recursos/header.php");
?>

<!-- CONTENIDO HTML -->
<div class="card bg-dark py-3 px-5 text-light">
    <div class="mb-4">
        <h2 class="seccion_titulo">Editar Perfil</h2>
        <p class="seccion_parrafo">Actualiza tu información personal y foto de perfil.</p>
    </div>

    <div class="row borde_contenido">
        <!-- Columna Izquierda -->
        <div class="col-md-6">
            <form method="POST" enctype="multipart/form-data" class="card bg-dark text-white py-4 mb-4 border-0">
                <h5 class="text-center text-light mb-3 fw-bold">Información Personal</h5>

                <div class="mb-3">
                    <label class="form-label">Foto de Perfil</label>
                    <input type="file" class="form-control bg-dark text-white" name="foto_perfil">
                </div>

                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control bg-dark text-white" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Apellido</label>
                    <input type="text" class="form-control bg-dark text-white" name="apellido" value="<?= htmlspecialchars($usuario['apellido']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">DNI</label>
                    <input type="text" class="form-control bg-dark text-white" name="dni" value="<?= htmlspecialchars($usuario['dni']) ?>" pattern="[0-9]{8}" title="Debe contener 8 dígitos" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Correo</label>
                    <input type="email" class="form-control bg-dark text-white" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" class="form-control bg-dark text-white" name="telefono" value="<?= htmlspecialchars($usuario['telefono']) ?>" pattern="[0-9]{6,10}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Dirección</label>
                    <textarea class="form-control bg-dark text-white" name="direccion"><?= htmlspecialchars($usuario['direccion']) ?></textarea>
                </div>

                <button type="submit" name="update_profile" class="btn btn-success w-100">Guardar Cambios</button>
            </form>
        </div>

        <!-- Columna Derecha -->
        <div class="col-md-6 card bg-dark p-4 border-0 d-flex flex-column">
            <div class="text-center text-light mb-4">
                <div class="my-2">
                    <img src="../../img/usuarios/<?= htmlspecialchars($usuario['foto_perfil']) ?>" alt="Foto de perfil" class="rounded-circle border border-white border-4 p-1" width="120" height="120">
                </div>
                <h2 class="seccion_titulo"><?= htmlspecialchars($usuario['nombre']) ?></h2>
                <p class="seccion_parrafo"><?= htmlspecialchars($usuario['rol']) ?></p>
            </div>

            <h5 class="text-center text-light mb-3 fw-bold">Seguridad</h5>
            <form method="POST" class="text-white">
                <label class="form-label">Nueva Contraseña</label>
                <input type="password" class="form-control bg-dark text-white mb-2" name="new_password" minlength="6">

                <label class="form-label">Confirmar Contraseña</label>
                <input type="password" class="form-control bg-dark text-white mb-2" name="confirm_password">

                <button type="submit" name="update_password" class="btn btn-danger w-100">Actualizar Contraseña</button>
            </form>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<?php if (!empty($mensaje)): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Swal.fire({
        icon: '<?= $tipo_mensaje ?>',
        title: '<?= $mensaje ?>',
        confirmButtonText: 'Aceptar'
    }).then((result) => {
        if (result.isConfirmed && '<?= $tipo_mensaje ?>' === 'success') {
            window.location.href = "<?= $_SERVER['PHP_SELF'] ?>";
        }
    });
</script>
<?php endif; ?>

<?php include("../recursos/footer.php"); ?>
