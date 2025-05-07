<?php
include("bd.php");

$errores = [];

// Limitar intentos de login (Máximo 5 intentos cada 10 minutos)
if (!isset($_SESSION["login_intentos"])) {
    $_SESSION["login_intentos"] = 0;
    $_SESSION["ultimo_intento"] = time();
}

// Bloqueo temporal de intentos de login si hay demasiados fallos
if ($_SESSION["login_intentos"] >= 5 && time() - $_SESSION["ultimo_intento"] < 600) { // 600s = 10 minutos
    $errores[] = "Demasiados intentos fallidos. Intente nuevamente en 10 minutos.";
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = filter_var(trim($_POST["correo"]), FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];

    // Validar entrada
    if (empty($correo) || empty($password)) {
        $errores[] = "Todos los campos son obligatorios.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Ingrese un correo válido.";
    }

    if (empty($errores)) {
        try {
            // Buscar usuario en la base de datos
            $sentencia = $conect->prepare("SELECT id_usuario, nombre, correo, direccion, foto_perfil, password, rol FROM usuarios WHERE correo = :correo LIMIT 1");
            $sentencia->bindParam(":correo", $correo, PDO::PARAM_STR);
            $sentencia->execute();
            $usuario = $sentencia->fetch(PDO::FETCH_ASSOC);

            // Verificar contraseña
            if ($usuario && password_verify($password, $usuario["password"])) {
                // Inicio de sesión exitoso: Resetear intentos fallidos
                $_SESSION["login_intentos"] = 0;

                // Regenerar la sesión para evitar secuestro de sesión
                session_regenerate_id(true);

                // Guardar datos en sesión
                $_SESSION["usuario_id"] = $usuario["id_usuario"];
                $_SESSION["usuario_nombre"] = htmlspecialchars($usuario["nombre"]);
                $_SESSION["usuario_apellido"] = htmlspecialchars($usuario["apellido"]);
                $_SESSION["usuario_rol"] = $usuario["rol"];
                $_SESSION["usuario_correo"] = $usuario["correo"];
                $_SESSION["usuario_direccion"] = $usuario["direccion"];
                $_SESSION["usuario_foto"] = $usuario["foto_perfil"];

                // Redirigir según el rol
                if ($usuario["rol"] == "Cliente") {
                    header("Location: ../menu/index.php");
                } else {
                    header("Location: inicio/index.php");
                }
                exit();
            } else {
                $errores[] = "Correo o contraseña incorrectos.";
                $_SESSION["login_intentos"]++; // Incrementar intentos fallidos
                $_SESSION["ultimo_intento"] = time();
            }
        } catch (PDOException $e) {
            $errores[] = "Error en el servidor. Intente más tarde.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuentro - Login</title>
    <link rel="stylesheet" href="recursos/estilos.css">
    <link rel="icon" href="../img/logos/ep_food.png" type="image/png">
    <link rel="stylesheet" href="../css/data.css">
</head>
<body>
    <div class="contenedor">
        <div class="contenedor_imagen">
            <img src="../img/logos/logo_solo.png" alt="logo">
        </div>
        <form action="login.php" method="POST">
            <?php if (!empty($errores)) { ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errores as $error) { echo "<li>" . htmlspecialchars($error) . "</li>"; } ?>
                    </ul>
                </div>
            <?php } ?>
            
            <div class="seccion">
                <label class="seccion_subtitulo" for="correo">Correo</label>
                <input type="email" name="correo" id="correo" placeholder="Ingrese su correo" value="luiosorio@email.com" required>
            </div>
            <div class="seccion">
                <label class="seccion_subtitulo" for="password">Contraseña</label>
                <input type="password" name="password" id="password" value="123456" placeholder="Ingrese su contraseña" required>
            </div>
            <a href="#" class="seccion_parrafo olvido">¿Olvidó su contraseña?</a>

            <div class="enlaces">
                <button type="submit" class="boton_seccion primario">Iniciar Sesión</button>
                <a class="boton_seccion second" href="registro.php">Registrarse</a>
                <a href="../" class="boton_seccion black ancho_completo">Volver</a>
            </div>
        </form>
    </div>
</body>
</html>
