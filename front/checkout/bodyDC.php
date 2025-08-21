<?php
require_once '../../back/db/connection.php';

$order = $_GET['order'] ?? null;
if (!$order) {
    die("❌ No se recibió número de pedido.");
}

try {
    // Obtener datos del pedido
    $stmt = $conn->prepare("SELECT * FROM pedidos_web WHERE order_number = ?");
    $stmt->execute([$order]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) {
        die("⚠️ Pedido no encontrado.");
    }

    // Obtener productos relacionados
    $stmt = $conn->prepare("
        SELECT pwp.*, r.nombre_producto 
        FROM pedidos_web_productos pwp
        JOIN productos_web pw ON pwp.id_producto_web = pw.id_producto_web
        JOIN recetas r ON pw.id_receta = r.id_receta
        WHERE pwp.id_pedido_web = ?
    ");
    $stmt->execute([$pedido['id_pedido_web']]);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
    die("❌ Error: " . $e->getMessage());
}
?>

<div class="comprobante">
    <h2 class="titulo">📄 Comprobante de Pedido</h2>
    <p><b>Número de Pedido:</b> <?= $pedido['order_number'] ?></p>
    <p><b>Cliente:</b> <?= $pedido['nombre'] . " " . $pedido['apellido_paterno'] . " " . $pedido['apellido_materno'] ?></p>
    <p><b>Correo:</b> <?= $pedido['correo'] ?> | <b>Teléfono:</b> <?= $pedido['telefono'] ?></p>
    <p><b>Dirección:</b> <?= "{$pedido['calle']} {$pedido['numero']}, {$pedido['colonia']}, {$pedido['municipio']}, {$pedido['ciudad']}, {$pedido['estado']}, CP {$pedido['codigo_postal']}, {$pedido['pais']}" ?></p>
    <p><b>Tipo de Envío:</b> <?= ucfirst($pedido['tipo_envio']) ?> | <b>Observaciones:</b> <?= $pedido['referencias'] ?></p>

    <h4>Productos:</h4>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($productos as $p): ?>
            <tr>
                <td><?= $p['nombre_producto'] ?></td>
                <td><?= $p['cantidad'] ?></td>
                <td>$<?= number_format($p['precio_unitario'], 2) ?></td>
                <td>$<?= number_format($p['precio_unitario'] * $p['cantidad'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <p><b>Subtotal:</b> $<?= number_format($pedido['subtotal'], 2) ?></p>
    <p><b>Costo Envío:</b> $<?= number_format($pedido['costo_envio'], 2) ?></p>
    <p><b>Total:</b> $<?= number_format($pedido['total'], 2) ?> MXN</p>

    <div class="text-center print-btn">
        <button onclick="window.print()" class="btn btn-success">🖨️ Imprimir / Guardar PDF</button>
        <a href="/" class="btn btn-secondary">🏠 Volver al inicio</a>
    </div>
</div>

