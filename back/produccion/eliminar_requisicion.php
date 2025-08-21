<?php
session_start();
include '../db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_requisicion = intval($_POST['id_requisicion']);
    $usuario_responsable = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Desconocido';

    // Validar que se recibió un ID válido
    if (empty($id_requisicion) || $id_requisicion <= 0) {
        header('Location: ../../front/produccion/requisiciones_realizadas.php?error=ID inválido.');
        exit();
    }

    try {
        // Verificar si la requisición existe y no está procesada
        $checkQuery = "SELECT estatus FROM requisiciones WHERE id_requisicion = :id";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bindParam(':id', $id_requisicion, PDO::PARAM_INT);
        $checkStmt->execute();
        $requisicion = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if (!$requisicion) {
            header('Location: ../../front/produccion/requisiciones_realizadas.php?error=Requisición no encontrada.');
            exit();
        }

        // Actualizar el estado a "rechazado"
        $updateQuery = "UPDATE requisiciones SET estatus = 'rechazado' WHERE id_requisicion = :id";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(':id', $id_requisicion, PDO::PARAM_INT);

        if ($updateStmt->execute()) {
            // Registro exitoso, redirigir con mensaje de éxito
            header('Location: ../../front/produccion/requiciones_realizadas.php?success=Requisición eliminada correctamente.');
            exit();
        } else {
            // Error en la actualización
            header('Location: ../../front/produccion/requiciones_realizadas.php?error=Error al eliminar la requisición.');
            exit();
        }
    } catch (PDOException $e) {
        header('Location: ../../front/produccion/requiciones_realizadas.php?error=Error en la base de datos: ' . $e->getMessage());
        exit();
    }
} else {
    // Redirigir si no es POST
    header('Location: ../../front/produccion/requiciones_realizadas.php?error=Método no permitido.');
    exit();
}
