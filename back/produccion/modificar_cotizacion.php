<?php
include '../db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->beginTransaction();

        $id_cotizacion = $_POST['id_cotizacion'];
        $id_direccion = $_POST['id_direccion'];
        $tipo_envio = $_POST['tipo_envio'];
        $fecha_entrega = $_POST['fecha_entrega']; // 🚀 nuevo campo
        $observaciones = $_POST['observaciones'];
        $costo_envio = $_POST['costo_envio'];
        $costo_total = $_POST['costo_total'];

        // Actualizar los datos generales de la cotización incluyendo fecha_entrega
        $stmt = $conn->prepare("UPDATE cotizaciones 
            SET id_direccion = ?, tipo_envio = ?, fecha_entrega = ?, observaciones = ?, costo_envio = ?, total = ? 
            WHERE id_cotizacion = ?");
        $stmt->execute([$id_direccion, $tipo_envio, $fecha_entrega, $observaciones, $costo_envio, $costo_total, $id_cotizacion]);

        // Decodificar productos y regalos
        $productos = isset($_POST['productos']) ? json_decode($_POST['productos'], true) : [];
        $regalos = isset($_POST['regalos']) ? json_decode($_POST['regalos'], true) : [];

        // Eliminar los productos actuales
        $stmt = $conn->prepare("DELETE FROM cotizaciones_productos WHERE id_cotizacion = ?");
        $stmt->execute([$id_cotizacion]);

        // Insertar los nuevos productos
        if (!empty($productos)) {
            $stmt = $conn->prepare("INSERT INTO cotizaciones_productos (id_cotizacion, id_producto, cantidad) VALUES (?, ?, ?)");
            foreach ($productos as $producto) {
                $stmt->execute([$id_cotizacion, $producto['id'], $producto['cantidad']]);
            }
        }

        // Eliminar los regalos actuales
        $stmt = $conn->prepare("DELETE FROM cotizaciones_regalos WHERE id_cotizacion = ?");
        $stmt->execute([$id_cotizacion]);

        // Insertar los nuevos regalos
        if (!empty($regalos)) {
            $stmt = $conn->prepare("INSERT INTO cotizaciones_regalos (id_cotizacion, id_regalo, cantidad) VALUES (?, ?, ?)");
            foreach ($regalos as $regalo) {
                $stmt->execute([$id_cotizacion, $regalo['id'], $regalo['cantidad']]);
            }
        }

        $conn->commit();
        header("Location: ../../front/produccion/pedidos_vendidos_asignacion.php?success=1");
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Error al actualizar la cotización: " . $e->getMessage();
    }
} else {
    echo "Acceso no autorizado.";
}
?>
