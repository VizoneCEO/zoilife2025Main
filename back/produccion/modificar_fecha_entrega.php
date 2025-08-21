<?php
include '../db/connection.php';

$id = $_POST['id_cotizacion'];
$fecha = $_POST['fecha_entrega'];

try {
    $stmt = $conn->prepare("UPDATE cotizaciones SET fecha_entrega = ? WHERE id_cotizacion = ?");
    $stmt->execute([$fecha, $id]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
