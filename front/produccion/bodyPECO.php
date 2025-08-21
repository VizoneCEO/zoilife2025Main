<?php
include '../../back/db/connection.php';

try {
    $query = "SELECT 
                pw.id_pedido_web, 
                pw.nombre AS cliente, 
                pw.fecha_creacion, 
                pw.total, 
                CONCAT(u.nombre, ' ', u.apellido) AS repartidor
              FROM pedidos_web pw
              JOIN asignaciones_web aw ON pw.id_pedido_web = aw.id_pedido_web
              JOIN usuarios u ON aw.id_usuario = u.id_usuario
              WHERE pw.estatus_pedido = 'en curso'
              ORDER BY u.nombre, pw.id_pedido_web ASC";

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
        .btn { padding: 8px 12px; margin: 2px; border-radius: 5px; cursor: pointer; font-size: 14px; display: inline-flex; align-items: center; gap: 5px; }
        .details-btn { background-color: #6C757D; color: white; }
        .print-btn { background-color: #17A2B8; color: white; }
        .reassign-btn { background-color: #FFC107; color: white; }
        .cancel-btn { background-color: #dc3545; color: white; }
    </style>

    <div class="container mt-4">
        <h1 class="mb-4 text-center">Pedidos Web en Reparto</h1>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <td>ID Pedido</td>
                    <td>Cliente</td>
                    <td>Fecha de Creación</td>
                    <td>Total</td>
                    <td>Acciones</td>
                </tr>
                </thead>
                <tbody>
                <?php
                $repartidor_actual = '';
                $colores = ['#ffffff', '#f2f2f2'];
                $colorIndex = 0;

                if (count($result) > 0) {
                    foreach ($result as $row) {
                        $id = $row['id_pedido_web'];
                        $cliente = htmlspecialchars($row['cliente']);
                        $fecha = $row['fecha_creacion'];
                        $total = number_format($row['total'], 2);
                        $repartidor = htmlspecialchars($row['repartidor']);

                        if ($repartidor !== $repartidor_actual) {
                            $repartidor_actual = $repartidor;
                            $colorIndex = 1 - $colorIndex;

                            echo "<tr style='background-color: #c6e6c3; font-weight: bold;'>
                                    <td colspan='5'>REPARTIDOR: $repartidor</td>
                                  </tr>";
                        }

                        $rowColor = $colores[$colorIndex];

                        echo "<tr style='background-color: $rowColor;'>
                                <td>WEB-$id</td>
                                <td>$cliente</td>
                                <td>$fecha</td>
                                <td>$$total</td>
                                <td>
                                    <button class='btn details-btn' data-bs-toggle='modal' data-bs-target='#modalDetalles' onclick='cargarDetallesWeb($id)'>
                                        <i class='bi bi-eye'></i> Detalles
                                    </button>
                                    <button class='btn print-btn' onclick='generarPDFWeb($id)'>
                                        <i class='bi bi-printer'></i> Imprimir
                                    </button>
                                    <button class='btn reassign-btn' onclick='reasignarPedidoWeb($id)'>
                                        <i class='bi bi-arrow-repeat'></i> Re Asignar
                                    </button>
                                    <button class='btn cancel-btn' onclick='cancelarPedidoWeb($id)'>
                                        <i class='bi bi-x-circle'></i> Cancelar
                                    </button>
                                </td>
                              </tr>";
                    }
                } else {
                    echo '<tr><td colspan="5" class="text-center">No hay pedidos web en reparto.</td></tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Detalles -->
    <div class="modal fade" id="modalDetalles" tabindex="-1" aria-labelledby="modalDetallesLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetallesLabel">Detalles del Pedido Web</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div id="detallesContenido">
                        <p class="text-center">Cargando detalles...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Asignar/Reasignar -->
    <div class="modal fade" id="modalAsignar" tabindex="-1" aria-labelledby="modalAsignarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAsignarLabel">Asignar Pedido Web</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
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
                    <button type="button" class="btn btn-success" id="confirmarAsignacion">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function cargarDetallesWeb(id) {
            let detallesContenido = document.getElementById("detallesContenido");
            detallesContenido.innerHTML = "<p class='text-center'>Cargando detalles...</p>";

            fetch(`../../back/produccion/detalles_pedido_web.php?id=${id}`)
                .then(response => response.text())
                .then(data => { detallesContenido.innerHTML = data; })
                .catch(error => { detallesContenido.innerHTML = "<p class='text-center text-danger'>Error al cargar detalles.</p>"; });
        }

        function generarPDFWeb(id) {
            let url = `../../back/produccion/imprimir_pedido_web.php?id=${id}`;
            window.open(url, '_blank');
        }



        /* ====== REASIGNAR PEDIDO WEB ====== */
        let idPedidoWebAAsignar = null;

        function reasignarPedidoWeb(id) {
            idPedidoWebAAsignar = id;
            const modal = new bootstrap.Modal(document.getElementById('modalAsignar'));
            modal.show();

            setTimeout(() => {
                document.getElementById('modalAsignarLabel').innerText = "Re Asignar Pedido Web";
                document.getElementById('mensajeAsignacion').innerText = "Cargando información del pedido...";
                document.getElementById('selectDeliver').value = "";

                fetch(`../../back/produccion/obtener_cliente_web.php?id=${id}`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('mensajeAsignacion').innerText =
                                `Estás por reasignar el pedido web de ${data.cliente} a otro repartidor.`;
                        } else {
                            document.getElementById('mensajeAsignacion').innerText =
                                "⚠️ No se pudo obtener el nombre del cliente.";
                        }
                    })
                    .catch(() => {
                        document.getElementById('mensajeAsignacion').innerText =
                            "Error al cargar los datos.";
                    });
            }, 200);
        }

        document.getElementById('confirmarAsignacion').addEventListener('click', function () {
            const idDeliver = document.getElementById('selectDeliver').value;
            if (!idDeliver) {
                alert("⚠️ Selecciona un repartidor antes de continuar.");
                return;
            }

            let formData = new FormData();
            formData.append("id_pedido_web", idPedidoWebAAsignar);
            formData.append("id_usuario", idDeliver);

            fetch("../../back/produccion/reasignar_pedido_web.php", {
                method: "POST",
                body: formData
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        alert("✅ Pedido web reasignado correctamente.");
                        location.reload();
                    } else {
                        alert("❌ Error al reasignar: " + (data.error || "Inténtalo de nuevo."));
                    }
                })
                .catch(() => {
                    alert("❌ Error de comunicación con el servidor.");
                });
        });
    </script>

    <script>
        function cancelarPedidoWeb(id) {
            if (confirm("¿Cancelar este pedido web?")) {
                let formData = new FormData();
                formData.append("id_pedido_web", id);

                fetch(`../../back/produccion/eliminar_pedido_web.php`, {
                    method: 'POST',
                    body: formData
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            alert("✅ Pedido cancelado correctamente.");
                            location.reload();
                        } else {
                            alert("❌ Error al cancelar: " + (data.error || "Inténtalo de nuevo."));
                        }
                    })
                    .catch(() => alert("❌ Error de red."));
            }
        }
    </script>


    <?php
} catch (PDOException $e) {
    echo '<div class="container mt-3"><p class="text-center text-danger">Error en la consulta: '.$e->getMessage().'</p></div>';
}
?>
