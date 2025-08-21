<?php
include '../db/connection.php';
header('Content-Type: application/json');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(["success" => false, "message" => "ID de pedido inválido"]);
    exit();
}

$id_cotizacion = $_GET['id'];

try {
    // Actualizar el estado de la cotización a "cancelada"
    $query = "UPDATE cotizaciones SET estatus = 'eliminada' WHERE id_cotizacion = :id_cotizacion";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_cotizacion', $id_cotizacion, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al eliminar el pedido."]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
