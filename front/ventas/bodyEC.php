<?php
include '../../back/db/connection.php';

// Verificar si se recibió el ID del cliente
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID de cliente no válido.";
    header('Location: clientes.php');
    exit();
}

$id_cliente = intval($_GET['id']);

// Obtener los datos del cliente
$query = "SELECT nombre, apellido_paterno, apellido_materno, telefono1, telefono2 FROM clientes WHERE id_cliente = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$id_cliente]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no se encontró el cliente, redirigir
if (!$cliente) {
    $_SESSION['error'] = "Cliente no encontrado.";
    header('Location: clientes.php');
    exit();
}
?>

<div class="container mt-4">
    <a href="clientes.php" class="btn btn-secondary mb-3">← Regresar a Clientes</a>
    <h1 class="text-center">Editar Cliente</h1>

    <!-- Mostrar mensajes de error o éxito -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form action="../../back/ventas/editar_cliente.php" method="POST">
        <input type="hidden" name="id_cliente" value="<?= $id_cliente ?>">

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($cliente['nombre']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="apellido_paterno" class="form-label">Apellido Paterno</label>
            <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" value="<?= htmlspecialchars($cliente['apellido_paterno']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="apellido_materno" class="form-label">Apellido Materno</label>
            <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" value="<?= htmlspecialchars($cliente['apellido_materno']) ?>">
        </div>

        <div class="mb-3">
            <label for="telefono1" class="form-label">Teléfono Principal</label>
            <input type="text" class="form-control" id="telefono1" name="telefono1" value="<?= htmlspecialchars($cliente['telefono1']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="telefono2" class="form-label">Teléfono Secundario (Opcional)</label>
            <input type="text" class="form-control" id="telefono2" name="telefono2" value="<?= htmlspecialchars($cliente['telefono2']) ?>">
        </div>

        <button type="submit" class="btn btn-success w-100">Guardar Cambios</button>
    </form>
</div>
