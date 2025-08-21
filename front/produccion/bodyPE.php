<?php
include '../../back/db/connection.php';

try {
    $query = "SELECT 
                c.id_cotizacion, 
                cl.nombre AS cliente, 
                c.fecha_creacion,
                c.costo_envio,
                c.total,
                c.receptorPedido,
                c.metodoPago,
                c.evidencia,
                u.nombre AS repartidor,
                v.nombre AS vendedora
              FROM cotizaciones c
              JOIN clientes cl ON c.id_cliente = cl.id_cliente
              JOIN asignaciones_entrega ae ON c.id_cotizacion = ae.id_cotizacion
              JOIN usuarios u ON ae.id_usuario = u.id_usuario
              LEFT JOIN usuarios v ON c.id_usuario = v.id_usuario
              WHERE c.estatus = 'entregado'
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

        .details-btn { background-color: #6C757D; color: white; }
        .print-btn { background-color: #17A2B8; color: white; }
        .reassign-btn { background-color: #FFC107; color: white; }
    </style>

    <div class="container mt-4">
        <h1 class="mb-4 text-center">Pedidos Entregados</h1>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <td>ID</td>
                    <td>Cliente</td>
                    <td>Fecha</td>
                    <td>Total</td>
                    <td>Repartidor</td>
                    <td>Recibió</td>
                    <td>Método Pago</td>
                    <td>Vendedora</td>
                    <td>Acciones</td>
                </tr>
                </thead>
                <tbody>
                <?php
                if (count($result) > 0) {
                    foreach ($result as $row) {
                        $id = $row['id_cotizacion'];
                        $cliente = htmlspecialchars($row['cliente']);
                        $fecha = $row['fecha_creacion'];
                        $costo_envio = $row['costo_envio'];
                        $s_total = $row['total'];
                        $totalP = $costo_envio+$s_total;
                        $total =number_format($totalP, 2);

                        $repartidor = htmlspecialchars($row['repartidor']);
                        $recibe = htmlspecialchars($row['receptorPedido']);
                        $metodo = htmlspecialchars($row['metodoPago']);
                        $vendedora = htmlspecialchars($row['vendedora']);
                        $evidencia = '../../front/' . ltrim($row['evidencia'], '/');

                        echo "<tr>
                        <td>$id</td>
                        <td>$cliente</td>
                        <td>$fecha</td>
                        <td>$$total</td>
                        <td>$repartidor</td>
                        <td>$recibe</td>
                        <td>$metodo</td>
                        <td>$vendedora</td>
                        <td>
                            <button class='btn details-btn' data-bs-toggle='modal' data-bs-target='#modalDetalles' onclick='cargarDetalles($id)'>
                                <i class='bi bi-eye'></i> Detalles
                            </button>
                            <button class='btn print-btn' onclick='generarPDF($id)'>
                                <i class='bi bi-printer'></i> Imprimir
                            </button>
                            <button class='btn reassign-btn' onclick='reasignarPedido($id)'>
                                <i class='bi bi-arrow-repeat'></i> Asignar Comisión
                            </button>
                            <button class='btn btn-dark' onclick='verEvidencia(\"$evidencia\")'>
                            <i class='bi bi-image'></i> Ver Evidencia                       
                  
                            </button>

                            
                        </td>
                    </tr>";
                    }
                } else {
                    echo '<tr><td colspan="9" class="text-center">No hay pedidos entregados aún.</td></tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>


    <!-- Modal Ver Evidencia -->
    <div class="modal fade" id="modalEvidencia" tabindex="-1" aria-labelledby="modalEvidenciaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Evidencia de Entrega</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center">
                    <!-- NUEVA LÍNEA: muestra la URL -->
                    <p class="text-muted small" id="urlEvidenciaText"></p>

                    <img id="imgEvidencia" src="" alt="Evidencia" class="img-fluid rounded shadow-sm" style="max-height: 500px; object-fit: contain;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>



    <script>
        function verEvidencia(url) {
            if (!url || url.trim() === "") {
                alert("⚠️ No hay evidencia disponible para este pedido.");
                return;
            }


            document.getElementById("imgEvidencia").src = url;

            const modal = new bootstrap.Modal(document.getElementById('modalEvidencia'));
            modal.show();
        }


    </script>




    <!-- Modal Detalles -->
    <div class="modal fade" id="modalDetalles" tabindex="-1" aria-labelledby="modalDetallesLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles de la Cotización</h5>
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

    <script>
        function cargarDetalles(id) {
            document.getElementById("detallesContenido").innerHTML = "<p class='text-center'>Cargando detalles...</p>";
            fetch(`../../back/produccion/detalles_pedido.php?id=${id}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById("detallesContenido").innerHTML = data;
                })
                .catch(error => {
                    document.getElementById("detallesContenido").innerHTML = "<p class='text-danger text-center'>Error al cargar detalles.</p>";
                });
        }

        function generarPDF(id) {
            let url = `../../back/produccion/generar_pdf.php?id=${id}`;
            window.open(url, '_blank');
        }

        function reasignarPedido(id) {
            alert("🛠  #" + id);
        }
    </script>

    <?php
} catch (PDOException $e) {
    echo '<div class="container mt-3"><p class="text-center text-danger">Error en la consulta: ' . $e->getMessage() . '</p></div>';
}
?>
