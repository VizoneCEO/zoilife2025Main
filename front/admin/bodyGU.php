<?php
include '../../back/db/connection.php';

try {
    // Obtener todos los usuarios
    $query = "SELECT id_usuario, nombre, apellido, email, rol, estado FROM usuarios";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener usuarios: " . $e->getMessage());
}
?>

<div class="container mt-4">
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Regresar al Dashboard</a>
    <h1 class="text-center">Gestión de Usuarios</h1>

    <a href="nuevo_usuario.php" class="btn btn-success mb-3">Agregar Nuevo Usuario</a>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead class="table-success">
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?= htmlspecialchars($usuario['id_usuario']) ?></td>
                    <td><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></td>
                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                    <td><?= htmlspecialchars($usuario['rol']) ?></td>
                    <td>
                        <span class="badge <?= $usuario['estado'] === 'activo' ? 'bg-success' : 'bg-danger' ?>">
                            <?= htmlspecialchars($usuario['estado']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="editar_usuario.php?id=<?= $usuario['id_usuario'] ?>" class="btn btn-primary btn-sm">Editar</a>
                        <a href="cambiar_password.php?id=<?= $usuario['id_usuario'] ?>" class="btn btn-warning btn-sm">Contraseña</a>

                        <?php if ($usuario['estado'] === 'activo'): ?>
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalEstado<?= $usuario['id_usuario'] ?>">
                                Desactivar
                            </button>
                        <?php else: ?>
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalEstado<?= $usuario['id_usuario'] ?>">
                                Activar
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>

                <!-- Modal de confirmación para cambiar estado -->
                <div class="modal fade" id="modalEstado<?= $usuario['id_usuario'] ?>" tabindex="-1" aria-labelledby="modalEstadoLabel<?= $usuario['id_usuario'] ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Confirmar Acción</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                ¿Estás seguro de que deseas
                                <?= $usuario['estado'] === 'activo' ? 'desactivar' : 'activar' ?>
                                al usuario <b><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></b>?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <a href="../../back/admin/cambiar_estado_usuario.php?id=<?= $usuario['id_usuario'] ?>" class="btn btn-<?= $usuario['estado'] === 'activo' ? 'danger' : 'success' ?>">
                                    <?= $usuario['estado'] === 'activo' ? 'Desactivar' : 'Activar' ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
