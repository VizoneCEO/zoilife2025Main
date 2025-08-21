<?php
include '../db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("UPDATE cotizaciones SET estatus = 'cancelada' WHERE id_cotizacion = ?");
    $success = $stmt->execute([$id]);

    echo json_encode(['success' => $success]);
} else {
    echo json_encode(['success' => false]);
}
?>
