<?php

include '../../back/db/connection.php';

$id_usuario = $_SESSION['user_id'];


$query = "SELECT 
            c.id_cotizacion, 
            c.fecha_creacion, 
            c.total, 
            c.costo_envio, 
            c.tipo_envio, 
            c.estatus, 
            cl.nombre, 
            cl.apellido_paterno, 
            cl.apellido_materno,
            u.nombre AS vendedora
          FROM cotizaciones c
          INNER JOIN clientes cl ON c.id_cliente = cl.id_cliente
          INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
          WHERE c.id_usuario = :id_usuario
          ORDER BY c.fecha_creacion DESC";

$stmt = $conn->prepare($query);
$stmt->bindParam(':id_usuario', $id_usuario);
$stmt->execute();
$cotizaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container mt-4">
    <h1 class="text-center">Listado de Cotizaciones</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <table class="table table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Fecha</th>
            <th>Total</th>
            <th>Costo Envío</th>
            <th>Tipo de Envío</th>
            <th>Vendedora</th>
            <th>Estatus</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($cotizaciones as $cotizacion): ?>
            <tr>
                <td><?= htmlspecialchars($cotizacion['id_cotizacion']); ?></td>
                <td><?= htmlspecialchars($cotizacion['nombre'] . ' ' . $cotizacion['apellido_paterno'] . ' ' . $cotizacion['apellido_materno']); ?></td>
                <td><?= htmlspecialchars($cotizacion['fecha_creacion']); ?></td>
                <td>$<?= number_format($cotizacion['total'], 2); ?></td>
                <td>$<?= number_format($cotizacion['costo_envio'], 2); ?></td>
                <td><?= htmlspecialchars(ucfirst($cotizacion['tipo_envio'])); ?></td>
                <td><?= htmlspecialchars($cotizacion['vendedora']); ?></td>

                <td>
                            <span class="badge bg-<?= $cotizacion['estatus'] == 'pendiente' ? 'warning' : ($cotizacion['estatus'] == 'confirmada' ? 'success' : 'danger'); ?>">
                                <?= htmlspecialchars(ucfirst($cotizacion['estatus'])); ?>
                            </span>
                </td>
                <td>
                    <a href="detalle_cotizacion.php?id=<?= $cotizacion['id_cotizacion']; ?>" class="btn btn-primary btn-sm">Ver Detalles</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <a href="dashboard.php" class="btn btn-secondary">Regresar</a>
</div>

