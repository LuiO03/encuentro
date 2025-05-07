<?php 
    include("../panel/bd.php");

    if (!isset($_SESSION['usuario_nombre'])) {
        header("Location: ../index.php");
        exit;
    }
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
            $ruta_destino = "../img/usuarios/" . $foto_perfil;
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuentro - Perfil</title>
    <link rel="icon" href="../img/logos/ep_food.png" type="image/png">
    <link rel="stylesheet" href="../css/nav.css">
    <link rel="stylesheet" href="../css/data.css">
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="estilos.css">
    <script src="https://kit.fontawesome.com/58fc50b085.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function verificarSesion(event) {
            <?php if (!$usuario_id): ?>
                event.preventDefault();
                Swal.fire({
                    title: '¡Debes iniciar sesión!',
                    text: 'Por favor, inicia sesión para realizar una reserva.',
                    icon: 'warning',
                    confirmButtonText: 'Iniciar sesión'
                }).then(() => { window.location.href = '../panel/login.php'; });
            <?php endif; ?>
        }
    </script>
</head>
<?php include('../archives/header.php'); ?>

<!-- Contenido Principal -->
<section class="profile-section container_seccion">
    <div class="container">
        <!-- Aside -->
        <aside class="profile-aside">
            <nav>
                <ul>
                    <li><a href="perfil.php" class="active">Mi Perfil</a></li>
                    <li><a href="pedidos.php">Mis Pedidos</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="profile-content">
            <div class="banner-container">
                <div class="profile-banner"></div>
                <div class="profile-info">
                    <?php
                        $foto = !empty($usuario['foto_perfil']) && file_exists("../img/usuarios/" . $usuario['foto_perfil'])
                            ? "../img/usuarios/" . htmlspecialchars($usuario['foto_perfil'])
                            : "../img/recursos/perfil.webp";
                    ?>
                    <div class="profile-picture">
                        <img src="<?= $foto ?>" alt="Foto de perfil">
                    </div>
                    <h2 class="profile-name">
                        <?= htmlspecialchars($usuario['nombre']) ?> <?= htmlspecialchars($usuario['apellido']) ?>
                    </h2>
                </div>
            </div>

            <div class="profile-details">
                <form method="POST" enctype="multipart/form-data">
                    <div class="input-group">
                        <label class="form-label">Foto de Perfil</label>
                        <input type="file" class="form-input" name="foto_perfil" accept="image/*">
                    </div>
                    <div class="input-group">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-input" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" 
                            placeholder="Apellido Nombre" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,30}" minlength="2" maxlength="30" required>
                    </div>
                    <div class="input-group">
                        <label class="form-label">Apellido</label>
                        <input type="text" class="form-input" name="apellido" value="<?= htmlspecialchars($usuario['apellido']) ?>"
                            placeholder="Ingrese Apellido" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,30}" minlength="2" maxlength="30" required>
                    </div>
                    <div class="input-group">
                        <label class="form-label">DNI</label>
                        <input type="text" class="form-input" name="dni" value="<?= htmlspecialchars($usuario['dni']) ?>"
                            placeholder="Ingrese DNI" pattern="\d{8}" maxlength="8" required>
                    </div>
                    <div class="input-group">
                        <label class="form-label">Correo</label>
                        <input type="email" class="form-input" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>"
                            placeholder="Ingrese Email" maxlength="60" required>
                    </div>
                    <div class="input-group">
                        <label class="form-label">Teléfono</label>
                        <input type="tel" class="form-input" name="telefono" value="<?= htmlspecialchars($usuario['telefono']) ?>"
                            placeholder="Ingrese Teléfono" pattern="\d{9}" maxlength="9" required>
                    </div>
                    <div class="input-group">
                        <label class="form-label">Dirección</label>
                        <input class="form-input" name="direccion" placeholder="Ingrese Dirección"
                                maxlength="200" required value="<?= htmlspecialchars($usuario['direccion'])?>">
                    </div>
                    <button type="submit" name="update_profile" class="boton_seccion">Guardar Cambios</button>
                </form>

                <form method="POST">
                    <h2 class="form-title">Seguridad</h2>
                    <div class="input-group">
                        <label class="form-label">Nueva Contraseña</label>
                        <input type="password" class="form-input" name="new_password" placeholder="Mínimo 6 caracteres" minlength="6" maxlength="30">
                    </div>
                    <div class="input-group">
                        <label class="form-label">Confirmar Contraseña</label>
                        <input type="password" class="form-input" name="confirm_password" placeholder="Repite la nueva contraseña" minlength="6" maxlength="30">
                    </div>
                    <button type="submit" name="update_password" class="boton_seccion">Actualizar Contraseña</button>
                </form>
            </div>
        </div>
    </div>
</section>
<script>
    <?php if (!empty($mensaje)): ?>
        Swal.fire({
            icon: '<?= $tipo_mensaje ?>',
            title: '<?= $mensaje ?>',
            confirmButtonText: 'Aceptar'
        }).then(() => {
            window.location.href = "<?= $_SERVER['PHP_SELF'] ?>";
        });
    <?php endif; ?>
</script>

<?php include("../archives/footer.php"); ?>

