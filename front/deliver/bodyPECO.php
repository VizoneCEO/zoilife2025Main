<?php
include '../../back/db/connection.php';


$id_repartidor = $_SESSION['user_id']; // variable del repartidor logueado

$query = "SELECT 
    pw.id_pedido_web,
    pw.total,
    pw.fecha_creacion,
    pw.nombre,
    pw.apellido_paterno,
    pw.telefono,
    pw.calle,
    pw.numero,
    pw.colonia,
    pw.municipio,
    pw.codigo_postal,
    pw.ciudad,
    pw.estado
FROM pedidos_web pw
JOIN asignaciones_web aw ON pw.id_pedido_web = aw.id_pedido_web
WHERE pw.estatus_pedido = 'en curso' AND aw.id_usuario = :id_repartidor";

$stmt = $conn->prepare($query);
$stmt->bindParam(':id_repartidor', $id_repartidor, PDO::PARAM_INT);
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .card {
        border-left: 5px solid #8BC34A;
        transition: transform 0.2s ease;
    }
    .card:hover { transform: scale(1.01); }
    .btn { font-size: 14px; }
</style>

<div class="container mt-3">
    <a href="dashboard.php" class="btn btn-success">← Regresar al Dashboard</a>
</div>

<div class="container mt-4">
    <h2 class="text-center mb-4">Tus Pedidos Web en Curso</h2>

    <div class="row g-4">
        <?php foreach ($pedidos as $pedido):
            $cliente = $pedido['nombre'] . ' ' . $pedido['apellido_paterno'];
            $direccion = $pedido['calle'] . ' ' . $pedido['numero'] . ', Col. ' . $pedido['colonia'] .
                ', ' . $pedido['municipio'] . ', CP ' . $pedido['codigo_postal'] .
                ', ' . $pedido['ciudad'] . ', ' . $pedido['estado'];

            $id = $pedido['id_pedido_web'];
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Pedido WEB-<?= $id ?></h5>
                        <p><strong>Cliente:</strong> <?= htmlspecialchars($cliente) ?></p>
                        <p><strong>Teléfono:</strong> <?= htmlspecialchars($pedido['telefono']) ?></p>
                        <p><strong>Dirección:</strong><br><?= htmlspecialchars($direccion) ?></p>

                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($direccion) ?>"
                               target="_blank" class="btn btn-outline-success w-100">📍 Mapa</a>

                            <a href="https://wa.me/52<?= $pedido['telefono'] ?>"
                               target="_blank" class="btn btn-outline-primary w-100">💬 WhatsApp</a>

                            <a href="tel:+52<?= $pedido['telefono'] ?>" class="btn btn-outline-dark w-100">📞 Llamar</a>

                            <button class="btn btn-secondary w-100" data-bs-toggle="modal" data-bs-target="#modalDetalles" onclick="cargarDetallesWeb(<?= $id ?>)">
                                🔍 Detalles
                            </button>

                            <button class="btn btn-outline-warning w-100" onclick="subirEvidenciaWeb(<?= $id ?>)">📷 Evidencia</button>

                            <button class="btn btn-success w-100" onclick="entregarPedidoWeb(<?= $id ?>)">✅ Entregar</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>








<!-- Modal Subir Evidencia Web -->
<div class="modal fade" id="modalEvidenciaWeb" tabindex="-1" aria-labelledby="modalEvidenciaWebLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEvidenciaWeb" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEvidenciaWebLabel">Subir Evidencia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="evidenciaIdPedidoWeb" name="id_pedido_web">

                    <div class="mb-3">
                        <label for="fotoEvidenciaWeb" class="form-label">Selecciona una foto:</label>
                        <input class="form-control" type="file" id="fotoEvidenciaWeb" name="foto" accept="image/*" required>
                    </div>

                    <div class="text-center">
                        <img id="previewEvidenciaWeb" src="" class="img-fluid rounded shadow-sm d-none" style="max-height: 300px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">📤 Subir Evidencia</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Confirmar Entrega Web -->
<div class="modal fade" id="modalConfirmarEntregaWeb" tabindex="-1" aria-labelledby="modalLabelEntregaWeb" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabelEntregaWeb">Confirmar entrega</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="modalBodyEntregaWeb">
                <div id="infoTotalEntregaWeb"></div>

                <div class="mb-3">
                    <label for="inputQuienRecibeWeb" class="form-label">¿Quién recibe el pedido?</label>
                    <input type="text" class="form-control" id="inputQuienRecibeWeb" placeholder="Nombre completo">
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnConfirmarEntregaWeb">Confirmar entrega</button>
            </div>
        </div>
    </div>
