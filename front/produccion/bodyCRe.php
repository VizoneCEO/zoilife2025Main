<?php
include '../../back/db/connection.php';

// Obtener los regalos desde la base de datos
try {
    $query = "SELECT * FROM regalos ORDER BY nombre ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $regalos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener regalos: " . $e->getMessage());
}
?>

<div class="container mt-4">
    <!-- Botón de regreso -->
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Regresar al Dashboard</a>

    <h1 class="text-center">Catálogo de Regalos</h1>

    <!-- Botón verde para agregar un nuevo regalo -->
    <a href="nuevo_regalo.php" class="btn btn-success mb-3">+ Agregar Regalo</a>

    <!-- Mensajes -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <!-- Tabla de regalos -->
    <div class="table-responsive">
        <table class="table table-striped">
            <thead class="table-success">
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Costo Estimado</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($regalos)): ?>
                <?php foreach ($regalos as $regalo): ?>
                    <tr>
                        <td><?= htmlspecialchars($regalo['id_regalo']) ?></td>
                        <td><?= htmlspecialchars($regalo['nombre']) ?></td>
                        <td><?= htmlspecialchars($regalo['descripcion'] ?: 'N/A') ?></td>
                        <td>$<?= htmlspecialchars($regalo['costo_estimado']) ?></td>
                        <td><?= htmlspecialchars($regalo['estatus']) ?></td>
                        <td>
                            <a href="editar_regalo.php?id=<?= $regalo['id_regalo'] ?>" class="btn btn-primary btn-sm">Editar</a>
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $regalo['id_regalo'] ?>">Eliminar</button>
                        </td>
                    </tr>

                    <!-- Modal de confirmación para eliminar -->
                    <div class="modal fade" id="deleteModal<?= $regalo['id_regalo'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $regalo['id_regalo'] ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Confirmar Eliminación</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    ¿Estás seguro de que deseas eliminar el regalo <b><?= htmlspecialchars($regalo['nombre']) ?></b>?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <a href="../../back/produccion/eliminar_regalo.php?id=<?= $regalo['id_regalo'] ?>" class="btn btn-danger">Eliminar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No hay regalos registrados.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
