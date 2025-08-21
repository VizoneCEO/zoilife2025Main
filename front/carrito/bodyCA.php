<?php

include '../../back/db/connection.php';

$carrito = $_SESSION['carrito'] ?? [];
$productos = [];

if (!empty($carrito)) {
    $ids = implode(',', array_keys($carrito));
    $stmt = $conn->prepare("
    SELECT pw.id_producto_web, pw.precio, pw.foto_principal, r.nombre_producto
    FROM productos_web pw
    JOIN recetas r ON pw.id_receta = r.id_receta
    WHERE pw.id_producto_web IN ($ids)
");

    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>




<div class="container mt-5 mb-5">
    <h2 class="mb-4">🛒 Tu Carrito</h2>

    <?php if (empty($productos)): ?>
        <div class="alert alert-info">Tu carrito está vacío.</div>
        <a href="../productos/productos.php" class="btn btn-success">Seguir comprando</a>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
                <th>Eliminar</th>
            </tr>
            </thead>
            <tbody>
            <?php $total = 0; foreach ($productos as $p):
                $cantidad = $carrito[$p['id_producto_web']];
                $subtotal = $p['precio'] * $cantidad;
                $total += $subtotal;
                ?>
                <tr>
                    <td>
                        <img src="../productosWeb/<?= $p['foto_principal'] ?>" width="50" height="50" style="object-fit:cover; border-radius:5px;">
                        <?= htmlspecialchars($p['nombre_producto']) ?>
                    </td>
                    <td>$<?= number_format($p['precio'], 2) ?></td>
                    <td><?= $cantidad ?></td>
                    <td>$<?= number_format($subtotal, 2) ?></td>
                    <td>
                        <form method="POST" action="eliminar_producto.php" onsubmit="return confirm('¿Eliminar este producto?');">
                            <input type="hidden" name="id_producto" value="<?= $p['id_producto_web'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">X</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="text-end">
            <h4>Total: $<?= number_format($total, 2) ?></h4>
        <a href="../checkout/checkout.php" class="btn btn-primary mt-3">Proceder al pago</a>

        </div>
    <?php endif; ?>
</div>

