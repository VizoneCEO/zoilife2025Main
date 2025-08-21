<?php
require_once '../../back/db/connection.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<p class='text-danger'>❌ No se recibió el ID del pedido.</p>";
    exit;
}

try {
    // Traer datos del pedido
    $stmt = $conn->prepare("SELECT * FROM pedidos_web WHERE id_pedido_web = ?");
    $stmt->execute([$id]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) {
        echo "<p class='text-danger'>❌ Pedido no encontrado.</p>";
        exit;
    }

    // Traer productos relacionados
    $sql = "SELECT 
                pwp.cantidad, 
                pwp.precio_unitario,
                (pwp.cantidad * pwp.precio_unitario) AS subtotal,
                r.nombre_producto
            FROM pedidos_web_productos pwp
            JOIN productos_web pw ON pwp.id_producto_web = pw.id_producto_web
            JOIN recetas r ON pw.id_receta = r.id_receta
            WHERE pwp.id_pedido_web = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <h5>📋 Información del pedido</h5>
    <p><b>Cliente:</b> <?= htmlspecialchars($pedido['nombre'] . " " . $pedido['apellido_paterno'] . " " . $pedido['apellido_materno']) ?></p>
    <p><b>Correo:</b> <?= htmlspecialchars($pedido['correo']) ?></p>
    <p><b>Teléfono:</b> <?= htmlspecialchars($pedido['telefono']) ?></p>
    <p><b>Dirección:</b> <?= htmlspecialchars("{$pedido['calle']} {$pedido['numero']}, {$pedido['colonia']}, {$pedido['municipio']}, {$pedido['ciudad']}, {$pedido['estado']}, CP {$pedido['codigo_postal']}") ?></p>
    <p><b>Tipo de envío:</b> <?= ucfirst($pedido['tipo_envio']) ?></p>
    <p><b>Estatus Pago:</b> <?= ucfirst($pedido['estatus_pago']) ?></p>
    <p><b>Estatus Pedido:</b> <?= ucfirst($pedido['estatus_pedido']) ?></p>

    <h5 class="mt-3">🛒 Productos</h5>
    <table class="table table-sm table-bordered">
        <thead>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Subtotal</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($productos as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['nombre_producto']) ?></td>
                <td><?= $p['cantidad'] ?></td>
                <td>$<?= number_format($p['precio_unitario'], 2) ?></td>
                <td>$<?= number_format($p['subtotal'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h5 class="mt-3">💰 Totales</h5>
    <p><b>Subtotal:</b> $<?= number_format($pedido['subtotal'], 2) ?></p>
    <p><b>Costo de envío:</b> $<?= number_format($pedido['costo_envio'], 2) ?></p>
    <p><b>Total:</b> $<?= number_format($pedido['total'], 2) ?></p>

    <?php
} catch (Exception $e) {
    echo "<p class='text-danger'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
