<?php
include '../../back/db/connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

$id_pedido_web = $_POST['id_pedido_web'] ?? null;

if (!$id_pedido_web) {
    echo json_encode(['success' => false, 'error' => 'Falta el ID del pedido']);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE pedidos_web SET estatus_pedido = 'cancelado' WHERE id_pedido_web = :id");
    $stmt->bindParam(':id', $id_pedido_web, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Pedido eliminado correctamente']);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se encontró el pedido']);
    }
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
