<?php
include '../../back/db/connection.php'; // Conexión a la base de datos

try {
    // Obtener todos los productos terminados activos
    $query = "SELECT id_receta, nombre_producto, unidad_medida FROM recetas WHERE estatus = 'activo'";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al recuperar los datos: " . $e->getMessage());
}
?>

<div class="container mt-4">
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Regresar al Dashboard</a>
    <h1 class="text-center mb-4">Stock de Productos Terminados</h1>

    <div class="row">
        <?php foreach ($productos as $producto): ?>
            <?php
            try {
                // Cantidad producida
                $query_producido = "SELECT SUM(cantidad) as total_producido FROM requisiciones 
                    WHERE id_producto = ? AND estatus = 'procesado'";
                $stmt_producido = $conn->prepare($query_producido);
                $stmt_producido->execute([$producto['id_receta']]);
                $producido = $stmt_producido->fetch(PDO::FETCH_ASSOC);
                $cantidad_producida = $producido['total_producido'] ?? 0;

                // Cantidad entregada
                $query_entregado = "SELECT SUM(cp.cantidad) as total_entregado
                      FROM cotizaciones_productos cp
                      JOIN cotizaciones c ON cp.id_cotizacion = c.id_cotizacion
                      WHERE cp.id_producto = ? AND c.estatus = 'entregado'";

                $stmt_entregado = $conn->prepare($query_entregado);
                $stmt_entregado->execute([$producto['id_receta']]);
                $entregado = $stmt_entregado->fetch(PDO::FETCH_ASSOC);
                $cantidad_entregada = $entregado['total_entregado'] ?? 0;

                // Stock disponible
                $stock_disponible = $cantidad_producida - $cantidad_entregada;

            } catch (PDOException $e) {
                $cantidad_producida = "Error";
                $cantidad_entregada = "Error";
                $stock_disponible = "Error";
            }
            ?>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?= htmlspecialchars($producto['nombre_producto']) ?></h5>
                        <p class="card-text"><strong>Total Producido:</strong> <?= number_format($cantidad_producida, 2) ?> <?= htmlspecialchars($producto['unidad_medida']) ?></p>
                        <p class="card-text text-danger"><strong>Total Entregado:</strong> <?= number_format($cantidad_entregada, 2) ?> <?= htmlspecialchars($producto['unidad_medida']) ?></p>
                        <p class="card-text"><strong>Stock Disponible:</strong> <?= number_format($stock_disponible, 2) ?> <?= htmlspecialchars($producto['unidad_medida']) ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
