<?php
include '../../back/db/connection.php';

// Verificar si se recibe un ID de cotización válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Cotización no válida.";
    header("Location: ../../front/ventas/cotizaciones.php");
    exit();
}

$id_cotizacion = $_GET['id'];

// Obtener detalles de la cotización
$queryCotizacion = "SELECT c.*, 
                           CONCAT(cl.nombre, ' ', cl.apellido_paterno, ' ', cl.apellido_materno) AS nombre_cliente,
                           d.calle, d.numero_casa, d.ciudad, d.estado,
                           c.fecha_entrega

                    FROM cotizaciones c
                    JOIN clientes cl ON c.id_cliente = cl.id_cliente
                    JOIN direcciones d ON c.id_direccion = d.id_direccion
                    WHERE c.id_cotizacion = :id_cotizacion";
$stmt = $conn->prepare($queryCotizacion);
$stmt->bindParam(':id_cotizacion', $id_cotizacion, PDO::PARAM_INT);
$stmt->execute();
$cotizacion = $stmt->fetch(PDO::FETCH_ASSOC);

// Si la cotización no existe, redirigir
if (!$cotizacion) {
    $_SESSION['error'] = "Cotización no encontrada.";
    header("Location: ../../front/ventas/cotizaciones.php");
    exit();
}

// Obtener productos de la cotización
$queryProductos = "SELECT p.nombre_producto, cp.cantidad, cp.precio_unitario 
                   FROM cotizaciones_productos cp
                   JOIN recetas p ON cp.id_producto = p.id_receta
                   WHERE cp.id_cotizacion = :id_cotizacion";
$stmt = $conn->prepare($queryProductos);
$stmt->bindParam(':id_cotizacion', $id_cotizacion, PDO::PARAM_INT);
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener regalos de la cotización
$queryRegalos = "SELECT r.nombre, cr.cantidad 
                 FROM cotizaciones_regalos cr
                 JOIN regalos r ON cr.id_regalo = r.id_regalo
                 WHERE cr.id_cotizacion = :id_cotizacion";
$stmt = $conn->prepare($queryRegalos);
$stmt->bindParam(':id_cotizacion', $id_cotizacion, PDO::PARAM_INT);
$stmt->execute();
$regalos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <a href="cotizaciones.php" class="btn btn-secondary mb-3">← Regresar a Cotizaciones</a>

    <h1 class="text-center">Detalle de Cotización #<?= htmlspecialchars($id_cotizacion) ?></h1>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Datos del Cliente</h5>
            <p><strong>Cliente:</strong> <?= htmlspecialchars($cotizacion['nombre_cliente']) ?></p>
            <p><strong>Dirección de Envío:</strong> <?= htmlspecialchars($cotizacion['calle'] . ' ' . $cotizacion['numero_casa'] . ', ' . $cotizacion['ciudad'] . ', ' . $cotizacion['estado']) ?></p>
            <p><strong>Tipo de Envío:</strong> <?= htmlspecialchars($cotizacion['tipo_envio']) ?></p>
            <p><strong>Tipo de Envío:</strong> <?= htmlspecialchars($cotizacion['tipo_envio']) ?></p>
            <p><strong style="background: yellow; padding: 3px 6px;">Fecha de Entrega:</strong> <?= date('d/m/Y', strtotime($cotizacion['fecha_entrega'])) ?></p>


            <p><strong>Observaciones:</strong> <?= htmlspecialchars($cotizacion['observaciones']) ?></p>
            <p><strong>Costo de Envío:</strong> $<?= number_format($cotizacion['costo_envio'], 2) ?></p>
            <p><strong>Total:</strong> $<?= number_format($cotizacion['total'], 2) ?></p>
        </div>
    </div>

    <h3 class="mt-4">Productos Cotizados</h3>
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

    <h3 class="mt-4">Regalos</h3>
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
</div>
