<?php
include '../../back/db/connection.php';


// Verificar si se recibió un ID de cliente válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Cliente no válido.";
    header("Location: clientes.php");
    exit();
}

$id_cliente = $_GET['id'];
?>

<!-- Google Maps Autocomplete -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAs5nizMiVRkA5MMpd0xQGMfQMKOeYGTiY&libraries=places"></script>

<script>
    function initAutocomplete() {
        var input = document.getElementById('direccion');
        var autocomplete = new google.maps.places.Autocomplete(input, {
            types: ['geocode'],
            componentRestrictions: { country: "MX" }
        });

        autocomplete.addListener('place_changed', function () {
            var place = autocomplete.getPlace();
            if (!place.geometry) return;

            var addressComponents = place.address_components;
            var street = "", street_number = "", colonia = "", municipio = "", city = "", state = "", postal_code = "", country = "";
            var lat = place.geometry.location.lat();
            var lng = place.geometry.location.lng();

            addressComponents.forEach(function (component) {
                console.log(component.types[0], "→", component.long_name); // 🔍 ver el type y el nombre

                if (component.types.includes("route")) street = component.long_name;
                if (component.types.includes("street_number")) street_number = component.long_name;

                if (component.types.includes("neighborhood") || component.types.includes("sublocality") || component.types.includes("sublocality_level_1")) {
                    colonia = component.long_name;
                }

                if (component.types.includes("administrative_area_level_2")) {
                    municipio = component.long_name;
                }

                if (component.types.includes("locality")) city = component.long_name;
                if (component.types.includes("administrative_area_level_1")) state = component.long_name;
                if (component.types.includes("postal_code")) postal_code = component.long_name;
                if (component.types.includes("country")) country = component.long_name;
            });


            // Asignar valores
            document.getElementById("calle").value = street;
            document.getElementById("numero_casa").value = street_number;
            document.getElementById("colonia").value = colonia;
            document.getElementById("alcaldia_municipio").value = municipio;
            document.getElementById("ciudad").value = city;
            document.getElementById("estado").value = state;
            document.getElementById("codigo_postal").value = postal_code;
            document.getElementById("pais").value = country;
            document.getElementById("latitud").value = lat;
            document.getElementById("longitud").value = lng;
        });
    }
</script>

<body onload="initAutocomplete()">

<div class="container mt-4">
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Regresar al Dashboard</a>
    <h1 class="text-center">Agregar Dirección</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form action="../../back/ventas/nueva_direccion.php" method="POST">
        <input type="hidden" name="id_cliente" value="<?= htmlspecialchars($id_cliente) ?>">

        <div class="mb-3">
            <label for="direccion" class="form-label">Buscar Dirección (Autocomplete)</label>
            <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Escribe la dirección..." required>
        </div>

        <div class="mb-3">
            <label for="calle" class="form-label">Calle</label>
            <input type="text" class="form-control" id="calle" name="calle" required>
        </div>

        <div class="mb-3">
            <label for="numero_casa" class="form-label">Número de Casa</label>
            <input type="text" class="form-control" id="numero_casa" name="numero_casa" required>
        </div>

        <div class="mb-3">
            <label for="numero_interior" class="form-label">Número Interior</label>
            <input type="text" class="form-control" id="numero_interior" name="numero_interior">
        </div>

        <div class="mb-3">
            <label for="colonia" class="form-label">Colonia</label>
            <input type="text" class="form-control" id="colonia" name="colonia" required>
        </div>

        <div class="mb-3">
            <label for="alcaldia_municipio" class="form-label">Alcaldía o Municipio</label>
            <input type="text" class="form-control" id="alcaldia_municipio" name="alcaldia_municipio" required>
        </div>

        <div class="mb-3">
            <label for="ciudad" class="form-label">Ciudad</label>
            <input type="text" class="form-control" id="ciudad" name="ciudad" required>
        </div>

        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <input type="text" class="form-control" id="estado" name="estado" required>
        </div>

        <div class="mb-3">
            <label for="codigo_postal" class="form-label">Código Postal</label>
            <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" required>
        </div>

        <div class="mb-3">
            <label for="pais" class="form-label">País</label>
            <input type="text" class="form-control" id="pais" name="pais" required>
        </div>

        <div class="mb-3">
            <label for="entre_calles" class="form-label">Entre Calles</label>
            <input type="text" class="form-control" id="entre_calles" name="entre_calles">
        </div>

        <div class="mb-3">
            <label for="observaciones" class="form-label">Observaciones</label>
            <textarea class="form-control" id="observaciones" name="observaciones" rows="2"></textarea>
        </div>

        <div class="mb-3">
            <label for="tipo_vivienda" class="form-label">Tipo de Vivienda</label>
            <select class="form-select" id="tipo_vivienda" name="tipo_vivienda" required>
                <option value="casa">Casa</option>
                <option value="departamento">Departamento</option>
            </select>
        </div>

        <input type="hidden" id="latitud" name="latitud">
        <input type="hidden" id="longitud" name="longitud">

        <div class="d-flex justify-content-between">
            <a href="clientes.php" class="btn btn-secondary">← Regresar</a>
            <button type="submit" class="btn btn-success">Guardar Dirección</button>
        </div>
    </form>
</div>

