<?php
include '../../back/db/connection.php';

try {
    // Obtener los registros de ingresos
    $query = "SELECT c.id_ingreso, m.nombre, c.cantidad_ingresada, c.unidad_medida, c.usuario_responsable, c.fecha_ingreso
              FROM control_ingresos c
              JOIN materia_prima m ON c.id_materia_prima = m.id_materia_prima
              ORDER BY c.fecha_ingreso DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al recuperar los datos: " . $e->getMessage());
}
?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Control de Stock - Materia Prima</h1>

    <a href="../../front/produccion/dashboard.php" class="btn btn-success mb-4">← Regresar al Dashboard</a>

    <?php if (!empty($result)): ?>
        <table class="table table-striped">
            <thead class="table-success">
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Cantidad Ingresada</th>
                <th>Unidad de Medida</th>
                <th>Usuario Responsable</th>
                <th>Fecha de Ingreso</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($result as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id_ingreso']) ?></td>
                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                    <td><?= htmlspecialchars($row['cantidad_ingresada']) ?></td>
                    <td><?= htmlspecialchars($row['unidad_medida']) ?></td>
                    <td><?= htmlspecialchars($row['usuario_responsable']) ?></td>
                    <td><?= htmlspecialchars($row['fecha_ingreso']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-center">No hay registros de ingresos.</p>
    <?php endif; ?>
</div>
