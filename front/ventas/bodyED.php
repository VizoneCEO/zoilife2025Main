<?php
include '../../back/db/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Dirección no válida.";
    header("Location: clientes.php");
    exit();
}

$id_direccion = $_GET['id'];

try {
    $query = "SELECT * FROM direcciones WHERE id_direccion = :id_direccion";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_direccion', $id_direccion, PDO::PARAM_INT);
    $stmt->execute();
    $direccion = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$direccion) {
        $_SESSION['error'] = "Dirección no encontrada.";
        header("Location: clientes.php");
        exit();
    }
} catch (PDOException $e) {
    die("Error al obtener la dirección: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Dirección</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDLoQQe1xoSThCOJni0kfg_EeFbJN7uifo&libraries=places"></script>
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
                    if (component.types.includes("route")) street = component.long_name;
                    if (component.types.includes("street_number")) street_number = component.long_name;
                    if (component.types.includes("neighborhood") || component.types.includes("sublocality") || component.types.includes("sublocality_level_1")) colonia = component.long_name;
                    if (component.types.includes("administrative_area_level_2")) municipio = component.long_name;
                    if (component.types.includes("locality")) city = component.long_name;
                    if (component.types.includes("administrative_area_level_1")) state = component.long_name;
                    if (component.types.includes("postal_code")) postal_code = component.long_name;
                    if (component.types.includes("country")) country = component.long_name;
                });

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
</head>
<body onload="initAutocomplete()">

<div class="container mt-4">
    <h1 class="text-center">Editar Dirección</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form action="../../back/ventas/editar_direccion.php" method="POST">
        <input type="hidden" name="id_direccion" value="<?= htmlspecialchars($id_direccion) ?>">
        <input type="hidden" name="id_cliente" value="<?= htmlspecialchars($direccion['id_cliente']) ?>">

        <div class="mb-3">
            <label for="direccion" class="form-label">Buscar Dirección</label>
            <input type="text" class="form-control" id="direccion" placeholder="Escribe la dirección...">
        </div>

        <div class="mb-3">
            <label for="calle" class="form-label">Calle</label>
            <input type="text" class="form-control" id="calle" name="calle" value="<?= htmlspecialchars($direccion['calle']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="numero_casa" class="form-label">Número de Casa</label>
            <input type="text" class="form-control" id="numero_casa" name="numero_casa" value="<?= htmlspecialchars($direccion['numero_casa']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="numero_interior" class="form-label">Número Interior</label>
            <input type="text" class="form-control" id="numero_interior" name="numero_interior" value="<?= htmlspecialchars($direccion['numero_interior'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="colonia" class="form-label">Colonia</label>
            <input type="text" class="form-control" id="colonia" name="colonia" value="<?= htmlspecialchars($direccion['colonia'] ?? '') ?>" required>
        </div>


        <div class="mb-3">
            <label for="alcaldia_municipio" class="form-label">Alcaldía o Municipio</label>
            <input type="text" class="form-control" id="alcaldia_municipio" name="alcaldia_municipio" value="<?= htmlspecialchars($direccion['alcaldia_municipio'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="ciudad" class="form-label">Ciudad</label>
            <input type="text" class="form-control" id="ciudad" name="ciudad" value="<?= htmlspecialchars($direccion['ciudad']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <input type="text" class="form-control" id="estado" name="estado" value="<?= htmlspecialchars($direccion['estado']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="codigo_postal" class="form-label">Código Postal</label>
            <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" value="<?= htmlspecialchars($direccion['codigo_postal']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="pais" class="form-label">País</label>
            <input type="text" class="form-control" id="pais" name="pais" value="<?= htmlspecialchars($direccion['pais']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="entre_calles" class="form-label">Entre Calles</label>
            <input type="text" class="form-control" id="entre_calles" name="entre_calles" value="<?= htmlspecialchars($direccion['entre_calles'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="observaciones" class="form-label">Observaciones</label>
            <textarea class="form-control" id="observaciones" name="observaciones" rows="2"><?= htmlspecialchars($direccion['observaciones']) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="tipo_vivienda" class="form-label">Tipo de Vivienda</label>
            <select class="form-select" id="tipo_vivienda" name="tipo_vivienda" required>
                <option value="casa" <?= ($direccion['tipo_vivienda'] == 'casa') ? 'selected' : '' ?>>Casa</option>
                <option value="departamento" <?= ($direccion['tipo_vivienda'] == 'departamento') ? 'selected' : '' ?>>Departamento</option>
            </select>
        </div>

        <input type="hidden" id="latitud" name="latitud" value="<?= htmlspecialchars($direccion['latitud']) ?>">
        <input type="hidden" id="longitud" name="longitud" value="<?= htmlspecialchars($direccion['longitud']) ?>">

        <div class="d-flex justify-content-between">
            <a href="direcciones.php?id=<?= htmlspecialchars($direccion['id_cliente']) ?>" class="btn btn-secondary">← Regresar</a>
            <button type="submit" class="btn btn-success">Actualizar Dirección</button>
        </div>
    </form>
</div>

</body>
</html>