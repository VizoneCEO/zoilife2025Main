<?php
include '../../back/db/connection.php';

// Verificar si se recibió un ID de cliente válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Cliente no válido.";
    header("Location: clientes.php");
    exit();
}

$id_cliente = $_GET['id'];

// Obtener las direcciones del cliente
try {
    $query = "SELECT * FROM direcciones WHERE id_cliente = :id_cliente AND estatus = 'activo'";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);
    $stmt->execute();
    $direcciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al obtener direcciones: " . $e->getMessage());
}
?>

<div class="container mt-4">
    <!-- Botón de regreso -->
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Regresar al Dashboard</a>

    <h1 class="text-center">Direcciones del Cliente</h1>

    <!-- Botón verde para agregar nueva dirección -->
    <a href="nueva_direccion.php?id=<?= htmlspecialchars($id_cliente) ?>" class="btn btn-success mb-3">
        + Agregar Dirección
    </a>

    <!-- Mensajes -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <!-- Lista de direcciones -->
    <div class="row">
        <?php if (!empty($direcciones)): ?>
            <?php foreach ($direcciones as $direccion): ?>
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <div class="card shadow-sm border-success">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($direccion['calle'] . ' ' . $direccion['numero_casa']) ?></h5>
                            <p class="card-text">
                                <strong>Ciudad:</strong> <?= htmlspecialchars($direccion['ciudad']) ?><br>
                                <strong>Estado:</strong> <?= htmlspecialchars($direccion['estado']) ?><br>
                                <strong>Código Postal:</strong> <?= htmlspecialchars($direccion['codigo_postal']) ?><br>
                                <strong>País:</strong> <?= htmlspecialchars($direccion['pais']) ?><br>
                                <strong>Tipo:</strong> <?= htmlspecialchars($direccion['tipo_vivienda']) ?><br>
                                <strong>Observaciones:</strong> <?= htmlspecialchars($direccion['observaciones'] ?: 'N/A') ?><br>
                            </p>
                            <div class="d-flex justify-content-between">
                                <a href="editar_direccion.php?id=<?= $direccion['id_direccion'] ?>" class="btn btn-primary btn-sm">Editar</a>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $direccion['id_direccion'] ?>">Eliminar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal de confirmación para eliminar -->
                <div class="modal fade" id="deleteModal<?= $direccion['id_direccion'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $direccion['id_direccion'] ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Confirmar Eliminación</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                ¿Estás seguro de que deseas eliminar esta dirección?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <a href="../../back/ventas/eliminar_direccion.php?id=<?= $direccion['id_direccion'] ?>&cliente=<?= $id_cliente ?>" class="btn btn-danger">Eliminar</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning text-center">No hay direcciones registradas para este cliente.</div>
            </div>
        <?php endif; ?>
    </div>
</div>

