<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db/connection.php';
header('Content-Type: application/json');

if (!isset($_POST['id_cotizacion']) || !isset($_POST['id_usuario'])) {
    echo json_encode(["success" => false, "error" => "Faltan datos"]);
    exit();
}

$id_cotizacion = $_POST['id_cotizacion'];
$id_usuario = $_POST['id_usuario'];

try {
    // Verificar si la cotización existe
    $checkQuery = "SELECT id_cotizacion FROM cotizaciones WHERE id_cotizacion = :id_cotizacion";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bindParam(':id_cotizacion', $id_cotizacion, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        echo json_encode(["success" => false, "error" => "La cotización no existe"]);
        exit();
    }

    // Insertar asignación
    $queryInsert = "INSERT INTO asignaciones_entrega (id_cotizacion, id_usuario) VALUES (:id_cotizacion, :id_usuario)";
    $stmt = $conn->prepare($queryInsert);
    $stmt->bindParam(':id_cotizacion', $id_cotizacion, PDO::PARAM_INT);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();

    // Actualizar estatus de la cotización
    $queryUpdate = "UPDATE cotizaciones SET estatus = 'asignada' WHERE id_cotizacion = :id_cotizacion";
    $stmt = $conn->prepare($queryUpdate);
    $stmt->bindParam(':id_cotizacion', $id_cotizacion, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(["success" => true]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
