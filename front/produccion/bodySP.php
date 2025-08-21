<?php
include '../../back/db/connection.php';

try {
    // Obtener las requisiciones pendientes
    $query = "SELECT r.id_requisicion, r.cantidad, r.fecha_creacion, r.usuario_responsable, p.nombre_producto 
              FROM requisiciones r
              JOIN recetas p ON r.id_producto = p.id_receta
              WHERE r.estatus = 'pendiente'
              ORDER BY r.fecha_creacion DESC";
    $stmt = $conn->query($query);
    $requisiciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al recuperar las requisiciones: " . $e->getMessage());
}
?>

<div class="container mt-4">
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Regresar al Dashboard</a>
    <h1 class="text-center mb-4">Salidas de Producto - Requisiciones Pendientes</h1>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']); ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <div class="row">
        <?php foreach ($requisiciones as $row): ?>
            <div class="col-12 col-md-6 col-lg-4 mb-3">
                <div class="card border-success shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?= htmlspecialchars($row['nombre_producto']); ?></h5>
                        <p class="card-text">
                            <strong>Cantidad:</strong> <?= htmlspecialchars($row['cantidad']); ?><br>
                            <strong>Solicitado por:</strong> <?= htmlspecialchars($row['usuario_responsable']); ?><br>
                            <strong>Fecha:</strong> <?= htmlspecialchars($row['fecha_creacion']); ?>
                        </p>
                        <!-- Botón para abrir el modal -->
                        <button class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#modal-<?= $row['id_requisicion']; ?>">
                            Entregar Requisición
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal de confirmación -->
            <div class="modal fade" id="modal-<?= $row['id_requisicion']; ?>" tabindex="-1" aria-labelledby="modalLabel-<?= $row['id_requisicion']; ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLabel-<?= $row['id_requisicion']; ?>">Confirmar Entrega</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            ¿Estás seguro de que deseas marcar como entregada la siguiente requisición?
                            <ul>
                                <li><strong>Producto:</strong> <?= htmlspecialchars($row['nombre_producto']); ?></li>
                                <li><strong>Cantidad:</strong> <?= htmlspecialchars($row['cantidad']); ?></li>
                            </ul>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <form action="../../back/produccion/entregar_requisicion.php" method="POST" class="d-inline">
                                <input type="hidden" name="id_requisicion" value="<?= $row['id_requisicion']; ?>">
                                <button type="submit" class="btn btn-success">Confirmar Entrega</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
