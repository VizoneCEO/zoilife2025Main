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
    // Obtener los datos de la receta
    $query = "SELECT nombre_producto FROM recetas WHERE id_receta = ?";
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
    <h1 class="text-center">Ingredientes de: <?= htmlspecialchars($receta['nombre_producto']) ?></h1>

    <!-- Botón para agregar ingrediente -->
    <button class="btn btn-success mb-4" data-bs-toggle="modal" data-bs-target="#modalAgregarIngrediente">Agregar Ingrediente</button>

    <!-- Modal para agregar ingrediente -->
    <div class="modal fade" id="modalAgregarIngrediente" tabindex="-1" aria-labelledby="modalAgregarIngredienteLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="../../back/produccion/agregar_ingrediente.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAgregarIngredienteLabel">Agregar Ingrediente</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_receta" value="<?= $id_receta ?>">
                        <div class="mb-3">
                            <label for="materia_prima" class="form-label">Seleccione Materia Prima</label>
                            <select class="form-select" id="materia_prima" name="materia_prima" required>
                                <option value="">Seleccione una opción</option>
                                <?php
                                try {
                                    $query_materia = "SELECT id_materia_prima, nombre FROM materia_prima WHERE estatus = 'activo'";
                                    $stmt_materia = $conn->query($query_materia);
                                    $materias_primas = $stmt_materia->fetchAll(PDO::FETCH_ASSOC);

                                    foreach ($materias_primas as $materia) {
                                        echo '<option value="' . $materia['id_materia_prima'] . '">' . htmlspecialchars($materia['nombre']) . '</option>';
                                    }
                                } catch (PDOException $e) {
                                    die("Error al obtener la materia prima: " . $e->getMessage());
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="cantidad" class="form-label">Cantidad</label>
                            <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="unidad_medida" class="form-label">Unidad de Medida</label>
                            <select class="form-select" id="unidad_medida" name="unidad_medida" required>
                                <option value="kg">Kilogramos</option>
                                <option value="gr">Gramos</option>
                                <option value="pieza">Pieza</option>
                                <option value="L">Litros</option>
                                <option value="ml">Mililitros</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Agregar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Ingredientes existentes -->
    <div class="row">
        <?php
        try {
            $query_ingredientes = "SELECT i.id_ingrediente, m.nombre, i.cantidad, i.unidad_medida 
                                    FROM ingredientes_receta i
                                    JOIN materia_prima m ON i.id_materia_prima = m.id_materia_prima
                                    WHERE i.id_receta = ? AND i.estatus = 'activo'";
            $stmt_ingredientes = $conn->prepare($query_ingredientes);
            $stmt_ingredientes->execute([$id_receta]);
            $ingredientes = $stmt_ingredientes->fetchAll(PDO::FETCH_ASSOC);

            foreach ($ingredientes as $ingrediente) {
                echo '
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title">' . htmlspecialchars($ingrediente['nombre']) . '</h5>
                                <p class="card-text">' . htmlspecialchars($ingrediente['cantidad']) . ' ' . htmlspecialchars($ingrediente['unidad_medida']) . '</p>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalEliminarIngrediente' . $ingrediente['id_ingrediente'] . '">Eliminar</button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal para eliminar ingrediente -->
                    <div class="modal fade" id="modalEliminarIngrediente' . $ingrediente['id_ingrediente'] . '" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="../../back/produccion/eliminar_ingrediente.php" method="POST">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Confirmar Eliminación</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>¿Estás seguro de que deseas eliminar este ingrediente?</p>
                                        <input type="hidden" name="id_ingrediente" value="' . $ingrediente['id_ingrediente'] . '">
                                        <input type="hidden" name="id_receta" value="' . $id_receta . '">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-danger">Eliminar</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                ';
            }
        } catch (PDOException $e) {
            die("Error al obtener los ingredientes: " . $e->getMessage());
        }
        ?>
    </div>
</div>
