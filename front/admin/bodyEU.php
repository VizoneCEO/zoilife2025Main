<?php
include '../../back/db/connection.php';

// Validar que el ID esté presente
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID de usuario no válido.";
    header('Location: usuarios.php');
    exit();
}

$id_usuario = intval($_GET['id']);

// Obtener datos del usuario
$query = "SELECT nombre, apellido, email, rol, estado FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$id_usuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    $_SESSION['error'] = "El usuario no existe.";
    header('Location: usuarios.php');
    exit();
}
?>

<div class="container mt-4">
    <a href="gestion_usuarios.php" class="btn btn-secondary mb-3">← Regresar a Gestión de Usuarios</a>
    <h1 class="text-center">Editar Usuario</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form action="../../back/admin/editar_usuario.php" method="POST">
        <input type="hidden" name="id_usuario" value="<?= $id_usuario ?>">

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="apellido" class="form-label">Apellido</label>
            <input type="text" class="form-control" id="apellido" name="apellido" value="<?= htmlspecialchars($usuario['apellido']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($usuario['email']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="rol" class="form-label">Rol</label>
            <select class="form-select" id="rol" name="rol" required>
                <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                <option value="produccion" <?= $usuario['rol'] === 'produccion' ? 'selected' : '' ?>>Producción</option>
                <option value="almacen" <?= $usuario['rol'] === 'almacen' ? 'selected' : '' ?>>Almacén</option>
                <option value="ventas" <?= $usuario['rol'] === 'ventas' ? 'selected' : '' ?>>Ventas</option>
                <option value="deliver" <?= $usuario['rol'] === 'deliver' ? 'selected' : '' ?>>Deliver</option>

            </select>
        </div>

        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <select class="form-select" id="estado" name="estado" required>
                <option value="activo" <?= $usuario['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                <option value="inactivo" <?= $usuario['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success w-100">Guardar Cambios</button>
    </form>
</div>
