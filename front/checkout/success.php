<?php
require_once '../../back/db/connection.php';

// Recibir número de pedido
$order = $_GET['order'] ?? null;

if (!$order) {
    die("❌ No se recibió número de pedido.");
}

try {
    // Actualizar estatus de pago
    $sql = "UPDATE pedidos_web 
            SET estatus_pago = 'pagado', estatus_pedido = 'procesando' 
            WHERE order_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$order]);

    if ($stmt->rowCount() === 0) {
        die("⚠️ Pedido no encontrado: $order");
    }

    // Obtener datos del pedido
    $stmt = $conn->prepare("SELECT * FROM pedidos_web WHERE order_number = ?");
    $stmt->execute([$order]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

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
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago exitoso - Zoilife</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow-lg p-4">
        <h2 class="text-success">✅ Pago exitoso</h2>
        <p>Gracias <strong><?= htmlspecialchars($pedido['nombre'] . " " . $pedido['apellido_paterno']) ?></strong>, hemos recibido tu pago.</p>
        <p><b>Número de pedido:</b> <?= htmlspecialchars($pedido['order_number']) ?></p>
        <p><b>Total:</b> $<?= number_format($pedido['total'], 2) ?> MXN</p>

        <h4>Productos:</h4>
        <ul>
            <?php foreach ($productos as $p): ?>
                <li><?= htmlspecialchars($p['nombre_producto']) ?> (x<?= (int)$p['cantidad'] ?>) -
                    $<?= number_format($p['precio_unitario'] * $p['cantidad'], 2) ?></li>
            <?php endforeach; ?>
        </ul>

        <div class="mt-4">
            <a href="descargar_comprobante.php?order=<?= urlencode($pedido['order_number']) ?>" class="btn btn-primary">
                📄 Descargar comprobante PDF
            </a>
            <a href="/" class="btn btn-secondary">🏠 Volver al inicio</a>
        </div>
    </div>
</div>
</body>
</html>
