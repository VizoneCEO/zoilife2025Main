<?php
session_start();
include '../../back/db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id_regalo = $_GET['id'];

    try {
        // Verificar si el regalo existe y obtener su estado actual
        $query = "SELECT estatus FROM regalos WHERE id_regalo = :id_regalo";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_regalo', $id_regalo, PDO::PARAM_INT);
        $stmt->execute();
        $regalo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($regalo) {
            // Cambiar el estado a "inactivo" si está activo, o viceversa
            $nuevo_estado = ($regalo['estatus'] === 'activo') ? 'inactivo' : 'activo';

            // Actualizar el estatus en la base de datos
            $query = "UPDATE regalos SET estatus = :estatus WHERE id_regalo = :id_regalo";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':estatus', $nuevo_estado, PDO::PARAM_STR);
            $stmt->bindParam(':id_regalo', $id_regalo, PDO::PARAM_INT);
            $stmt->execute();

            $_SESSION['success'] = "Regalo actualizado correctamente.";
        } else {
            $_SESSION['error'] = "Regalo no encontrado.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al actualizar el estado del regalo: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Solicitud no válida.";
}

header("Location: ../../front/produccion/catalogo_regalos.php");
exit();
?>
