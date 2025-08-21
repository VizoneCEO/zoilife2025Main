<?php
header('Content-Type: application/json');
include '../db/connection.php'; // Asegúrate que esta ruta es correcta según tu estructura

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'error' => 'ID no proporcionado o inválido']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT total, costo_envio FROM cotizaciones WHERE id_cotizacion = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        echo json_encode([
            'success' => true,
            'total' => floatval($row['total']),
            'costo_envio' => floatval($row['costo_envio'])
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Cotización no encontrada']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Error en la base de datos']);
}
