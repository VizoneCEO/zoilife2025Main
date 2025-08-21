<?php
include '../../back/db/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p class='text-danger text-center'>Cotización no válida.</p>";
    exit();
}

$id_cotizacion = $_GET['id'];

// Obtener detalles de la cotización
$queryCotizacion = "SELECT c.*, 
                           CONCAT(cl.nombre, ' ', cl.apellido_paterno, ' ', cl.apellido_materno) AS nombre_cliente,
                           d.calle, d.numero_casa, d.numero_interior, d.alcaldia_municipio, d.colonia, d.entre_calles,
                           d.ciudad, d.estado,d.codigo_postal,c.fecha_entrega
                    FROM cotizaciones c
                    JOIN clientes cl ON c.id_cliente = cl.id_cliente
                    JOIN direcciones d ON c.id_direccion = d.id_direccion
                    WHERE c.id_cotizacion = :id_cotizacion";

$stmt = $conn->prepare($queryCotizacion);
$stmt->bindParam(':id_cotizacion', $id_cotizacion, PDO::PARAM_INT);
$stmt->execute();
$cotizacion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cotizacion) {
    echo "<p class='text-danger text-center'>Cotización no encontrada.</p>";
    exit();
}

// Obtener productos
$queryProductos = "SELECT p.nombre_producto, cp.cantidad, cp.precio_unitario 
                   FROM cotizaciones_productos cp
                   JOIN recetas p ON cp.id_producto = p.id_receta
                   WHERE cp.id_cotizacion = :id_cotizacion";

$stmt = $conn->prepare($queryProductos);
$stmt->bindParam(':id_cotizacion', $id_cotizacion, PDO::PARAM_INT);
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener regalos
$queryRegalos = "SELECT r.nombre, cr.cantidad 
                 FROM cotizaciones_regalos cr
                 JOIN regalos r ON cr.id_regalo = r.id_regalo
                 WHERE cr.id_cotizacion = :id_cotizacion";

$stmt = $conn->prepare($queryRegalos);
$stmt->bindParam(':id_cotizacion', $id_cotizacion, PDO::PARAM_INT);
$stmt->execute();
$regalos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div>
    <h5>Datos del Cliente</h5>
    <p><strong>Cliente:</strong> <?= htmlspecialchars($cotizacion['nombre_cliente']) ?></p>

    <p><strong>Calle:</strong> <?= htmlspecialchars($cotizacion['calle']) ?></p>
    <p><strong>Número Exterior:</strong> <?= htmlspecialchars($cotizacion['numero_casa']) ?></p>

    <?php if (!empty($cotizacion['numero_interior'])): ?>
        <p><strong>Número Interior:</strong> <?= htmlspecialchars($cotizacion['numero_interior']) ?></p>
    <?php endif; ?>
    <p><strong>Código Postal:</strong> <?= htmlspecialchars($cotizacion['codigo_postal']) ?></p>
    <p><strong>Colonia:</strong> <?= htmlspecialchars($cotizacion['colonia']) ?></p>


    <p><strong>Alcaldía o Municipio:</strong> <?= htmlspecialchars($cotizacion['alcaldia_municipio']) ?></p>
    <p><strong>Ciudad:</strong> <?= htmlspecialchars($cotizacion['ciudad']) ?></p>
    <p><strong>Estado:</strong> <?= htmlspecialchars($cotizacion['estado']) ?></p>

    <?php if (!empty($cotizacion['entre_calles'])): ?>
        <p><strong>Entre Calles:</strong> <?= htmlspecialchars($cotizacion['entre_calles']) ?></p>
    <?php endif; ?>


    <p><strong>Tipo de Envío:</strong> <?= htmlspecialchars($cotizacion['tipo_envio']) ?></p>
    <p><strong style="background: yellow; padding: 3px 6px;">Fecha de Entrega:</strong> <?= date('d/m/Y', strtotime($cotizacion['fecha_entrega'])) ?></p>
    <p><strong>Observaciones:</strong> <?= htmlspecialchars($cotizacion['observaciones']) ?></p>
    <p><strong>Costo de Envío:</strong> $<?= number_format($cotizacion['costo_envio'], 2) ?></p>
    <p><strong>Sub Total:</strong> $<?= number_format($cotizacion['total'], 2) ?></p>
    <?php $totalFinal = $cotizacion['total'] + $cotizacion['costo_envio']; ?>
    <p><strong>Total (productos + envío):</strong> $<?= number_format($totalFinal, 2) ?></p>

</div>

<h5 class="mt-3">Productos</h5>
<table class="table table-bordered">
    <thead>
    <tr>
        <th>Producto</th>
        <th>Cantidad</th>
        <th>Precio Unitario</th>
        <th>Subtotal</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($productos as $producto): ?>
        <tr>
            <td><?= htmlspecialchars($producto['nombre_producto']) ?></td>
            <td><?= htmlspecialchars($producto['cantidad']) ?></td>
            <td>$<?= number_format($producto['precio_unitario'], 2) ?></td>
            <td>$<?= number_format($producto['cantidad'] * $producto['precio_unitario'], 2) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h5 class="mt-3">Regalos</h5>
<table class="table table-bordered">
    <thead>
    <tr>
        <th>Regalo</th>
        <th>Cantidad</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($regalos as $regalo): ?>
        <tr>
            <td><?= htmlspecialchars($regalo['nombre']) ?></td>
            <td><?= htmlspecialchars($regalo['cantidad']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
