<?php
include '../../back/db/connection.php';

try {
    // Obtener las recetas activas (productos terminados)
    $query = "SELECT id_receta, nombre_producto, cantidad, unidad_medida FROM recetas WHERE estatus = 'activo'";
    $stmt = $conn->query($query);
    $recetas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener las recetas: " . $e->getMessage());
}
?>

<div class="container mt-4">
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Regresar al Dashboard</a>

    <h1 class="text-center mb-4">Nueva Requisición</h1>

    <!-- MENSAJE DE ERROR SI NO HAY STOCK DISPONIBLE -->
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <form id="requisicionForm" action="../../back/produccion/process_nueva_requisicion.php" method="POST">
        <div class="mb-3">
            <label for="producto" class="form-label">Seleccione un Producto Terminado</label>
            <select class="form-select" id="producto" name="producto" required>
                <option value="">Seleccione un producto</option>
                <?php foreach ($recetas as $row): ?>
                    <option value="<?= $row['id_receta'] ?>"
                            data-nombre="<?= htmlspecialchars($row['nombre_producto']) ?>"
                            data-cantidad="<?= htmlspecialchars($row['cantidad']) ?>"
                            data-unidad="<?= htmlspecialchars($row['unidad_medida']) ?>">
                        <?= htmlspecialchars($row['nombre_producto']) ?>
                        (<?= htmlspecialchars($row['cantidad']) ?> <?= htmlspecialchars($row['unidad_medida']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="cantidad" class="form-label">Cantidad Requisitada</label>
            <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" required>
        </div>

        <!-- Mensaje de ingredientes faltantes -->
        <div id="faltantes" class="alert alert-danger d-none mt-3">
            <strong>No se puede completar la requisición. Ingredientes faltantes:</strong>
            <ul id="listaFaltantes"></ul>
        </div>

        <button type="submit" class="btn btn-success w-100">Registrar Requisición</button>
    </form>
</div>

<script>
    document.getElementById('producto').addEventListener('change', async function () {
        const productoId = this.value;
        const cantidadRequisitada = document.getElementById('cantidad').value || 1;

        if (!productoId) {
            return;
        }

        // Realizar la validación de ingredientes faltantes vía AJAX
        const response = await fetch(`../../back/produccion/check_ingredientes_faltantes.php?id_receta=${productoId}&cantidad=${cantidadRequisitada}`);
        const data = await response.json();

        const faltantesDiv = document.getElementById('faltantes');
        const listaFaltantes = document.getElementById('listaFaltantes');
        listaFaltantes.innerHTML = '';

        if (data.faltantes && data.faltantes.length > 0) {
            data.faltantes.forEach(falta => {
                const li = document.createElement('li');
                li.textContent = `${falta.nombre} (${falta.cantidad_necesaria} ${falta.unidad_medida}, stock actual: ${falta.stock_actual})`;
                listaFaltantes.appendChild(li);
            });

            faltantesDiv.classList.remove('d-none');
        } else {
            faltantesDiv.classList.add('d-none');
        }
    });

    document.getElementById('cantidad').addEventListener('input', function () {
        document.getElementById('producto').dispatchEvent(new Event('change'));
    });
</script>
