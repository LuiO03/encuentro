<?php
include("../panel/bd.php");
include('../archives/header.php');

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro Exitoso</title>
    <link rel="icon" href="../img/logos/ep_food.png" type="image/png">
    <link rel="stylesheet" href="../css/nav.css">
    <link rel="stylesheet" href="../css/data.css">
    <link rel="stylesheet" href="../css/normalize.css">
    <style>
        .card {
            display: flex;
            flex-direction: column;
            margin: 50px auto 120px auto;
            border: 5px double var(--color_primario);
            box-shadow: 0 0 16px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 50%;
            align-items: center;
            gap: 20px;
        }
        .seccion_parrafo{
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="card container_seccion">
        <h2 class="seccion_titulo">¡Registro exitoso!</h2>
        <p class="seccion_parrafo">
            ¡Bienvenido a Encuentro! Tu cuenta ha sido creada correctamente y ya formas parte de nuestra comunidad.
        </p>
        <p class="seccion_parrafo">
            Ahora podrás explorar nuestro menú, hacer reservas, dejar comentarios y disfrutar de todos nuestros servicios exclusivos para clientes registrados.
        </p>
        <p class="seccion_parrafo">
            Si tienes alguna duda o necesitas asistencia, nuestro equipo está siempre disponible para ayudarte.
        </p>
        <a class="boton_seccion" href="login.php">Ir a Iniciar Sesión</a>
    </div>
</body>
<?php include('../archives/footer.php'); ?>