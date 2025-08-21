<?php
include '../db/connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_pedido_web'] ?? null;
    $receptor = $_POST['receptorPedido'] ?? null;

    if (!$id || !$receptor) {
        echo json_encode(['success' => false, 'error' => 'Faltan datos.']);
        exit;
    }

    try {
        $stmt = $conn->prepare("UPDATE pedidos_web 
                                SET estatus_pedido = 'entregado',
                                    receptor_pedido = :receptor,
                                    fecha_actualizacion = NOW()
                                WHERE id_pedido_web = :id");

        $stmt->bindParam(':receptor', $receptor);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Pedido no encontrado o sin cambios.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Método inválido.']);
}
