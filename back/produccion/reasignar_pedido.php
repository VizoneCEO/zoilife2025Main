<?php
include '../db/connection.php';
header('Content-Type: application/json');

if (!isset($_POST['id_cotizacion']) || !isset($_POST['id_usuario'])) {
    echo json_encode([
        "success" => false,
        "error" => "Faltan datos obligatorios."
    ]);
    exit();
}

$id_cotizacion = $_POST['id_cotizacion'];
$id_usuario = $_POST['id_usuario'];

try {
    // Verificar si la cotización existe
    $stmtCheckCot = $conn->prepare("SELECT id_cotizacion FROM cotizaciones WHERE id_cotizacion = :id");
    $stmtCheckCot->bindParam(':id', $id_cotizacion, PDO::PARAM_INT);
    $stmtCheckCot->execute();

    if ($stmtCheckCot->rowCount() === 0) {
        echo json_encode(["success" => false, "error" => "La cotización no existe."]);
        exit();
    }

    // Verificar si ya tiene asignación previa
    $stmtCheck = $conn->prepare("SELECT id_asignacion FROM asignaciones_entrega WHERE id_cotizacion = :id");
    $stmtCheck->bindParam(':id', $id_cotizacion, PDO::PARAM_INT);
    $stmtCheck->execute();

    if ($stmtCheck->rowCount() > 0) {
        // Ya existe → REASIGNAR (UPDATE)
        $stmtUpdate = $conn->prepare("UPDATE asignaciones_entrega SET id_usuario = :usuario, fecha_asignacion = NOW() WHERE id_cotizacion = :id");
    } else {
        // No existe → ASIGNAR (INSERT)
        $stmtUpdate = $conn->prepare("INSERT INTO asignaciones_entrega (id_cotizacion, id_usuario) VALUES (:id, :usuario)");
    }

    $stmtUpdate->bindParam(':id', $id_cotizacion, PDO::PARAM_INT);
    $stmtUpdate->bindParam(':usuario', $id_usuario, PDO::PARAM_INT);
    $stmtUpdate->execute();

    // Actualizar estatus a 'asignada'
    $stmtStatus = $conn->prepare("UPDATE cotizaciones SET estatus = 'asignada' WHERE id_cotizacion = :id");
    $stmtStatus->bindParam(':id', $id_cotizacion, PDO::PARAM_INT);
    $stmtStatus->execute();

    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "error" => "Error en la base de datos: " . $e->getMessage()
    ]);
}
