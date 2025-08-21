<?php
include '../../back/db/connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

$id_pedido_web = $_POST['id_pedido_web'] ?? null;
$id_usuario    = $_POST['id_usuario'] ?? null;

if (!$id_pedido_web || !$id_usuario) {
    echo json_encode(['success' => false, 'error' => 'Faltan parámetros']);
    exit;
}

try {
    // Insertar la asignación
    $stmt = $conn->prepare("INSERT INTO asignaciones_web (id_pedido_web, id_usuario) VALUES (?, ?)");
    $stmt->execute([$id_pedido_web, $id_usuario]);

    // Actualizar estatus del pedido a "asignado"
    $stmt2 = $conn->prepare("UPDATE pedidos_web SET estatus_pedido = 'asignada' WHERE id_pedido_web = ?");
    $stmt2->execute([$id_pedido_web]);

    echo json_encode(['success' => true, 'message' => 'Pedido asignado correctamente']);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
