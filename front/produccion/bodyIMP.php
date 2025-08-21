<?php
include '../../back/db/connection.php';

try {
    // Obtener lista de materias primas activas
    $query = "SELECT id_materia_prima, nombre FROM materia_prima WHERE estatus = 'activo'";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $materiaPrimaList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener lista de proveedores activos
    $query = "SELECT id_proveedor, nombre FROM proveedores WHERE estatus = 'activo'";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $proveedoresList = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al recuperar los datos: " . $e->getMessage());
}
?>

<style>
    #calculo-total {
        font-size: 16px;
        font-weight: bold;
        color: #4CAF50;
        margin-top: 10px;
    }
</style>

<div class="container mt-4">
    <!-- Botón regresar -->
    <div class="mb-3">
        <a href="dashboard.php" class="btn btn-secondary">← Regresar al Dashboard</a>
    </div>

    <!-- Título -->
    <h1 class="mb-4 text-center">Ingreso de Materia Prima</h1>

    <!-- Formulario -->
    <form action="../../back/produccion/process_ingreso_materia_prima.php" method="POST">
        <div class="mb-3">
            <label for="materia_prima" class="form-label">Seleccione una materia prima</label>
            <select class="form-select" id="materia_prima" name="materia_prima" required>
                <option value="">Seleccione una materia prima</option>
                <?php foreach ($materiaPrimaList as $row): ?>
                    <option value="<?= htmlspecialchars($row['id_materia_prima']) ?>">
                        <?= htmlspecialchars($row['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="cantidad" class="form-label">Cantidad a ingresar</label>
            <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" required>
        </div>

        <div class="mb-3">
            <label for="unidad_medida" class="form-label">Unidad de Medida</label>
            <select class="form-select" id="unidad_medida" name="unidad_medida" required>
                <option value="kg">Kilogramos</option>
                <option value="gr">Gramos</option>
                <option value="pieza">Pieza</option>
                <option value="L">Litros</option>
                <option value="ml">Mililitros</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="costo_total" class="form-label">Costo Total</label>
            <input type="number" step="0.01" class="form-control" id="costo_total" name="costo_total" required>
        </div>

        <div class="mb-3">
            <label for="ticket_factura" class="form-label">Número de Ticket o Factura</label>
            <input type="text" class="form-control" id="ticket_factura" name="ticket_factura" required>
        </div>

        <div class="mb-3">
            <label for="proveedor" class="form-label">Seleccione un proveedor</label>
            <select class="form-select" id="proveedor" name="proveedor" required>
                <option value="">Seleccione un proveedor</option>
                <?php foreach ($proveedoresList as $row): ?>
                    <option value="<?= htmlspecialchars($row['id_proveedor']) ?>">
                        <?= htmlspecialchars($row['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Registrar Ingreso</button>
    </form>
</div>

<script>
    // Elementos del DOM
    const cantidadInput = document.getElementById('cantidad');
    const unidadMedidaSelect = document.getElementById('unidad_medida');
    const calculoTotal = document.getElementById('calculo-total');

    // Actualizar la visualización de la cantidad a ingresar
    function actualizarCalculoTotal() {
        const cantidad = parseInt(cantidadInput.value) || 0;
        const unidadMedida = unidadMedidaSelect.value;

        if (cantidad > 0) {
            calculoTotal.textContent = `Estas por ingresar: ${cantidad} ${unidadMedida}`;
        } else {
            calculoTotal.textContent = 'Estas por ingresar: -';
        }
    }

    // Eventos para actualizar el cálculo
    cantidadInput.addEventListener('input', actualizarCalculoTotal);
    unidadMedidaSelect.addEventListener('change', actualizarCalculoTotal);
</script>
