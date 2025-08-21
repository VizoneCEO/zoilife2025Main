<?php
include '../../back/db/connection.php';

try {
    $query = "SELECT 
            c.id_cotizacion, 
            cl.nombre AS cliente, 
            c.fecha_creacion, 
            c.fecha_entrega,
            c.costo_envio,
            c.total,
            CONCAT(u.nombre, ' ', u.apellido) AS repartidor
          FROM cotizaciones c
          JOIN clientes cl ON c.id_cliente = cl.id_cliente
          JOIN asignaciones_entrega ae ON c.id_cotizacion = ae.id_cotizacion
          JOIN usuarios u ON ae.id_usuario = u.id_usuario
          WHERE c.estatus = 'en curso'
          ORDER BY u.nombre, c.id_cotizacion ASC";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


    ?>

    <div class="container mt-3">
        <a href="dashboard.php" class="btn btn-success">← Regresar al Dashboard</a>
    </div>

    <style>
        .table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .table th, .table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #8BC34A;
            color: white;
            font-size: 16px;
        }

        td {
            background-color: #f9f9f9;
            font-size: 14px;
        }

        /* Botones */
        .btn {
            padding: 8px 12px;
            margin: 2px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .details-btn { background-color: #6C757D; color: white; }  /* Nuevo botón de detalles */
        .reassign-btn { background-color: #FFC107; color: white; }
        .print-btn { background-color: #17A2B8; color: white; }
        .assign-btn { background-color: #28A745; color: white; }
        .delete-btn { background-color: #DC3545; color: white; }

        .btn i {
            font-size: 16px;
        }


    </style>
    <style>
        .cancel-btn {
            background-color: #dc3545;
            color: white;
        }
    </style>


    <div class="container mt-4">
        <h1 class="mb-4 text-center">Pedidos en Reparto</h1>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <td>ID Cotización</td>
                    <td>Cliente</td>
                    <td>Fecha de Creación</td>
                    <td>Fecha Entrega</td>
                    <td>Total</td>
                    <td>Acciones</td>
                </tr>
                </thead>
                <tbody>
                <?php
                $repartidor_actual = '';
                $colores = ['#ffffff', '#f2f2f2']; // blanco y gris claro
                $colorIndex = 0;

                if (count($result) > 0) {
                    foreach ($result as $row) {
                        $id = $row['id_cotizacion'];
                        $cliente = htmlspecialchars($row['cliente']);
                        $fecha = $row['fecha_creacion'];
                        $total = number_format($row['total'], 2);
                        $costo_envio = $row['costo_envio'];
                        $s_total = $row['total'];
                        $totalP = $costo_envio+$s_total;
                        $total =number_format($totalP, 2);
                        $fecha_E = $row['fecha_entrega'];
                        $repartidor = htmlspecialchars($row['repartidor']);

                        if ($repartidor !== $repartidor_actual) {
                            $repartidor_actual = $repartidor;
                            $colorIndex = 1 - $colorIndex;

                            echo "<tr style='background-color: #c6e6c3; font-weight: bold;'>
                    <td colspan='6'>REPARTIDOR: $repartidor</td>
                  </tr>";
                        }

                        $rowColor = $colores[$colorIndex];

                        echo "<tr style='background-color: $rowColor;'>
                <td>$id</td>
                <td>$cliente</td>
                <td>$fecha</td>
                <td> $fecha_E</td>
                <td>$$total</td>
                <td>
                    <button class='btn details-btn' data-bs-toggle='modal' data-bs-target='#modalDetalles' onclick='cargarDetalles($id)'>
                        <i class='bi bi-eye'></i> Detalles
                    </button>
                    <button class='btn print-btn' onclick='generarPDF($id)'>
                        <i class='bi bi-printer'></i> Imprimir
                    </button>
                    <button class='btn reassign-btn' onclick='reasignarPedido($id)'>
    <i class='bi bi-arrow-repeat'></i> Re Asignar
</button>

<button class='btn cancel-btn' onclick='mostrarModalCancelar($id)'>
    <i class='bi bi-x-circle'></i> Cancelar
</button>

                </td>
              </tr>";
                    }
                } else {
                    echo '<tr><td colspan="6" class="text-center">No hay pedidos pendientes.</td></tr>';
                }
                ?>

                </tbody>

            </table>
        </div>
    </div>



    <!-- Modal para Cancelar Pedido -->
    <div class="modal fade" id="modalCancelar" tabindex="-1" aria-labelledby="modalCancelarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Cancelación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas <strong>cancelar</strong> esta cotización? Esta acción no se puede deshacer.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmarCancelar">Sí, Cancelar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal para Detalles de Cotización -->
    <div class="modal fade" id="modalDetalles" tabindex="-1" aria-labelledby="modalDetallesLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetallesLabel">Detalles de la Cotización</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div id="detallesContenido">
                        <p class="text-center">Cargando detalles...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>




    <!-- Modal para Asignar o Re Asignar Pedido -->
    <div class="modal fade" id="modalAsignar" tabindex="-1" aria-labelledby="modalAsignarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAsignarLabel">Asignar Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p id="mensajeAsignacion"></p>
                    <div class="mb-3">
                        <label for="selectDeliver" class="form-label">Selecciona un repartidor:</label>
                        <select id="selectDeliver" class="form-select">
                            <option value="">-- Selecciona --</option>
                            <?php
                            // Traer usuarios tipo 'deliver' desde la BD
                            $usuarios = $conn->query("SELECT id_usuario, nombre FROM usuarios WHERE rol = 'deliver'");
                            foreach ($usuarios as $usuario) {
                                echo "<option value='{$usuario['id_usuario']}'>{$usuario['nombre']}</option>";
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





    <!-- Modal de Confirmación de Entrega -->
    <div class="modal fade" id="modalEntregar" tabindex="-1" aria-labelledby="modalEntregarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEntregarLabel">Confirmar entrega</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas marcar esta cotización como <strong>en curso</strong> y entregar el producto al repartidor?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="confirmarEntrega">Sí, entregar</button>
                </div>
            </div>
        </div>
    </div>



    <script>
        let idCotizacionAEntregar = null;

        function entregar(id) {
            idCotizacionAEntregar = id;
            const modal = new bootstrap.Modal(document.getElementById('modalEntregar'));
            modal.show();
        }

        document.getElementById('confirmarEntrega').addEventListener('click', function () {
            if (!idCotizacionAEntregar) return;

            fetch(`../../back/produccion/marcar_en_curso.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id_cotizacion=${idCotizacionAEntregar}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("✅ Cotización marcada como 'en curso'.");
                        location.reload();
                    } else {
                        alert("❌ Error al marcar como entregado: " + (data.error || "Inténtalo de nuevo."));
                    }
                })
                .catch(error => {
                    console.error("❌ Error en la solicitud:", error);
                    alert("Error de comunicación con el servidor.");
                });
        });
    </script>











    <script >
        function cargarDetalles(id) {
            let detallesContenido = document.getElementById("detallesContenido");
            detallesContenido.innerHTML = "<p class='text-center'>Cargando detalles...</p>";

            fetch(`../../back/produccion/detalles_pedido.php?id=${id}`)
                .then(response => response.text())
                .then(data => {
                    detallesContenido.innerHTML = data;
                })
                .catch(error => {
                    detallesContenido.innerHTML = "<p class='text-center text-danger'>Error al cargar detalles.</p>";
                });
        }


    </script>

    <script>
        function generarPDF(id) {
            let url = `../../back/produccion/generar_pdf.php?id=${id}`;
            window.open(url, '_blank');
        }
    </script>



    <script>
        let idCotizacionAAsignar = null;

        // Función para abrir el modal y preparar la info
        function reasignarPedido(id) {
            idCotizacionAAsignar = id;

            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('modalAsignar'));
            modal.show();

            // Esperar que el modal esté cargado visualmente
            setTimeout(() => {
                const modalTitle = document.getElementById('modalAsignarLabel');
                const mensajeAsignacion = document.getElementById('mensajeAsignacion');
                const selectDeliver = document.getElementById('selectDeliver');

                if (!modalTitle || !mensajeAsignacion || !selectDeliver) {
                    console.error("❌ No se encontraron elementos del modal.");
                    return;
                }

                modalTitle.innerText = "Re Asignar Pedido";
                mensajeAsignacion.innerText = "Cargando información del pedido...";
                selectDeliver.value = "";

                // Obtener info del cliente
                fetch(`../../back/produccion/obtener_cliente.php?id=${id}`)
                    .then(response => {
                        if (!response.ok) throw new Error("Error en la respuesta del servidor.");
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            mensajeAsignacion.innerText = `Estás por re asignar el pedido de ${data.cliente} a otro repartidor.`;
                        } else {
                            mensajeAsignacion.innerText = "⚠️ No se pudo obtener el nombre del cliente.";
                        }
                    })
                    .catch(error => {
                        console.error("❌ Error al obtener el cliente:", error);
                        mensajeAsignacion.innerText = "Error al cargar los datos.";
                    });
            }, 200); // Esperamos que se pinte el DOM del modal
        }

        // Función que se ejecuta al confirmar asignación/reasignación
        document.getElementById('confirmarAsignacion').addEventListener('click', function () {
            const idDeliver = document.getElementById('selectDeliver').value;

            if (!idDeliver) {
                alert("⚠️ Selecciona un repartidor antes de continuar.");
                return;
            }

            let formData = new FormData();
            formData.append("id_cotizacion", idCotizacionAAsignar);
            formData.append("id_usuario", idDeliver);

            fetch("../../back/produccion/reasignar_pedido.php", {
                method: "POST",
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    console.log("Respuesta del backend:", data);
                    if (data.success) {
                        alert("✅ Pedido reasignado correctamente.");
                        location.reload();
                    } else {
                        alert("❌ Error al reasignar el pedido: " + (data.error || "Inténtalo de nuevo."));
                    }
                })
                .catch(error => {
                    console.error("❌ Error en la solicitud:", error);
                    alert("Error de comunicación con el servidor.");
                });
        });
    </script>


    <script>
        let idACancelar = null;

        function mostrarModalCancelar(id) {
            idACancelar = id;
            const modal = new bootstrap.Modal(document.getElementById('modalCancelar'));
            modal.show();
        }

        document.getElementById('confirmarCancelar').addEventListener('click', function () {
            if (!idACancelar) return;

            fetch(`../../back/produccion/cancelar_cotizacion.php?id=${idACancelar}`, {
                method: 'POST'
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Cotización cancelada correctamente.");
                        location.reload();
                    } else {
                        alert("Error al cancelar la cotización.");
                    }
                })
                .catch(error => {
                    console.error("Error en la solicitud:", error);
                    alert("Error en el servidor.");
                });
        });
    </script>


    <?php
} catch (PDOException $e) {
    echo '<div class="container mt-3"><p class="text-center text-danger">Error en la consulta: ' . $e->getMessage() . '</p></div>';
}
?>
