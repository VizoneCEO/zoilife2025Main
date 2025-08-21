<?php
include '../../back/db/connection.php';

try {
    // Obtener todas las entradas de materia prima
    $queryEntradas = "
        SELECT 
            ing.id_ingreso AS id,
            mp.nombre AS materia_prima,
            ing.cantidad_ingresada AS cantidad,
            ing.unidad_medida AS unidad,
            ing.fecha_ingreso AS fecha,
            ing.usuario_responsable AS usuario
        FROM control_ingresos ing
        JOIN materia_prima mp ON ing.id_materia_prima = mp.id_materia_prima
        ORDER BY ing.fecha_ingreso DESC";

    $stmtEntradas = $conn->query($queryEntradas);
    $entradas = $stmtEntradas->fetchAll(PDO::FETCH_ASSOC);

    // Obtener todas las salidas de materia prima
    $querySalidas = "
        SELECT 
            sal.id_salida AS id,
            mp.nombre AS materia_prima,
            sal.cantidad_salida AS cantidad,
            sal.unidad_medida AS unidad,
            sal.fecha_salida AS fecha,
            req.usuario_responsable AS usuario
        FROM salidas_producto sal
        JOIN materia_prima mp ON sal.id_ingrediente = mp.id_materia_prima
        JOIN requisiciones req ON sal.id_requisicion = req.id_requisicion
        ORDER BY sal.fecha_salida DESC";

    $stmtSalidas = $conn->query($querySalidas);
    $salidas = $stmtSalidas->fetchAll(PDO::FETCH_ASSOC);

    // Obtener el stock de materia prima sumando entradas y restando salidas
    $queryStock = "
       SELECT 
    mp.id_materia_prima AS id,
    mp.nombre AS materia_prima,
    (SELECT COALESCE(SUM(cantidad_ingresada), 0) FROM control_ingresos 
        WHERE id_materia_prima = mp.id_materia_prima) AS total_ingresado,
    (SELECT COALESCE(SUM(cantidad_salida), 0) FROM salidas_producto 
        WHERE id_ingrediente = mp.id_materia_prima) AS total_salido,
    ((SELECT COALESCE(SUM(cantidad_ingresada), 0) FROM control_ingresos 
        WHERE id_materia_prima = mp.id_materia_prima) 
        - 
    (SELECT COALESCE(SUM(cantidad_salida), 0) FROM salidas_producto 
        WHERE id_ingrediente = mp.id_materia_prima)) AS stock_disponible,
    (SELECT unidad_medida FROM control_ingresos 
        WHERE id_materia_prima = mp.id_materia_prima 
        LIMIT 1) AS unidad
FROM materia_prima mp
ORDER BY stock_disponible ASC;
";

    $stmtStock = $conn->query($queryStock);
    $materia_prima = $stmtStock->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los datos de materia prima: " . $e->getMessage());
}
?>

<div class="container mt-4">
    <!-- Botón de regreso al dashboard -->
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Regresar al Dashboard</a>

    <!-- Campo de búsqueda -->
    <div class="mb-3">
        <input type="text" id="searchInput" class="form-control" placeholder="Buscar en todas las tablas...">
    </div>
    <h2 class="text-center mt-5">Stock de Materia Prima</h2>
    <table class="table table-bordered text-center filterTable">
        <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Materia Prima</th>
            <th>Total Ingresado</th>
            <th>Total Salido</th>
            <th>Stock Disponible</th>
            <th>Unidad</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($materia_prima as $row): ?>
            <tr class="
                    <?php
            if ($row['stock_disponible'] == 0) echo 'table-danger'; // Rojo si no hay stock
            elseif ($row['stock_disponible'] < 100) echo 'table-warning'; // Amarillo si es menor a 100
            else echo 'table-success'; // Verde si hay suficiente stock
            ?>
                ">
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['materia_prima']) ?></td>
                <td><?= number_format($row['total_ingresado'], 2) ?></td>
                <td><?= number_format($row['total_salido'], 2) ?></td>
                <td><?= number_format($row['stock_disponible'], 2) ?></td>
                <td><?= htmlspecialchars($row['unidad']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <h2 class="text-center">Entradas de Materia Prima</h2>
    <table class="table table-bordered text-center filterTable">
        <thead class="table-success">
        <tr>
            <th>ID</th>
            <th>Materia Prima</th>
            <th>Cantidad Ingresada</th>
            <th>Unidad</th>
            <th>Fecha de Ingreso</th>
            <th>Usuario Responsable</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($entradas as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['materia_prima']) ?></td>
                <td><?= number_format($row['cantidad'], 2) ?></td>
                <td><?= htmlspecialchars($row['unidad']) ?></td>
                <td><?= htmlspecialchars($row['fecha']) ?></td>
                <td><?= htmlspecialchars($row['usuario']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h2 class="text-center mt-5">Salidas de Materia Prima</h2>
    <table class="table table-bordered text-center filterTable">
        <thead class="table-danger">
        <tr>
            <th>ID</th>
            <th>Materia Prima</th>
            <th>Cantidad Salida</th>
            <th>Unidad</th>
            <th>Fecha de Salida</th>
            <th>Usuario Responsable</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($salidas as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['materia_prima']) ?></td>
                <td><?= number_format($row['cantidad'], 2) ?></td>
                <td><?= htmlspecialchars($row['unidad']) ?></td>
                <td><?= htmlspecialchars($row['fecha']) ?></td>
                <td><?= htmlspecialchars($row['usuario']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>


</div>

<script>
    // Función para filtrar todas las tablas con un solo campo de búsqueda
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let tables = document.querySelectorAll('.filterTable tbody');

        tables.forEach(tbody => {
            let rows = tbody.getElementsByTagName('tr');
            for (let row of rows) {
                let cells = row.getElementsByTagName('td');
                let found = false;
                for (let cell of cells) {
                    if (cell.textContent.toLowerCase().includes(filter)) {
                        found = true;
                        break;
                    }
                }
                row.style.display = found ? "" : "none";
            }
        });
    });
</script>
