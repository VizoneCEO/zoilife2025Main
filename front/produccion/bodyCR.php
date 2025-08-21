<?php
include '../../back/db/connection.php';

try {
    // Obtener todas las recetas activas
    $query = "SELECT id_receta, nombre_producto, cantidad, unidad_medida FROM recetas WHERE estatus = 'activo'";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $recetas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener las recetas: " . $e->getMessage());
}
?>

<div class="container mt-4">
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Regresar al Dashboard</a>

    <h1 class="text-center">Catálogo de Recetas</h1>

    <div class="row">
        <?php foreach ($recetas as $receta): ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?= htmlspecialchars($receta['nombre_producto']) ?></h5>
                        <p class="card-text">
                            <?= htmlspecialchars($receta['cantidad']) . ' ' . htmlspecialchars($receta['unidad_medida']) ?>
                        </p>

                        <div class="d-flex flex-column gap-2">
                            <a href="editar_receta.php?id=<?= $receta['id_receta'] ?>" class="btn btn-primary btn-sm">Editar</a>
                            <a href="ingredientes_receta.php?id=<?= $receta['id_receta'] ?>" class="btn btn-warning btn-sm">Ingredientes</a>
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalEliminar<?= $receta['id_receta'] ?>">Eliminar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de confirmación para eliminar -->
            <div class="modal fade" id="modalEliminar<?= $receta['id_receta'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $receta['id_receta'] ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLabel<?= $receta['id_receta'] ?>">Confirmar Eliminación</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            ¿Estás seguro de que deseas eliminar la receta <b><?= htmlspecialchars($receta['nombre_producto']) ?></b>?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <a href="../../back/produccion/eliminar_receta.php?id=<?= $receta['id_receta'] ?>" class="btn btn-danger">Eliminar</a>
                        </div>
                    </div>
                </div>
            </div>

        <?php endforeach; ?>
    </div>
</div>
