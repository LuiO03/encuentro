<?php 
    include("../bd.php");

    // Obtener información del restaurante
    $sentencia = $conect->prepare("SELECT * FROM restaurant LIMIT 1");
    $sentencia->execute();
    $restaurant = $sentencia->fetch(PDO::FETCH_ASSOC);

    // Si se envía el formulario de actualización del restaurante
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_restaurant"])) {
        $hora_abierto = $_POST["hora_abierto"];
        $hora_cerrado = $_POST["hora_cerrado"];
        $direccion = $_POST["direccion"];
        $correo = $_POST["correo"];
        $telefono = $_POST["telefono"];
        $celular = $_POST["celular"];

        $sentencia = $conect->prepare("UPDATE restaurant SET hora_abierto=?, hora_cerrado=?, direccion=?, correo=?, telefono=?, celular=? WHERE id=?");
        $sentencia->execute([$hora_abierto, $hora_cerrado, $direccion, $correo, $telefono, $celular, $restaurant["id"]]);

        // Redirigir para evitar la reenvío del formulario al recargar
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Obtener redes sociales
    $sentencia = $conect->prepare("SELECT * FROM social_media");
    $sentencia->execute();
    $redes_sociales = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    // Si se envía el formulario de actualización de redes sociales
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_social"])) {
        $id_red = $_POST["id_red"];
        $url = $_POST["url"];
        $estado = isset($_POST["estado"]) ? 1 : 0; 

        $sentencia = $conect->prepare("UPDATE social_media SET url=?, estado=? WHERE id_red=?");
        $sentencia->execute([$url, $estado, $id_red]);

        // Redirigir para evitar reenvíos de formulario
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    include("../recursos/header.php"); 
?>

<style>
    input[type="time"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
        cursor: pointer;
    }
</style>

<div class="card bg-dark py-3 px-5 text-light">
    <div class="text-center mb-4">
        <h2 class="seccion_titulo">Administrar Restaurante</h2>
        <p class="seccion_parrafo">Edita la información del restaurante y gestiona sus redes sociales.</p>
    </div>

    <div class="row borde_contenido">
        <!-- Información del Restaurante (Izquierda) -->
        <div class="col-md-6">
            <form method="POST" class="card bg-dark text-white py-4 mb-4 border-0">
                <h5 class="text-center text-light mb-3 fw-bold">Información del Restaurante</h5>

                <div class="mb-3">
                    <label class="form-label">Horario de Atención</label>
                    <div class="d-flex gap-2">
                        <input type="time" class="form-control bg-dark text-white" name="hora_abierto" 
                            value="<?php echo isset($restaurant['hora_abierto']) ? $restaurant['hora_abierto'] : ''; ?>" required>
                        <span class="text-light">a</span>
                        <input type="time" class="form-control bg-dark text-white" name="hora_cerrado" 
                            value="<?php echo isset($restaurant['hora_cerrado']) ? $restaurant['hora_cerrado'] : ''; ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Dirección</label>
                    <input type="text" class="form-control bg-dark text-white" name="direccion" 
                        value="<?php echo $restaurant['direccion']; ?>" 
                        minlength="5" title="La dirección debe tener al menos 5 caracteres" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control bg-dark text-white" name="correo" 
                        value="<?php echo $restaurant['correo']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" class="form-control bg-dark text-white" name="telefono" 
                        value="<?php echo $restaurant['telefono']; ?>" pattern="[0-9]{6,10}" 
                        title="Debe contener entre 6 y 10 dígitos numéricos" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Celular</label>
                    <input type="text" class="form-control bg-dark text-white" name="celular" 
                        value="<?php echo $restaurant['celular']; ?>" pattern="[0-9]{9,10}" 
                        title="Debe contener entre 9 y 10 dígitos numéricos" required>
                </div>

                <button type="submit" name="update_restaurant" class="btn btn-success">Guardar Cambios</button>
            </form>
        </div>

        <!-- Redes Sociales (Derecha) -->
        <div class="col-md-6 card bg-dark p-4 mb-4 border-0">
            <h5 class="text-center text-light mb-3 fw-bold">Redes Sociales</h5>
            <div class="list-group">
                <?php foreach ($redes_sociales as $red) { ?>
                    <form method="POST" class="bg-dark text-light p-1">
                        <input type="hidden" name="id_red" value="<?php echo $red['id_red']; ?>">

                        <label class="form-label"><?php echo $red['nombre']; ?></label>
                        <input type="url" class="form-control bg-dark text-white mb-2" name="url" 
                            value="<?php echo $red['url']; ?>" pattern="https?://.*" 
                            title="Debe comenzar con http:// o https://" required>

                        <div class="d-flex justify-content-between align-items-center">
                            <div class="form-check form-switch">
                                <input class="form-check-input bg-primary" type="checkbox" name="estado" value="1" 
                                    <?php echo ($red['estado'] == 1) ? 'checked' : ''; ?>>
                                <label class="form-check-label"><?php echo ($red['estado'] == 1) ? 'Activo' : 'Inactivo'; ?></label>
                            </div>
                            <button type="submit" name="update_social" class="btn btn-success">Guardar</button>
                        </div>
                    </form>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php include("../recursos/footer.php"); ?>
