<?php
include '../../back/db/connection.php';

try {
    // Consulta para obtener todos los proveedores activos
    $query = "SELECT id_proveedor, nombre, contacto, telefono, email FROM proveedores WHERE estatus = 'activo'";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al recuperar los datos: " . $e->getMessage());
}
?>

<style>
    .card {
        border: 1px solid #4CAF50;
        border-radius: 8px;
        transition: transform 0.2s ease-in-out;
    }

    .card:hover {
        transform: scale(1.05);
    }

    .card-title {
        font-size: 18px;
        font-weight: bold;
    }

    .card-text {
        font-size: 14px;
    }
</style>

<div class="container mt-4">
    <!-- Botón regresar -->
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Regresar al Dashboard</a>

    <h1 class="text-center">Proveedores</h1>
    <div class="row mt-4">
        <?php foreach ($proveedores as $proveedor): ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                <div class="card h-100 shadow">
                    <div class="card-body text-center">
                        <h5 class="card-title text-success"><?= htmlspecialchars($proveedor['nombre']); ?></h5>
                        <p class="card-text">
                            <strong>Contacto:</strong> <?= htmlspecialchars($proveedor['contacto']); ?><br>
                            <strong>Teléfono:</strong> <?= htmlspecialchars($proveedor['telefono']); ?><br>
                            <strong>Email:</strong> <?= htmlspecialchars($proveedor['email']); ?>
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <!-- Botón Editar -->
                            <a href="editar_proveedor.php?id=<?= $proveedor['id_proveedor']; ?>" class="btn btn-primary btn-sm">Editar</a>
                            <!-- Botón Eliminar con Modal -->
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal-<?= $proveedor['id_proveedor']; ?>">Eliminar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de confirmación para eliminación -->
            <div class="modal fade" id="deleteModal-<?= $proveedor['id_proveedor']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            ¿Estás seguro de que deseas eliminar al proveedor <strong><?= htmlspecialchars($proveedor['nombre']); ?></strong>?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <a href="../../back/produccion/eliminar_proveedor.php?id=<?= $proveedor['id_proveedor']; ?>" class="btn btn-danger">Eliminar</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
