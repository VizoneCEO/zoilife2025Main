<?php
include '../../back/db/connection.php';

try {
    // Obtener los regalos activos para la selección
    $query = "SELECT id_regalo, nombre FROM regalos WHERE estatus = 'activo' ORDER BY nombre ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $regalos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener regalos: " . $e->getMessage());
}
?>

<div class="container mt-4">
    <a href="catalogo_regalos.php" class="btn btn-secondary mb-3">← Regresar al Catálogo</a>

    <h1 class="text-center">Ingreso de Regalos</h1>

    <!-- Mostrar mensajes de error o éxito -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form action="../../back/produccion/ingreso_regalos.php" method="POST">
        <div class="mb-3">
            <label for="id_regalo" class="form-label">Seleccionar Regalo</label>
            <select class="form-select" id="id_regalo" name="id_regalo" required>
                <option value="">Seleccione un regalo</option>
                <?php foreach ($regalos as $regalo): ?>
                    <option value="<?= htmlspecialchars($regalo['id_regalo']) ?>">
                        <?= htmlspecialchars($regalo['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="cantidad" class="form-label">Cantidad</label>
            <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" required>
        </div>



        <button type="submit" class="btn btn-success w-100">Registrar Ingreso</button>
    </form>
</div>
