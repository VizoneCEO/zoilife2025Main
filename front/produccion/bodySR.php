<?php
include '../../back/db/connection.php';

try {
    // Obtener la lista única de nombres de regalos activos
    $query = "SELECT nombre 
              FROM regalos 
              WHERE estatus = 'activo' 
              GROUP BY nombre 
              ORDER BY nombre ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $nombres = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $regalos = [];

    foreach ($nombres as $nombre) {
        // Sumar todos los ingresos para ese nombre
        $query_ingresos = "SELECT SUM(i.cantidad) AS total_ingresado
                           FROM regalos r
                           LEFT JOIN ingreso_regalos i ON r.id_regalo = i.id_regalo
                           WHERE r.nombre = ? AND r.estatus = 'activo'";
        $stmt_ing = $conn->prepare($query_ingresos);
        $stmt_ing->execute([$nombre]);
        $ingresado = $stmt_ing->fetch(PDO::FETCH_ASSOC);

        // Sumar todas las entregas para ese nombre
        $query_entregado = "SELECT SUM(cr.cantidad) AS total_entregado
                            FROM regalos r
                            JOIN cotizaciones_regalos cr ON r.id_regalo = cr.id_regalo
                            JOIN cotizaciones c ON cr.id_cotizacion = c.id_cotizacion
                            WHERE r.nombre = ? AND c.estatus = 'entregado'";
        $stmt_ent = $conn->prepare($query_entregado);
        $stmt_ent->execute([$nombre]);
        $entregado = $stmt_ent->fetch(PDO::FETCH_ASSOC);

        $regalos[] = [
            'nombre' => $nombre,
            'total_ingresado' => $ingresado['total_ingresado'] ?? 0,
            'total_entregado' => $entregado['total_entregado'] ?? 0,
            'stock_real' => ($ingresado['total_ingresado'] ?? 0) - ($entregado['total_entregado'] ?? 0)
        ];
    }
} catch (PDOException $e) {
    die("Error al obtener stock de regalos: " . $e->getMessage());
}
?>

<div class="container mt-4">
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Regresar al Dashboard</a>
    <h1 class="text-center">Stock de Regalos</h1>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead class="table-success">
            <tr>
                <th>#</th>
                <th>Nombre del Regalo</th>
                <th>Total Ingresado</th>
                <th>Total Entregado</th>
                <th>Stock Disponible</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($regalos as $index => $regalo): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($regalo['nombre']) ?></td>
                    <td><?= htmlspecialchars($regalo['total_ingresado']) ?></td>
                    <td><?= htmlspecialchars($regalo['total_entregado']) ?></td>
                    <td><strong><?= $regalo['stock_real'] ?></strong></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
