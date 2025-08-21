<div class="container mt-4">
    <a href="gestion_usuarios.php" class="btn btn-secondary mb-3">← Regresar a Gestión de Usuarios</a>
    <h1 class="text-center">Agregar Nuevo Usuario</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form action="../../back/admin/nuevo_usuario.php" method="POST">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>

        <div class="mb-3">
            <label for="apellido" class="form-label">Apellido</label>
            <input type="text" class="form-control" id="apellido" name="apellido" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <div class="mb-3">
            <label for="rol" class="form-label">Rol</label>
            <select class="form-select" id="rol" name="rol" required>
                <option value="admin">Administrador</option>
                <option value="produccion">Producción</option>
                <option value="almacen">Almacén</option>
                <option value="ventas">Ventas</option>
                <option value="deliver'">Deliver</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success w-100">Registrar Usuario</button>
    </form>
</div>
