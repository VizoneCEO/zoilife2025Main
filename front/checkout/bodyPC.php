
<?php

include '../../back/db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $apellido_paterno = $_POST['apellido_paterno'];
    $apellido_materno = $_POST['apellido_materno'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $calle = $_POST['calle'];
    $numero = $_POST['numero_casa'];
    $colonia = $_POST['colonia'];
    $municipio = $_POST['alcaldia_municipio'];
    $ciudad = $_POST['ciudad'];
    $estado = $_POST['estado'];
    $cp = $_POST['codigo_postal'];
    $pais = $_POST['pais'];
    $tipo_envio = $_POST['tipo_envio'];
    $observaciones = $_POST['observaciones'];
    $total = $_POST['total'];
    $carrito = json_decode($_POST['carrito_json'], true);


}
?>

<div class="container mt-5">
    <h3>Confirmación de Pedido</h3>
    <p><strong>Cliente:</strong> <?= "$nombre $apellido_paterno $apellido_materno" ?></p>
    <p><strong>Correo:</strong> <?= $correo ?> | <strong>Tel:</strong> <?= $telefono ?></p>
    <p><strong>Dirección:</strong> <?= "$calle $numero, $colonia, $municipio, $ciudad, $estado, $cp, $pais" ?></p>
    <p><strong>Envío:</strong> <?= $tipo_envio ?> | <strong>Observaciones:</strong> <?= $observaciones ?></p>

    <h4>Productos:</h4>
    <ul>
        <?php
        $ids = implode(',', array_keys($carrito));
        $stmt = $conn->prepare("SELECT pw.id_producto_web, r.nombre_producto, pw.precio FROM productos_web pw JOIN recetas r ON pw.id_receta = r.id_receta WHERE pw.id_producto_web IN ($ids)");
        $stmt->execute();
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($productos as $p):
            $cantidad = $carrito[$p['id_producto_web']];
            ?>
            <li><?= $p['nombre_producto'] ?> (x<?= $cantidad ?>) - $<?= number_format($p['precio'] * $cantidad, 2) ?></li>
        <?php endforeach; ?>
    </ul>

    <p><strong>Total:</strong> $<?= number_format($total, 2) ?></p>

    <form action="procesar_pago.php" method="POST">
        <input type="hidden" name="nombre" value="<?= $nombre ?>">
        <input type="hidden" name="apellido_paterno" value="<?= $apellido_paterno ?>">
        <input type="hidden" name="apellido_materno" value="<?= $apellido_materno ?>">
        <input type="hidden" name="correo" value="<?= $correo ?>">
        <input type="hidden" name="telefono" value="<?= $telefono ?>">

        <input type="hidden" name="calle" value="<?= $calle ?>">
        <input type="hidden" name="numero_casa" value="<?= $numero ?>">
        <input type="hidden" name="colonia" value="<?= $colonia ?>">
        <input type="hidden" name="alcaldia_municipio" value="<?= $municipio ?>">
        <input type="hidden" name="ciudad" value="<?= $ciudad ?>">
        <input type="hidden" name="estado" value="<?= $estado ?>">
        <input type="hidden" name="codigo_postal" value="<?= $cp ?>">
        <input type="hidden" name="pais" value="<?= $pais ?>">

        <input type="hidden" name="tipo_envio" value="<?= $tipo_envio ?>">
        <input type="hidden" name="observaciones" value="<?= $observaciones ?>">
        <input type="hidden" name="total" value="<?= $total ?>">
        <input type="hidden" name="carrito_json" value='<?= json_encode($carrito) ?>'>

        <button type="submit" class="btn btn-primary">Proceder a pagar con Clip</button>
    </form>

</div>
