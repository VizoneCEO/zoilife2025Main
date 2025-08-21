<?php

include '../../back/db/connection.php';

// Validar si hay un ID de proveedor en la URL
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: proveedores.php?error=Proveedor no encontrado.');
    exit();
}

$id_proveedor = intval($_GET['id']);

try {
    // Consultar los datos del proveedor usando PDO
    $query = "SELECT * FROM proveedores WHERE id_proveedor = :id_proveedor";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
    $stmt->execute();
    $proveedor = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si se encontró el proveedor
    if (!$proveedor) {
        header('Location: proveedores.php?error=Proveedor no encontrado.');
        exit();
    }
} catch (PDOException $e) {
    die("Error al obtener el proveedor: " . $e->getMessage());
}

?>

<div class="container mt-4">
    <!-- Botón regresar -->
    <div class="mb-3">
        <a href="proveedores.php" class="btn btn-secondary">← Regresar a Proveedores</a>
    </div>

    <!-- Título -->
    <h1 class="mb-4 text-center">Editar Proveedor</h1>

    <!-- Formulario de edición -->
    <form action="../../back/produccion/process_editar_proveedor.php" method="POST">
        <input type="hidden" name="id_proveedor" value="<?= htmlspecialchars($proveedor['id_proveedor']); ?>">

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre de la Empresa</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($proveedor['nombre']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="contacto" class="form-label">Contacto</label>
            <input type="text" class="form-control" id="contacto" name="contacto" value="<?= htmlspecialchars($proveedor['contacto']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" class="form-control" id="telefono" name="telefono" value="<?= htmlspecialchars($proveedor['telefono']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($proveedor['email']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <textarea class="form-control" id="direccion" name="direccion" rows="3" required><?= htmlspecialchars($proveedor['direccion']); ?></textarea>
        </div>

        <button type="submit" class="btn btn-success">Guardar Cambios</button>
    </form>
</div>
