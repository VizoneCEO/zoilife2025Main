<?php
require_once '../db/connection.php';

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'error' => 'ID faltante']);
    exit;
}

$id = $_GET['id'];

$query = "SELECT evidencia FROM cotizaciones WHERE id_cotizacion = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result && !empty($result['evidencia'])) {
    echo json_encode(['success' => true, 'evidencia' => $result['evidencia']]);
} else {
    echo json_encode(['success' => false]);
}
