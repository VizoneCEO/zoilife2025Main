<?php
include '../db/connection.php';
header('Content-Type: application/json');

if (!isset($_POST['id_pedido_web']) || !isset($_POST['id_usuario'])) {
    echo json_encode([
        "success" => false,
        "error" => "Faltan datos obligatorios."
    ]);
    exit();
}

$id_pedido_web = intval($_POST['id_pedido_web']);
$id_usuario    = intval($_POST['id_usuario']);

try {
    // Verificar si el pedido existe (SIN filtrar estatus)
    $stmtCheck = $conn->prepare("SELECT id_pedido_web FROM pedidos_web WHERE id_pedido_web = :id LIMIT 1");
    $stmtCheck->bindParam(':id', $id_pedido_web, PDO::PARAM_INT);
    $stmtCheck->execute();

    if ($stmtCheck->rowCount() === 0) {
        echo json_encode([
            "success" => false,
            "error"   => "El pedido web no existe.",
            "debug"   => [
                "id_pedido_web" => $id_pedido_web,
                "query"         => "SELECT id_pedido_web FROM pedidos_web WHERE id_pedido_web = $id_pedido_web LIMIT 1",
                "rowCount"      => $stmtCheck->rowCount()
            ]
        ]);
        exit();
    }

    // Revisar si ya tiene asignación previa
    $stmtCheckAsignacion = $conn->prepare("SELECT id_asignacion FROM asignaciones_web WHERE id_pedido_web = :id LIMIT 1");
    $stmtCheckAsignacion->bindParam(':id', $id_pedido_web, PDO::PARAM_INT);
    $stmtCheckAsignacion->execute();

    if ($stmtCheckAsignacion->rowCount() > 0) {
        // Reasignar
        $stmtUpdate = $conn->prepare("UPDATE asignaciones_web 
                                      SET id_usuario = :usuario, fecha_asignacion = NOW() 
                                      WHERE id_pedido_web = :id");
    } else {
        // Asignar nuevo
        $stmtUpdate = $conn->prepare("INSERT INTO asignaciones_web (id_pedido_web, id_usuario, fecha_asignacion) 
                                      VALUES (:id, :usuario, NOW())");
    }

    $stmtUpdate->bindParam(':id', $id_pedido_web, PDO::PARAM_INT);
    $stmtUpdate->bindParam(':usuario', $id_usuario, PDO::PARAM_INT);
    $stmtUpdate->execute();

    // Actualizar estatus del pedido web
    $stmtStatus = $conn->prepare("UPDATE pedidos_web SET estatus_pedido = 'asignada' WHERE id_pedido_web = :id");
    $stmtStatus->bindParam(':id', $id_pedido_web, PDO::PARAM_INT);
    $stmtStatus->execute();

    echo json_encode(["success" => true, "message" => "Pedido web asignado/reasignado correctamente."]);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "error"   => "Error en la base de datos: " . $e->getMessage(),
        "debug"   => [
            "id_pedido_web" => $id_pedido_web,
            "id_usuario"    => $id_usuario
        ]
    ]);
}
