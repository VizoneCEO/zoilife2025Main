<?php

$carrito = $_SESSION['carrito'] ?? [];

if (empty($carrito)) {
    echo "Tu carrito está vacío.";
    exit;
}

include '../../back/db/connection.php';

$ids = implode(',', array_keys($carrito));
$stmt = $conn->prepare("
    SELECT pw.id_producto_web, r.nombre_producto, pw.precio 
    FROM productos_web pw
    JOIN recetas r ON pw.id_receta = r.id_receta
    WHERE pw.id_producto_web IN ($ids)
");
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach ($productos as $p) {
    $cantidad = $carrito[$p['id_producto_web']];
    $subtotal = $p['precio'] * $cantidad;
    $total += $subtotal;
}
?>

<script>
  fbq('track', 'InitiateCheckout');
</script>


<div class="container mt-5 mb-5">
    <h2 class="mb-4">Finaliza tu compra</h2>

    <form id="checkout-form" method="POST" action="procesar_checkout.php">
        <!-- DATOS DEL CLIENTE -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Nombre</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Apellido Paterno</label>
                <input type="text" name="apellido_paterno" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Apellido Materno</label>
                <input type="text" name="apellido_materno" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label>Correo electrónico</label>
                <input type="email" name="correo" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Teléfono</label>
                <input type="text" name="telefono" class="form-control" required>
            </div>
        </div>

        <h4>Dirección de envío</h4>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAs5nizMiVRkA5MMpd0xQGMfQMKOeYGTiY&libraries=places"></script>
        <script>
            function initAutocomplete() {
                const estadosLocales = ["Ciudad de México", "CDMX", "Estado de México","Queretaro"];
                const costoEnvioForaneo = 150;

                const autocomplete = new google.maps.places.Autocomplete(
                    document.getElementById('direccion'),
                    { types: ['geocode'], componentRestrictions: { country: "MX" } }
                );

                autocomplete.addListener('place_changed', function () {
                    const place = autocomplete.getPlace();
                    if (!place.geometry) return;

                    let state = "";
                    let street = "", number = "", colonia = "", municipio = "", ciudad = "", estado = "", cp = "", pais = "";

                    place.address_components.forEach(component => {
                        if (component.types.includes("route")) street = component.long_name;
                        if (component.types.includes("street_number")) number = component.long_name;
                        if (component.types.includes("neighborhood") || component.types.includes("sublocality")) colonia = component.long_name;
                        if (component.types.includes("administrative_area_level_2")) municipio = component.long_name;
                        if (component.types.includes("locality")) ciudad = component.long_name;
                        if (component.types.includes("administrative_area_level_1")) estado = component.long_name;
                        if (component.types.includes("postal_code")) cp = component.long_name;
                        if (component.types.includes("country")) pais = component.long_name;
                    });

                    document.getElementById('calle').value = street;
                    document.getElementById('numero_casa').value = number;
                    document.getElementById('colonia').value = colonia;
                    document.getElementById('alcaldia_municipio').value = municipio;
                    document.getElementById('ciudad').value = ciudad;
                    document.getElementById('estado').value = estado;
                    document.getElementById('codigo_postal').value = cp;
                    document.getElementById('pais').value = pais;

                    const tipoEnvioLocal = document.querySelector('input[value="local"]');
                    const tipoEnvioForaneo = document.querySelector('input[value="foraneo"]');
                    const filaEnvio = document.getElementById("fila-envio");
                    const envioValor = document.getElementById("envio-valor");
                    const totalElem = document.querySelector('input[name="total"]');
                    const totalVisual = document.getElementById("total-final");

                    let subtotal = <?= $total ?>;
                    let totalFinal = subtotal;

                    if (!estadosLocales.includes(estado)) {
                        tipoEnvioForaneo.checked = true;
                        tipoEnvioLocal.disabled = true;
                        tipoEnvioForaneo.disabled = false;
                        filaEnvio.style.display = 'flex';
                        envioValor.textContent = "$" + costoEnvioForaneo.toFixed(2);
                        totalFinal += costoEnvioForaneo;
                    } else {
                        tipoEnvioLocal.checked = true;
                        tipoEnvioForaneo.disabled = true;
                        tipoEnvioLocal.disabled = false;
                        filaEnvio.style.display = 'none';
                        envioValor.textContent = "$0.00";
                    }

                    totalElem.value = totalFinal.toFixed(2);
                    totalVisual.textContent = "$" + totalFinal.toFixed(2);
                });
            }
        </script>
        <body onload="initAutocomplete()">

        <input type="text" id="direccion" class="form-control mb-3" placeholder="Escribe la dirección...">

        <input type="text" name="calle" id="calle" class="form-control mb-3" placeholder="Calle" required>
        <input type="text" name="numero_casa" id="numero_casa" class="form-control mb-3" placeholder="Número" required>
        <input type="text" name="colonia" id="colonia" class="form-control mb-3" placeholder="Colonia" required>
        <input type="text" name="alcaldia_municipio" id="alcaldia_municipio" class="form-control mb-3" placeholder="Alcaldía / Municipio" required>
        <input type="text" name="ciudad" id="ciudad" class="form-control mb-3" placeholder="Ciudad" required>
        <input type="text" name="estado" id="estado" class="form-control mb-3" placeholder="Estado" required>
        <input type="text" name="codigo_postal" id="codigo_postal" class="form-control mb-3" placeholder="Código Postal" required>
        <input type="text" name="pais" id="pais" class="form-control mb-3" placeholder="País" required>

        <div class="mb-3">
            <label>Tipo de envío</label><br>
            <input type="radio" name="tipo_envio" value="local" checked readonly> Local
            <input type="radio" name="tipo_envio" value="foraneo" readonly> Foráneo
        </div>



        <div class="mb-3">
            <label>Referencias de entrega</label>
            <textarea name="observaciones" class="form-control" rows="3"></textarea>
        </div>

        <h4>Resumen del pedido</h4>
        <ul class="list-group mb-3" id="resumen-pedido">
            <?php foreach ($productos as $p): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($p['nombre_producto']) ?> (x<?= $carrito[$p['id_producto_web']] ?>)
                    <span>$<?= number_format($p['precio'] * $carrito[$p['id_producto_web']], 2) ?></span>
                </li>
            <?php endforeach; ?>
            <li class="list-group-item d-flex justify-content-between align-items-center" id="fila-envio" style="display:none;">
                <span><strong>Costo de envío foráneo</strong></span>
                <strong id="envio-valor">$0.00</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <strong>Total</strong>
                <strong id="total-final">$<?= number_format($total, 2) ?></strong>
            </li>
        </ul>

        <input type="hidden" name="total" value="<?= $total ?>">
        <input type="hidden" name="carrito_json" value='<?= json_encode($carrito) ?>'>
        <button type="submit" class="btn btn-success w-100">Proceder al Pago</button>
    </form>
</div>