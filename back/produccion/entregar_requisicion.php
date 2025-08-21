<?php
session_start();
include '../db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_requisicion = intval($_POST['id_requisicion']);
    $usuario_responsable = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Desconocido';

    // Validar que se recibió un ID válido
    if (empty($id_requisicion) || $id_requisicion <= 0) {
        header('Location: ../../front/produccion/salida_producto.php?error=ID inválido.');
        exit();
    }

    try {
        // Verificar si la requisición existe y está pendiente
        $checkQuery = "SELECT estatus FROM requisiciones WHERE id_requisicion = :id";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bindParam(':id', $id_requisicion, PDO::PARAM_INT);
        $checkStmt->execute();
        $requisicion = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if (!$requisicion) {
            header('Location: ../../front/produccion/salida_producto.php?error=Requisición no encontrada.');
            exit();
        }

        if ($requisicion['estatus'] !== 'pendiente') {
            header('Location: ../../front/produccion/salida_producto.php?error=La requisición ya ha sido procesada o rechazada.');
            exit();
        }

        // Actualizar el estado de la requisición a "procesado"
        $updateQuery = "UPDATE requisiciones SET estatus = 'procesado' WHERE id_requisicion = :id";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(':id', $id_requisicion, PDO::PARAM_INT);

        if ($updateStmt->execute()) {
            header('Location: ../../front/produccion/salida_producto.php?success=Requisición entregada correctamente.');
            exit();
        } else {
            header('Location: ../../front/produccion/salida_producto.php?error=Error al procesar la requisición.');
            exit();
        }
    } catch (PDOException $e) {
        header('Location: ../../front/produccion/salida_producto.php?error=Error en la base de datos: ' . $e->getMessage());
        exit();
    }
} else {
    header('Location: ../../front/produccion/salida_producto.php?error=Método no permitido.');
    exit();
}
