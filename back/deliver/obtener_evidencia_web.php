<?php
require_once '../db/connection.php';

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'error' => 'ID faltante']);
    exit;
}

$id = intval($_GET['id']);

$query = "SELECT evidencia_entrega FROM pedidos_web WHERE id_pedido_web = :id LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result && !empty($result['evidencia_entrega'])) {
    echo json_encode([
        'success' => true,
        'evidencia' => $result['evidencia_entrega']
    ]);
} else {
    echo json_encode(['success' => false]);
}
