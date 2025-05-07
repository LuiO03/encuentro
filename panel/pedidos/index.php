<?php
include("../bd.php");

// Eliminar un pedido si se recibe un ID válido
if (isset($_GET['id_eliminar'])) {
    $idPedido = $_GET['id_eliminar'];

    $sentencia = $conect->prepare("DELETE FROM carrito WHERE id_carrito = :id");
    $sentencia->bindParam(":id", $idPedido);
    $sentencia->execute();

    header("Location: index.php?eliminado=1");
    exit();
}

// Obtener la lista de pedidos con información del usuario
$sentencia = $conect->prepare("SELECT c.*, u.nombre, u.apellido, u.direccion FROM carrito c 
                                INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
                                ORDER BY c.fecha_creacion DESC");
$sentencia->execute();
$lista_pedidos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

include("../recursos/header.php");
?>

<div class="card bg-dark text-white py-3 px-4">
    <div class="card-header text-center">
        <span class="seccion_titulo">Gestión de Pedidos</span>
        <p class="seccion_parrafo">Administra los pedidos realizados por los usuarios.</p>
    </div>
    <div class="card-body">
        <div class="table-responsive table-container borde_contenido">
            <table id="table" class="table table-dark table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Tipo de Entrega</th>
                        <th>Dirección de Entrega</th> <!-- NUEVA COLUMNA -->
                        <th>Total Pedido</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_pedidos as $pedido) { ?>
                        <tr>
                            <td class="text-center"><?php echo $pedido['id_carrito']; ?></td>
                            <td><?php echo htmlspecialchars($pedido['nombre'] . ' ' . $pedido['apellido']); ?></td>
                            <td>
                                <?php 
                                    setlocale(LC_TIME, 'es_ES.UTF-8');
                                    echo strftime('%d %B %Y, %I:%M %p', strtotime($pedido['fecha_creacion']));
                                ?>
                            </td>
                            <td><?php echo ucfirst($pedido['tipo_entrega']); ?></td>
                            <td>
                                <?php
                                    echo $pedido['tipo_entrega'] === 'delivery'
                                        ? htmlspecialchars($pedido['direccion'])
                                        : 'Local';
                                ?>
                            </td>
                            <td>
                                S/. <?php echo number_format($pedido['total_pedido'], 2); ?>
                            </td>
                            <td>
                                <form method="post" action="actualizar_estado.php">
                                    <input type="hidden" name="id_pedido" value="<?php echo $pedido['id_carrito']; ?>">
                                    <select name="nuevo_estado" class="cursor-pointer form-select estado-pedido" data-id="<?php echo $pedido['id_carrito']; ?>">
                                        <option value="pendiente" <?php echo $pedido['estado'] == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                        <option value="finalizado" <?php echo $pedido['estado'] == 'finalizado' ? 'selected' : ''; ?>>Finalizado</option>
                                        <option value="cancelado" <?php echo $pedido['estado'] == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                    </select>
                                </form>
                            </td>
                            <td class="acciones">
                                <button class="btn btn-info" onclick="verDetalles(<?php echo $pedido['id_carrito']; ?>)">
                                    <i class="fas fa-eye"></i> Ver Detalles
                                </button>
                                <a class="btn btn-danger" href="index.php?id_eliminar=<?php echo $pedido['id_carrito']; ?>" 
                                    onclick="confirmarEliminacion(event, 'index.php?id_eliminar=<?php echo $pedido['id_carrito']; ?>')">
                                    <i class="fas fa-trash"></i> Borrar
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalDetalles" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header text-center">
                <span class="seccion_titulo text-center">Detalles del Pedido</span>
            </div>
            <div class="modal-body">
                <div id="detallesPedido"></div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmarEliminacion(event, url) {
        event.preventDefault();
        Swal.fire({
            title: "¿Estás seguro?",
            text: "Esta acción eliminará el pedido permanentemente.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('eliminado')) {
            Swal.fire({
                title: '¡Eliminado!',
                text: 'El producto ha sido eliminado correctamente.',
                icon: 'success',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        }
    });

    function verDetalles(idPedido) {
        fetch(`ver_detalles_pedido.php?id_carrito=${idPedido}`)
            .then(response => response.json())
            .then(data => {
                let detallesHTML = "<table class='table table-dark table-striped'>";
                detallesHTML += "<thead><tr><th>Producto</th><th>Cantidad</th><th>Precio Unitario</th><th>Total</th></tr></thead>";
                detallesHTML += "<tbody>";

                let totalGeneral = 0;

                if (data.error) {
                    detallesHTML += `<tr><td colspan='4' class='text-center'>${data.error}</td></tr>`;
                } else {
                    data.forEach(detalle => {
                        const precioTotal = parseFloat(detalle.precio_total);
                        totalGeneral += precioTotal;

                        detallesHTML += `<tr>
                            <td>${detalle.nombre_producto}</td>
                            <td>${detalle.cantidad}</td>
                            <td>S/. ${parseFloat(detalle.precio_unitario).toFixed(2)}</td>
                            <td>S/. ${precioTotal.toFixed(2)}</td>
                        </tr>`;
                    });
                }

                detallesHTML += "</tbody></table>";

                detallesHTML += `
                    <div class="text-end mt-3">
                        <h5>Pago Total: <span class="text-success">S/. ${totalGeneral.toFixed(2)}</span></h5>
                    </div>
                `;

                document.getElementById("detallesPedido").innerHTML = detallesHTML;
                $("#modalDetalles").modal("show");
            })
            .catch(error => console.error("Error al obtener detalles:", error));
    }

    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".estado-pedido").forEach(select => {
            select.addEventListener("change", function () {
                let idPedido = this.getAttribute("data-id");
                let nuevoEstado = this.value;

                fetch("actualizar_estado.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `id_pedido=${idPedido}&nuevo_estado=${nuevoEstado}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        Swal.fire({
                            icon: "success",
                            title: "Estado actualizado",
                            text: "El estado del pedido se ha cambiado correctamente",
                            confirmButtonText: "OK"
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "No se pudo actualizar el estado del pedido",
                            confirmButtonText: "Intentar de nuevo"
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Hubo un problema con la actualización",
                        confirmButtonText: "Cerrar"
                    });
                    console.error("Error:", error);
                });
            });
        });
    });
</script>

<?php include("../recursos/footer.php"); ?>
