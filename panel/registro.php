<?php
    include("bd.php");
    try {
        $conect = new PDO("mysql:host=$servidor;dbname=$baseDatos", $usuario, $contrasenia);
        $conect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $error) {
        die("Error de conexión: " . $error->getMessage());
    }

    // Variables de error
    $error = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obtener y limpiar los datos
        $nombre = trim($_POST["nombre"]);
        $apellido = trim($_POST["apellido"]);
        $correo = trim($_POST["correo"]);
        $password = $_POST["password"];
        $confirmPassword = $_POST["confirmPassword"];

        // Validación
        if (empty($nombre) || empty($apellido) || empty($correo) || empty($password) || empty($confirmPassword)) {
            $error = "Por favor complete todos los campos.";
        } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $error = "Correo electrónico no válido.";
        } elseif ($password !== $confirmPassword) {
            $error = "Las contraseñas no coinciden.";
        } else {
            // Verificar si ya existe un usuario con el mismo correo
            $consulta = $conect->prepare("SELECT id_usuario FROM usuarios WHERE correo = :correo");
            $consulta->bindParam(':correo', $correo);
            $consulta->execute();

            if ($consulta->rowCount() > 0) {
                $error = "Ya existe una cuenta con este correo.";
            } else {
                // Encriptar la contraseña
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                // Insertar nuevo cliente
                $sql = "INSERT INTO usuarios (nombre, apellido, correo, password, rol) 
                        VALUES (:nombre, :apellido, :correo, :password, 'Cliente')";
                $stmt = $conect->prepare($sql);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':apellido', $apellido);
                $stmt->bindParam(':correo', $correo);
                $stmt->bindParam(':password', $passwordHash);

                if ($stmt->execute()) {
                    // Redirigir al usuario a confirmación
                    header("Location: confirmacion.php");
                    exit();
                } else {
                    $error = "Ocurrió un error al registrar. Inténtalo nuevamente.";
                }
            }
        }
    }
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Cuenta</title>
    <link rel="stylesheet" href="recursos/estilos.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="icon" href="../img/logos/ep_food.png" type="image/png">
    <link rel="stylesheet" href="../css/data.css">
    <script defer src="script.js"></script>
</head>

<body>
    <div class="contenedor">
        <div class="contenedor_imagen">
            <img src="../img/logos/logo_solo.png" alt="logo">
        </div>
        <p class="seccion_parrafo">
            Crea tu cuenta y accede a todos nuestros servicios
        </p>
        <form id="registroForm" class="formulario" method="POST" action="">
            <div class="seccion_info">
                <div class="seccion">
                    <label class="seccion_subtitulo" for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" placeholder="Ingrese su Nombre" required>
                </div>

                <div class="seccion">
                    <label class="seccion_subtitulo" for="apellido">Apellido:</label>
                    <input type="text" name="apellido" id="apellido" placeholder="Ingrese su Apellido" required>
                </div>
            </div>
            <div class="seccion">
                <label class="seccion_subtitulo" for="correo">Correo Electrónico:</label>
                <input type="email" name="correo" id="correo" placeholder="Ingrese su correo" required>
            </div>
            <div class="seccion">
                <label class="seccion_subtitulo" for="password">Contraseña:</label>
                <input type="password" name="password" id="password" placeholder="Ingrese su contraseña" required>
            </div>
            <div class="seccion">
                <label class="seccion_subtitulo" for="confirmPassword">Confirmar Contraseña:</label>
                <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirme su contraseña" required>
            </div>
            <?php if (!empty($error)) : ?>
                <p class="error" style="color: red; text-align: center;"><?= $error ?></p>
            <?php endif; ?>
            <div class="enlaces">
                <button class="boton_seccion second" type="submit">Registrarse</button>
            </div>
        </form>
        <div class="seccion">
            <p class="seccion_parrafo">
                ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a>
            </p>
        </div>
    </div>
</body>

</html>