</div>

<script>
    // ========= SUBIR EVIDENCIA WEB =========
    function subirEvidenciaWeb(id) {
        document.getElementById("evidenciaIdPedidoWeb").value = id;
        document.getElementById("fotoEvidenciaWeb").value = '';
        document.getElementById("previewEvidenciaWeb").classList.add('d-none');

        // Cargar evidencia si ya existe
        fetch(`../../back/deliver/obtener_evidencia_web.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.evidencia) {
                    const preview = document.getElementById("previewEvidenciaWeb");
                    preview.src = `../../front/${data.evidencia}`;
                    preview.classList.remove('d-none');
                }
            });

        new bootstrap.Modal(document.getElementById('modalEvidenciaWeb')).show();
    }

    // Preview
    document.getElementById("fotoEvidenciaWeb").addEventListener("change", function () {
        const file = this.files[0];
        const preview = document.getElementById("previewEvidenciaWeb");

        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        }
    });

    // Submit evidencia
    document.getElementById("formEvidenciaWeb").addEventListener("submit", e => {
        e.preventDefault();
        const formData = new FormData(e.target);

        fetch("../../back/deliver/subir_evidencia_web.php", { method: "POST", body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert("✅ Evidencia subida correctamente.");
                    bootstrap.Modal.getInstance(document.getElementById('modalEvidenciaWeb')).hide();
                } else {
                    alert("❌ Error: " + (data.error || "No se pudo subir la imagen."));
                }
            });
    });

    // ========= ENTREGAR PEDIDO WEB =========
    let idPedidoWebSeleccionado = null;

    function entregarPedidoWeb(id) {
        idPedidoWebSeleccionado = id;

        fetch(`../../back/deliver/obtener_total_web.php?id=${id}`)
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const total = parseFloat(data.total) || 0;
                    const envio = parseFloat(data.costo_envio) || 0;
                    const totalCobrar = total + envio;

                    document.getElementById('infoTotalEntregaWeb').innerHTML =
                        `<p>💰 Total del pedido: <strong>$${total.toFixed(2)}</strong></p>
                         <p>🚚 Costo de envío: <strong>$${envio.toFixed(2)}</strong></p>
                         <hr>
                         <p>Total: <strong>$${totalCobrar.toFixed(2)}</strong></p>`;

                    new bootstrap.Modal(document.getElementById('modalConfirmarEntregaWeb')).show();
                } else {
                    alert("❌ No se pudo obtener el total del pedido.");
                }
            });
    }

    // Confirmar entrega
    document.getElementById('btnConfirmarEntregaWeb').addEventListener('click', () => {
        const quienRecibe = document.getElementById('inputQuienRecibeWeb').value.trim();

        if (!quienRecibe) {
            alert("❌ Por favor ingresa el nombre de quien recibe.");
            return;
        }

        const datos = new URLSearchParams();
        datos.append("id_pedido_web", idPedidoWebSeleccionado);
        datos.append("receptorPedido", quienRecibe);

        fetch("../../back/deliver/marcar_entregado_web.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: datos.toString()
        })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert("✅ Pedido web entregado.");
                    location.reload();
                } else {
                    alert("❌ Error: " + (data.error || "No se pudo actualizar."));
                }
            });
    });

</script>







<!-- Modal Detalles Pedido -->
<div class="modal fade" id="modalDetalles" tabindex="-1" aria-labelledby="modalDetallesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetallesLabel">Detalles del Pedido Web</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div id="detallesContenidoWeb">
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
    function cargarDetallesWeb(id) {
        let detallesContenido = document.getElementById("detallesContenidoWeb");
        detallesContenido.innerHTML = "<p class='text-center'>Cargando detalles...</p>";

        fetch(`../../back/produccion/detalles_pedido_web.php?id=${id}`)
            .then(r => r.text())
            .then(data => detallesContenido.innerHTML = data)
            .catch(() => detallesContenido.innerHTML = "<p class='text-danger text-center'>Error al cargar detalles.</p>");
    }
</script>
