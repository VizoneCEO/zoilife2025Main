<?php
include '../../back/db/connection.php';

try {
    // Consulta con JOIN para obtener el nombre del cliente
    $query = "SELECT 
    c.id_cotizacion, 
    cl.nombre AS cliente, 
    u.nombre AS vendedora,
    c.fecha_creacion, 
    c.fecha_entrega,
    c.costo_envio,
    c.total,
d.alcaldia_municipio AS municipio

FROM cotizaciones c
JOIN clientes cl ON c.id_cliente = cl.id_cliente
JOIN usuarios u ON c.id_usuario = u.id_usuario
LEFT JOIN direcciones d ON c.id_direccion = d.id_direccion
WHERE c.estatus = 'pendiente'
ORDER BY c.fecha_creacion DESC";



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

            padding: 4px 8px;
            margin: 2px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 10px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .details-btn { background-color: #6C757D; color: white; }  /* Nuevo botón de detalles */
        .edit-btn { background-color: #FFC107; color: white; }
        .print-btn { background-color: #17A2B8; color: white; }
        .assign-btn { background-color: #28A745; color: white; }
        .delete-btn { background-color: #DC3545; color: white; }

        .btn i {
            font-size: 16px;
        }

    </style>

    <div class="container mt-4">
        <h1 class="mb-4 text-center">Pedidos en Cotización - Pendientes</h1>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <td>ID Cotización</td>
                    <td>Cliente</td>
                    <td>Municipio/Alcaldia</td>
                    <td>Vendedora</td>
                    <td>Fecha de Creación</td>
                    <td>Fecha de Entrega</td>
                    <td>Total</td>
                    <td>Acciones</td>
                </tr>
                </thead>
                <tbody>
                <?php
                if (count($result) > 0) {
                    foreach ($result as $row) {
                        $id = $row['id_cotizacion'];
                        $cliente = htmlspecialchars($row['cliente']);
                        $municipio = htmlspecialchars($row['municipio'] ?? 'Sin dirección');
                        $vendedora = htmlspecialchars($row['vendedora']);
                        $fecha = $row['fecha_creacion'];
                        $fecha_E = $row['fecha_entrega'];
                        $costo_envio = $row['costo_envio'];
                        $s_total = $row['total'];
                        $totalP = $costo_envio+$s_total;
                        $total =number_format($totalP, 2);


                        echo "<tr>
            <td>$id</td>
            <td>$cliente</td>
            <td>$municipio</td>
            <td>$vendedora</td>
            <td>$fecha</td>
            <td> $fecha_E</td>
    

            <td>$$total</td>
            <td>
                <button class='btn details-btn' data-bs-toggle='modal' data-bs-target='#modalDetalles' onclick='cargarDetalles($id)'>
                    <i class='bi bi-eye'></i> Detalles
                </button>

                <a href='editarPedidoVendido.php?id=$id' class='btn edit-btn'>
                    <i class='bi bi-pencil'></i> Editar
                </a>

                <button class='btn print-btn' onclick='generarPDF($id)'>
                    <i class='bi bi-printer'></i> Imprimir
                </button>

                <button class='btn assign-btn' onclick='asignarPedido($id)'>
                    <i class='bi bi-check-circle'></i> Asignar
                </button>

                <button class='btn delete-btn' onclick='eliminarPedido($id)'>
                    <i class='bi bi-trash'></i> Eliminar
                </button>
            </td>
        </tr>";
                    }
                } else {
                    echo '<tr><td colspan="5" class="text-center">No hay pedidos pendientes.</td></tr>';
                }
                ?>

                </tbody>

            </table>
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






    <!-- Modal de Confirmación -->
    <div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEliminarLabel">Confirmar eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas eliminar esta cotización? Esta acción no se puede deshacer.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmarEliminar">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let idEliminar = null;

        function eliminarPedido(id) {
            idEliminar = id;
            let modalEliminar = new bootstrap.Modal(document.getElementById('modalEliminar'));
            modalEliminar.show();
        }

        document.getElementById('confirmarEliminar').addEventListener('click', function () {
            if (idEliminar) {
                fetch(`../../back/produccion/eliminar_pedido.php?id=${idEliminar}`, {
                    method: 'POST'
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Pedido eliminado correctamente.");
                            location.reload(); // Recargar la tabla
                        } else {
                            alert("Error al eliminar el pedido.");
                        }
                    })
                    .catch(error => console.error("Error en la solicitud:", error));
            }
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

    <!-- Modal para Asignar Pedido -->
    <div class="modal fade" id="modalAsignar" tabindex="-1" aria-labelledby="modalAsignarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Asignar Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p id="mensajeAsignacion"></p>
                    <div class="mb-3">
                        <label for="selectDeliver" class="form-label">Selecciona un repartidor:</label>
                        <select id="selectDeliver" class="form-select">
                            <option value="">-- Selecciona --</option>
                            <?php
                            // Traer usuarios tipo 'delivery' desde la BD
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
        let idCotizacionAAsignar = null; // Definir variable global

        function asignarPedido(id) {
            let mensajeAsignacion = document.getElementById('mensajeAsignacion');
            let selectDeliver = document.getElementById('selectDeliver');

            // Reiniciar el select de repartidores
            selectDeliver.value = '';

            // Mostrar mensaje de carga mientras se obtiene la información
            mensajeAsignacion.innerText = "Cargando información del pedido...";

            console.log(`🔵 Enviando solicitud para obtener cliente con ID: ${id}`);

            // Obtener el nombre del cliente desde el backend
            fetch(`../../back/produccion/obtener_cliente.php?id=${id}`)
                .then(response => {
                    console.log("🔵 Respuesta obtenida del servidor:", response);
                    if (!response.ok) {
                        throw new Error(`⚠️ Error en la respuesta del servidor (Status: ${response.status})`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("✅ Datos recibidos del servidor:", data);
                    if (data.success) {
                        mensajeAsignacion.innerText = `Estás por asignar el pedido de ${data.cliente} al repartidor.`;
                    } else {
                        mensajeAsignacion.innerText = "⚠️ Error al obtener la información del pedido.";
                        console.error("⚠️ Error en la respuesta del servidor:", data.error);
                    }
                })
                .catch(error => {
                    console.error("❌ Error al obtener la información del pedido:", error);
                    mensajeAsignacion.innerText = "❌ Error al cargar los datos.";
                });

            // Guardar el ID de la cotización y abrir el modal
            idCotizacionAAsignar = id;
            const modal = new bootstrap.Modal(document.getElementById('modalAsignar'));
            modal.show();
        }

        document.getElementById('confirmarAsignacion').addEventListener('click', function () {
            const idDeliver = document.getElementById('selectDeliver').value;

            if (!idDeliver) {
                alert("Selecciona un repartidor antes de continuar.");
                return;
            }

            let formData = new FormData();
            formData.append("id_cotizacion", idCotizacionAAsignar);
            formData.append("id_usuario", idDeliver);

            fetch("../../back/produccion/asignar_pedido.php", {
                method: "POST",
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    console.log("Respuesta del backend:", data);
                    if (data.success) {
                        alert("Pedido asignado correctamente.");
                        location.reload();
                    } else {
                        alert("Error al asignar el pedido. Inténtalo de nuevo.");
                    }
                })
                .catch(error => {
                    console.error("Error al asignar el pedido:", error);
                    alert("Error de comunicación con el servidor.");
                });
        });



    </script>



    <?php
} catch (PDOException $e) {
    echo '<div class="container mt-3"><p class="text-center text-danger">Error en la consulta: ' . $e->getMessage() . '</p></div>';
}
?>
