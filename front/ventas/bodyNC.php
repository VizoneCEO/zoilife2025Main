<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../front/auth/login.php?error=Debe iniciar sesión.');
    exit();
}

?>

<div class="container mt-4">
    <h1 class="text-center">Registrar Nuevo Cliente</h1>

    <!-- Mostrar mensajes de error o éxito -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form action="../../back/ventas/nuevo_cliente.php" method="POST">
        <input type="hidden" name="usuario_creador" value="<?= $_SESSION['user_id']; ?>">

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>

        <div class="mb-3">
            <label for="apellido_paterno" class="form-label">Apellido Paterno</label>
            <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" required>
        </div>

        <div class="mb-3">
            <label for="apellido_materno" class="form-label">Apellido Materno</label>
            <input type="text" class="form-control" id="apellido_materno" name="apellido_materno">
        </div>

        <div class="mb-3">
            <label for="telefono1" class="form-label">Teléfono Principal</label>
            <input type="text" class="form-control" id="telefono1" name="telefono1" required>
        </div>

        <div class="mb-3">
            <label for="telefono2" class="form-label">Teléfono Secundario (Opcional)</label>
            <input type="text" class="form-control" id="telefono2" name="telefono2">
        </div>

        <div class="d-flex justify-content-between">
            <a href="clientes.php" class="btn btn-secondary">← Regresar</a>
            <button type="submit" class="btn btn-success">Registrar Cliente</button>
        </div>
    </form>
</div>
