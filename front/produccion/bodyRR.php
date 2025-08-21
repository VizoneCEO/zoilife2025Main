<?php
include '../../back/db/connection.php';

try {
    // Obtener las requisiciones pendientes
    $query = "SELECT r.id_requisicion, r.cantidad, r.fecha_creacion, r.usuario_responsable, r.estatus, re.nombre_producto
              FROM requisiciones r
              JOIN recetas re ON r.id_producto = re.id_receta
              WHERE r.estatus = 'pendiente'";
    $stmt = $conn->query($query);
    $requisiciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al recuperar las requisiciones: " . $e->getMessage());
}
?>

<div class="container mt-4">
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Regresar al Dashboard</a>
    <h1 class="text-center mb-4">Requisiciones Realizadas</h1>

    <div class="row">
        <?php foreach ($requisiciones as $row): ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?= htmlspecialchars($row['nombre_producto']) ?></h5>
                        <p class="card-text">
                            Cantidad: <?= htmlspecialchars($row['cantidad']) ?><br>
                            Responsable: <?= htmlspecialchars($row['usuario_responsable']) ?><br>
                            Fecha: <?= htmlspecialchars($row['fecha_creacion']) ?><br>
                            Estado: <?= htmlspecialchars($row['estatus']) ?>
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?= $row['id_requisicion'] ?>">
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal de confirmación -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar esta requisición?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="../../back/produccion/eliminar_requisicion.php" method="POST">
                    <input type="hidden" name="id_requisicion" id="deleteId">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Pasar el ID de la requisición al modal
    const deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const requisicionId = button.getAttribute('data-id');
        const inputDeleteId = document.getElementById('deleteId');
        inputDeleteId.value = requisicionId;
    });
</script>
