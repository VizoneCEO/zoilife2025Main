<?php
include '../../back/db/connection.php';


$id_repartidor = $_SESSION['user_id']; // o usa el nombre de variable que tengas para el usuario logueado

$query = "SELECT 
    c.id_cotizacion,
    c.total,
    c.fecha_creacion,
    cl.nombre,
    cl.apellido_paterno,
    cl.telefono1,
    d.calle,
    d.numero_casa,
    d.colonia,
    d.alcaldia_municipio,
    d.codigo_postal,
    d.ciudad,
    d.estado
FROM cotizaciones c
JOIN clientes cl ON c.id_cliente = cl.id_cliente
JOIN direcciones d ON c.id_direccion = d.id_direccion
JOIN asignaciones_entrega ae ON c.id_cotizacion = ae.id_cotizacion
WHERE c.estatus = 'en curso' AND ae.id_usuario = :id_repartidor";



$stmt = $conn->prepare($query);
$stmt->bindParam(':id_repartidor', $id_repartidor);
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .card {
        border-left: 5px solid #8BC34A;
        transition: transform 0.2s ease;
    }

    .card:hover {
        transform: scale(1.01);
    }

    .btn {
        font-size: 14px;
    }
</style>

<div class="container mt-3">
    <a href="dashboard.php" class="btn btn-success">
        ← Regresar al Dashboard
    </a>
</div>

<div class="container mt-4">
    <h2 class="text-center mb-4">Tus Pedidos en Curso</h2>

    <div class="row g-4">
        <?php foreach ($pedidos as $pedido):
            $cliente = $pedido['nombre'] . ' ' . $pedido['apellido_paterno'];
            $direccion = $pedido['calle'] . ' ' . $pedido['numero_casa'] . ', Col. ' . $pedido['colonia'] .
                ', ' . $pedido['alcaldia_municipio'] . ', CP ' . $pedido['codigo_postal'] .
                ', ' . $pedido['ciudad'] . ', ' . $pedido['estado'];

            $id = $pedido['id_cotizacion'];
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Pedido #<?= $id ?></h5>
                        <p><strong>Cliente:</strong> <?= htmlspecialchars($cliente) ?></p>
                        <p><strong>Teléfono:</strong> <?= htmlspecialchars($pedido['telefono1']) ?></p>
                        <p><strong>Dirección:</strong><br><?= htmlspecialchars($direccion) ?></p>


                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($direccion) ?>"
                               target="_blank" class="btn btn-outline-success w-100">
                                📍 Mapa
                            </a>

                            <a href="https://wa.me/52<?= $pedido['telefono1'] ?>"
                               target="_blank" class="btn btn-outline-primary w-100">
                                💬 WhatsApp
                            </a>

                            <a href="tel:+52<?= $pedido['telefono1'] ?>"
                               class="btn btn-outline-dark w-100">
                                📞 Llamar
                            </a>
                            <button class="btn btn-secondary w-100" data-bs-toggle="modal" data-bs-target="#modalDetalles" onclick="cargarDetalles(<?= $id ?>)">
                                🔍 Detalles
                            </button>

                            <button class="btn btn-outline-warning w-100" onclick="subirEvidencia(<?= $id ?>)">
                                📷 Evidencia
                            </button>

                            <button class="btn btn-success w-100" onclick="entregarPedido(<?= $id ?>)">
                                ✅ Entregar
                            </button>
                        </div>
                    </div>

                </div>
            </div>



        <?php endforeach; ?>
    </div>
</div>



<!-- Modal para Detalles de Cotización -->
<div class="modal fade" id="modalDetalles" tabindex="-1" aria-labelledby="modalDetallesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetallesLabel">Detalles del Pedido</h5>
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






<!-- Modal Subir Evidencia -->
<div class="modal fade" id="modalEvidencia" tabindex="-1" aria-labelledby="modalEvidenciaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEvidencia" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEvidenciaLabel">Subir Evidencia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="evidenciaIdPedido" name="id_cotizacion">

                    <div class="mb-3">
                        <label for="fotoEvidencia" class="form-label">Selecciona una foto:</label>
                        <input class="form-control" type="file" id="fotoEvidencia" name="foto" accept="image/*" required>
                    </div>

                    <div class="text-center">
                        <img id="previewEvidencia" src="" class="img-fluid rounded shadow-sm d-none" style="max-height: 300px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">📤 Subir Evidencia</button>
                </div>
            </form>
        </div>
    </div>
</div>





