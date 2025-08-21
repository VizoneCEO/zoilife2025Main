<?php
session_start();
include '../../back/db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->beginTransaction();

        $id_cliente = $_POST['id_cliente'];
        $id_direccion = $_POST['id_direccion'];
        $tipo_envio = $_POST['tipo_envio'];
        $observaciones = trim($_POST['observaciones']);
        $costo_envio = floatval($_POST['costo_envio']);
        $costo_total = floatval($_POST['costo_total']);
        $id_usuario = $_SESSION['user_id'];
        $fecha_entrega = $_POST['fecha_entrega']; // Nuevo campo

        $query = "INSERT INTO cotizaciones (
                    id_cliente, id_usuario, id_direccion, total, 
                    costo_envio, tipo_envio, observaciones, fecha_entrega, estatus
                  ) 
                  VALUES (
                    :id_cliente, :id_usuario, :id_direccion, :total, 
                    :costo_envio, :tipo_envio, :observaciones, :fecha_entrega, 'pendiente'
                  )";

        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':id_cliente' => $id_cliente,
            ':id_usuario' => $id_usuario,
            ':id_direccion' => $id_direccion,
            ':total' => $costo_total,
            ':costo_envio' => $costo_envio,
            ':tipo_envio' => $tipo_envio,
            ':observaciones' => $observaciones,
            ':fecha_entrega' => $fecha_entrega
        ]);

        $id_cotizacion = $conn->lastInsertId();

        // Guardar productos
        if (!empty($_POST['productos'])) {
            $productos = json_decode($_POST['productos'], true);
            if (is_array($productos)) {
                $queryProductos = "INSERT INTO cotizaciones_productos 
                    (id_cotizacion, id_producto, cantidad, precio_unitario) 
                    VALUES (:id_cotizacion, :id_producto, :cantidad, :precio_unitario)";
                $stmtProductos = $conn->prepare($queryProductos);

                foreach ($productos as $producto) {
                    $stmtProductos->execute([
                        ':id_cotizacion' => $id_cotizacion,
                        ':id_producto' => intval($producto['id']),
                        ':cantidad' => intval($producto['cantidad']),
                        ':precio_unitario' => floatval($producto['precio'])
                    ]);
                }
            }
        }

        // Guardar regalos
        if (!empty($_POST['regalos'])) {
            $regalos = json_decode($_POST['regalos'], true);
            if (is_array($regalos)) {
                $queryRegalos = "INSERT INTO cotizaciones_regalos 
                    (id_cotizacion, id_regalo, cantidad) 
                    VALUES (:id_cotizacion, :id_regalo, :cantidad)";
                $stmtRegalos = $conn->prepare($queryRegalos);

                foreach ($regalos as $regalo) {
                    $stmtRegalos->execute([
                        ':id_cotizacion' => $id_cotizacion,
                        ':id_regalo' => intval($regalo['id']),
                        ':cantidad' => intval($regalo['cantidad'])
                    ]);
                }
            }
        }

        $conn->commit();

        $_SESSION['success'] = "Cotización registrada correctamente.";
        header("Location: ../../front/ventas/cotizaciones.php");
        exit();
    } catch (PDOException $e) {
        $conn->rollBack();
        $_SESSION['error'] = "Error al registrar la cotización: " . $e->getMessage();
        header("Location: ../../front/ventas/nueva_cotizacion.php?id=$id_cliente");
        exit();
    }
} else {
    $_SESSION['error'] = "Método no permitido.";
    header("Location: ../../front/ventas/clientes.php");
    exit();
}
?>
