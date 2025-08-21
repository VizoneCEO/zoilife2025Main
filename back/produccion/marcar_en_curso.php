<?php
include '../../back/db/connection.php';
header('Content-Type: application/json');

if (!isset($_POST['id_cotizacion']) || !is_numeric($_POST['id_cotizacion'])) {
    echo json_encode(["success" => false, "error" => "ID inválido."]);
    exit();
}

$id = $_POST['id_cotizacion'];

try {
    $stmt = $conn->prepare("UPDATE cotizaciones SET estatus = 'en curso' WHERE id_cotizacion = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