<!-- Modal -->
<div class="modal fade" id="modalConfirmarEntrega" tabindex="-1" aria-labelledby="modalLabelEntrega" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabelEntrega">Confirmar entrega</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="modalBodyEntrega">
                <!-- Aquí se mostrará el monto total -->
                <div id="infoTotalEntrega"></div>

                <div class="mb-3">
                    <label for="inputQuienRecibe" class="form-label">¿Quién recibe el pedido?</label>
                    <input type="text" class="form-control" id="inputQuienRecibe" placeholder="Nombre completo">
                </div>

                <div class="mb-3">
                    <label for="inputMetodoPago" class="form-label">Método de pago</label>
                    <select class="form-select" id="inputMetodoPago" required>
                        <option value="" selected disabled>Selecciona un método de pago</option>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="Tarjeta">Tarjeta</option>
                        <option value="Paypal">PayPal</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnConfirmarEntrega">Confirmar entrega</button>
            </div>
        </div>
    </div>
</div>









<script>
    function cargarDetalles(id) {
        const detallesContenido = document.getElementById("detallesContenido");
        detallesContenido.innerHTML = "<p class='text-center'>Cargando detalles...</p>";

        fetch(`../../back/produccion/detalles_pedido.php?id=${id}`)
            .then(response => response.text())
            .then(data => {
                detallesContenido.innerHTML = data;
            })
            .catch(error => {
                detallesContenido.innerHTML = "<p class='text-danger text-center'>Error al cargar los detalles del pedido.</p>";
                console.error("Error:", error);
            });
    }
</script>


<script>

    function subirEvidencia(id) {
        const evidenciaInput = document.getElementById("fotoEvidencia");
        const preview = document.getElementById("previewEvidencia");

        // Limpiar valores actuales
        document.getElementById("evidenciaIdPedido").value = id;
        evidenciaInput.value = '';
        preview.src = '';
        preview.classList.add('d-none');

        // Verificar si ya existe una evidencia
        fetch(`../../back/deliver/obtener_evidencia.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.evidencia) {
                    preview.src = `../../front/${data.evidencia}`; // RUTA CORREGIDA
                    preview.classList.remove('d-none');
                }
            })
            .catch(err => {
                console.error("Error al obtener evidencia previa:", err);
            });

        const modal = new bootstrap.Modal(document.getElementById('modalEvidencia'));
        modal.show();
    }

    // Preview en tiempo real
    document.getElementById("fotoEvidencia").addEventListener("change", function () {
        const file = this.files[0];
        const preview = document.getElementById("previewEvidencia");

        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        } else {
            preview.classList.add('d-none');
        }
    });

    // Enviar formulario
    document.getElementById("formEvidencia").addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch("../../back/deliver/subir_evidencia.php", {
            method: "POST",
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("✅ Evidencia subida correctamente.");
                    bootstrap.Modal.getInstance(document.getElementById('modalEvidencia')).hide();
                } else {
                    alert("❌ Error: " + (data.error || "No se pudo subir la imagen."));
                }
            })
            .catch(err => {
                console.error("❌ Error:", err);
                alert("Error al comunicar con el servidor.");
            });
    });

</script>




<script>


    let idPedidoSeleccionado = null;

    function entregarPedido(id) {
        // Guardar id en variable global
        idPedidoSeleccionado = id;

        // Obtener total y costo_envio
        fetch(`../../back/deliver/obtener_total.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const total = parseFloat(data.total) || 0;
                    const envio = parseFloat(data.costo_envio) || 0;
                    const totalCobrar = total + envio;

                    document.getElementById('infoTotalEntrega').innerHTML =
                        `<p>💰 Total del pedido: <strong>$${total.toFixed(2)}</strong></p>
     <p>🚚 Costo de envío: <strong>$${envio.toFixed(2)}</strong></p>
     <hr>
     <p>Total a cobrar: <strong>$${totalCobrar.toFixed(2)}</strong></p>`;

                    const modal = new bootstrap.Modal(document.getElementById('modalConfirmarEntrega'));
                    modal.show();
                } else {
                    alert("❌ No se pudo obtener la información del pedido.");
                }
            })
            .catch(err => {
                console.error("Error:", err);
                alert("❌ Error al obtener los datos del pedido.");
            });
    }

    // Al confirmar en el modal
    document.getElementById('btnConfirmarEntrega').addEventListener('click', () => {
        const quienRecibe = document.getElementById('inputQuienRecibe').value.trim();
        const metodoPago = document.getElementById('inputMetodoPago').value;

        if (quienRecibe === '' || metodoPago === '') {
            alert("❌ Por favor completa todos los campos antes de confirmar.");
            return;
        }

        const datos = new URLSearchParams();
        datos.append("id_cotizacion", idPedidoSeleccionado);
        datos.append("receptorPedido", quienRecibe);
        datos.append("metodoPago", metodoPago);

        fetch(`../../back/deliver/marcar_entregado.php`, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: datos.toString()
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("✅ Pedido entregado.");
                    location.reload();
                } else {
                    alert("❌ Error: " + (data.error || "No se pudo actualizar."));
                }
            })
            .catch(err => {
                console.error("Error:", err);
                alert("❌ Error al comunicar con el servidor.");
            });
    });


</script>
