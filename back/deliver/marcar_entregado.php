<?php
include '../db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_cotizacion'] ?? null;
    $receptor = $_POST['receptorPedido'] ?? null;
    $metodo = $_POST['metodoPago'] ?? null;

    if (!$id || !$receptor || !$metodo) {
        echo json_encode(['success' => false, 'error' => 'Faltan datos.']);
        exit;
    }

    try {
        $stmt = $conn->prepare("UPDATE cotizaciones 
                                SET estatus = 'entregado',
                                    receptorPedido = :receptor,
                                    metodoPago = :metodo
                                WHERE id_cotizacion = :id");

        $stmt->bindParam(':receptor', $receptor);
        $stmt->bindParam(':metodo', $metodo);
        $stmt->bindParam(':id', $id);

        $stmt->execute();

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Método inválido.']);
}
