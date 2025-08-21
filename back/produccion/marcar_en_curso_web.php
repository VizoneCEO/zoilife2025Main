<?php
include '../../back/db/connection.php';
header('Content-Type: application/json');

// Validar que venga el ID del pedido web
if (!isset($_POST['id_pedido_web']) || !is_numeric($_POST['id_pedido_web'])) {
    echo json_encode(["success" => false, "error" => "ID de pedido web inválido."]);
    exit();
}

$id_pedido_web = intval($_POST['id_pedido_web']);

try {
    // Actualizar estatus del pedido web a "en curso"
    $stmt = $conn->prepare("UPDATE pedidos_web SET estatus_pedido = 'en curso' WHERE id_pedido_web = :id");
    $stmt->bindParam(':id', $id_pedido_web, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true, "message" => "Pedido web marcado como 'en curso'."]);
    } else {
        echo json_encode(["success" => false, "error" => "No se encontró el pedido web o ya estaba en curso."]);
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
