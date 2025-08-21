<?php
include '../../back/db/connection.php';

// Verificar si se recibió el ID de la receta
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID de receta no válido.";
    header('Location: catalogo_recetas.php');
    exit();
}

$id_receta = intval($_GET['id']);

try {
    // Obtener los datos de la receta para mostrarlos en el formulario
    $query = "SELECT nombre_producto, cantidad, unidad_medida FROM recetas WHERE id_receta = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id_receta]);
    $receta = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$receta) {
        $_SESSION['error'] = "La receta no existe.";
        header('Location: catalogo_recetas.php');
        exit();
    }
} catch (PDOException $e) {
    die("Error al obtener la receta: " . $e->getMessage());
}
?>

<div class="container mt-4">
    <a href="catalogo_recetas.php" class="btn btn-secondary mb-3">← Regresar al Catálogo</a>
    <h1 class="text-center">Editar Receta</h1>
    <form action="../../back/produccion/process_editar_receta.php" method="POST">
        <input type="hidden" name="id_receta" value="<?= $id_receta ?>">

        <div class="mb-3">
            <label for="nombre_producto" class="form-label">Nombre del Producto Terminado</label>
            <input type="text" class="form-control" id="nombre_producto" name="nombre_producto" value="<?= htmlspecialchars($receta['nombre_producto']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="cantidad" class="form-label">Cantidad</label>
            <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" value="<?= htmlspecialchars($receta['cantidad']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="unidad_medida" class="form-label">Unidad de Medida</label>
            <select class="form-select" id="unidad_medida" name="unidad_medida" required>
                <option value="kg" <?= $receta['unidad_medida'] === 'kg' ? 'selected' : '' ?>>Kilogramos</option>
                <option value="gr" <?= $receta['unidad_medida'] === 'gr' ? 'selected' : '' ?>>Gramos</option>
                <option value="pieza" <?= $receta['unidad_medida'] === 'pieza' ? 'selected' : '' ?>>Pieza</option>
                <option value="L" <?= $receta['unidad_medida'] === 'L' ? 'selected' : '' ?>>Litros</option>
                <option value="ml" <?= $receta['unidad_medida'] === 'ml' ? 'selected' : '' ?>>Mililitros</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Guardar Cambios</button>
    </form>
</div>
