<?php
include '../../back/db/connection.php';

try {
    // Traer pedidos web pendientes o procesando
    $query = "SELECT 
                pw.id_pedido_web,
                CONCAT(pw.nombre, ' ', pw.apellido_paterno, ' ', pw.apellido_materno) AS cliente,
                pw.municipio,
                pw.fecha_creacion,
                pw.total,
                pw.estatus_pago,
                pw.estatus_pedido,
                pw.order_number
            FROM pedidos_web pw
            WHERE pw.estatus_pedido IN ('pendiente','procesando')
            ORDER BY pw.fecha_creacion DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="container mt-3">
        <a href="dashboard.php" class="btn btn-success">← Regresar al Dashboard</a>
    </div>

    <style>
        .table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        .table th, .table td { padding: 12px; border-bottom: 1px solid #ddd; text-align: center; }
        th { background-color: #8BC34A; color: white; font-size: 16px; }
        td { background-color: #f9f9f9; font-size: 14px; }

        .btn { padding: 4px 8px; margin: 2px; border-radius: 5px; font-size: 10px; }
        .details-btn { background-color: #6C757D; color: white; }
        .print-btn { background-color: #17A2B8; color: white; }
        .assign-btn { background-color: #28A745; color: white; }
        .delete-btn { background-color: #DC3545; color: white; }
    </style>

    <div class="container mt-4">
        <h1 class="mb-4 text-center">📦 Pedidos Online</h1>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <td>Order #</td>
                    <td>Cliente</td>
                    <td>Municipio/Alcaldía</td>
                    <td>Fecha</td>
                    <td>Estatus Pago</td>
                    <td>Estatus Pedido</td>
                    <td>Total</td>
                    <td>Acciones</td>
                </tr>
                </thead>
                <tbody>
                <?php
                if (count($result) > 0) {
                    foreach ($result as $row) {
                        $id = $row['id_pedido_web'];
                        $cliente = htmlspecialchars($row['cliente']);
                        $municipio = htmlspecialchars($row['municipio'] ?? 'Sin dirección');
                        $fecha = $row['fecha_creacion'];
                        $estatusPago = ucfirst($row['estatus_pago']);
                        $estatusPedido = ucfirst($row['estatus_pedido']);
                        $total = number_format($row['total'], 2);

                        echo "<tr>
                            <td>{$row['order_number']}</td>
                            <td>$cliente</td>
                            <td>$municipio</td>
                            <td>$fecha</td>
                            <td>$estatusPago</td>
                            <td>$estatusPedido</td>
                            <td>$$total</td>
                            <td>
                                <button class='btn details-btn' data-bs-toggle='modal' data-bs-target='#modalDetalles' onclick='cargarDetalles($id)'>
                                    <i class='bi bi-eye'></i> Detalles
                                </button>
                                <button class='btn print-btn' onclick='imprimirPedido($id)'>
                                    <i class='bi bi-printer'></i> Imprimir
                                </button>
                                <button class='btn assign-btn' onclick='asignarPedido($id)'>
                                    <i class='bi bi-person-check'></i> Asignar
                                </button>
                                <button class='btn delete-btn' onclick='eliminarPedido($id)'>
                                    <i class='bi bi-trash'></i> Eliminar
                                </button>
                            </td>
                        </tr>";
                    }
                } else {
                    echo '<tr><td colspan="8" class="text-center">No hay pedidos online pendientes.</td></tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Detalles -->
    <div class="modal fade" id="modalDetalles" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles del Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="detallesContenido">Cargando...</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Asignar -->
    <div class="modal fade" id="modalAsignar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Asignar Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="mensajeAsignacion"></p>
                    <div class="mb-3">
                        <label for="selectDeliver" class="form-label">Selecciona un repartidor:</label>
                        <select id="selectDeliver" class="form-select">
                            <option value="">-- Selecciona --</option>
                            <?php
                            $usuarios = $conn->query("SELECT id_usuario, nombre, apellido FROM usuarios WHERE rol = 'deliver'");
                            foreach ($usuarios as $usuario) {
                                echo "<option value='{$usuario['id_usuario']}'>{$usuario['nombre']} {$usuario['apellido']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="confirmarAsignacion">Asignar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function cargarDetalles(id) {
            document.getElementById("detallesContenido").innerHTML = "<p>Cargando detalles...</p>";
            fetch(`../../back/produccion/detalles_pedido_web.php?id=${id}`)
                .then(r => r.text())
                .then(data => document.getElementById("detallesContenido").innerHTML = data)
                .catch(err => document.getElementById("detallesContenido").innerHTML = "❌ Error al cargar detalles.");
        }

        function imprimirPedido(id) {
            window.open(`../../back/produccion/imprimir_pedido_web.php?id=${id}`, '_blank');
        }

        let idPedidoAsignar = null;
        function asignarPedido(id) {
            idPedidoAsignar = id;
            document.getElementById("mensajeAsignacion").innerText = `Estás por asignar el pedido #${id}`;
            new bootstrap.Modal(document.getElementById('modalAsignar')).show();
        }

        document.getElementById('confirmarAsignacion').addEventListener('click', function () {
            const idDeliver = document.getElementById('selectDeliver').value;
            if (!idDeliver) { alert("Selecciona un repartidor"); return; }

            let formData = new FormData();
            formData.append("id_pedido_web", idPedidoAsignar);
            formData.append("id_usuario", idDeliver);

            fetch("../../back/produccion/asignar_pedido_web.php", { method: "POST", body: formData })
                .then(r => r.json())
                .then(data => {
                    if(data.success) location.reload();
                    else alert("Error al asignar.");
                })
                .catch(err => alert("Error de comunicación."));
        });

        function eliminarPedido(id) {
            if (confirm("¿Eliminar este pedido?")) {
                let formData = new FormData();
                formData.append("id_pedido_web", id);  // 👈 clave que espera PHP

                fetch(`../../back/produccion/eliminar_pedido_web.php`, {
                    method: 'POST',
                    body: formData
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) location.reload();
                        else alert("Error: " + data.error);
                    });
            }
        }

    </script>

    <?php
} catch (PDOException $e) {
    echo "<div class='container mt-3'><p class='text-danger'>Error: {$e->getMessage()}</p></div>";
}
?>
