<?php
include '../../back/db/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID de usuario no válido.";
    header("Location: usuarios.php");
    exit();
}

$id_usuario = intval($_GET['id']);

// Obtener datos del usuario
try {
    $query = "SELECT nombre, apellido FROM usuarios WHERE id_usuario = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        $_SESSION['error'] = "Usuario no encontrado.";
        header("Location: usuarios.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al obtener datos del usuario.";
    header("Location: usuarios.php");
    exit();
}
?>

<div class="container mt-4">
    <a href="gestion_usuarios.php" class="btn btn-secondary mb-3">← Regresar a Gestión de Usuarios</a>
    <h1 class="text-center">Cambiar Contraseña</h1>

    <!-- Mostrar mensajes de error o éxito -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form action="../../back/admin/cambiar_password.php" method="POST" onsubmit="return validarPassword()">
        <input type="hidden" name="id_usuario" value="<?= $id_usuario ?>">

        <div class="mb-3">
            <label class="form-label">Usuario:</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?>" disabled>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Nueva Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>

        <div class="alert alert-danger d-none" id="errorPassword">Las contraseñas no coinciden.</div>

        <button type="submit" class="btn btn-success w-100">Actualizar Contraseña</button>
    </form>
</div>

<script>
    function validarPassword() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const errorDiv = document.getElementById('errorPassword');

        if (password !== confirmPassword) {
            errorDiv.classList.remove('d-none');
            return false;
        }

        errorDiv.classList.add('d-none');
        return true;
    }
</script>
