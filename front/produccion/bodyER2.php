<?php

include '../../back/db/connection.php';

// Verificar si se recibió un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Regalo no válido.";
    header("Location: catalogo_regalos.php");
    exit();
}

$id_regalo = $_GET['id'];

// Obtener los datos del regalo
try {
    $query = "SELECT * FROM regalos WHERE id_regalo = :id_regalo";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_regalo', $id_regalo, PDO::PARAM_INT);
    $stmt->execute();
    $regalo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$regalo) {
        $_SESSION['error'] = "Regalo no encontrado.";
        header("Location: catalogo_regalos.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al obtener el regalo.";
    header("Location: catalogo_regalos.php");
    exit();
}
?>

<div class="container mt-4">
    <h1 class="text-center">Editar Regalo</h1>

    <!-- Mensajes -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form action="../../back/produccion/editar_regalo.php" method="POST">
        <input type="hidden" name="id_regalo" value="<?= htmlspecialchars($id_regalo) ?>">

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Regalo</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($regalo['nombre']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="2"><?= htmlspecialchars($regalo['descripcion']) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="costo_estimado" class="form-label">Costo Estimado</label>
            <input type="number" step="0.01" class="form-control" id="costo_estimado" name="costo_estimado" value="<?= htmlspecialchars($regalo['costo_estimado']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="estatus" class="form-label">Estatus</label>
            <select class="form-select" id="estatus" name="estatus" required>
                <option value="activo" <?= $regalo['estatus'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                <option value="inactivo" <?= $regalo['estatus'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </div>

        <div class="d-flex justify-content-between">
            <a href="catalogo_regalos.php" class="btn btn-secondary">← Regresar</a>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </form>
</div>
