<?php
session_start();
include '../../back/db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id']) && isset($_GET['cliente'])) {
    $id_direccion = $_GET['id'];
    $id_cliente = $_GET['cliente']; // Obtener ID del cliente para la redirección

    try {
        // Verificar si la dirección existe
        $query = "SELECT * FROM direcciones WHERE id_direccion = :id_direccion";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_direccion', $id_direccion, PDO::PARAM_INT);
        $stmt->execute();
        $direccion = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($direccion) {
            // Marcar la dirección como inactiva en lugar de eliminarla físicamente
            $query = "UPDATE direcciones SET estatus = 'inactivo' WHERE id_direccion = :id_direccion";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id_direccion', $id_direccion, PDO::PARAM_INT);
            $stmt->execute();

            $_SESSION['success'] = "Dirección eliminada correctamente.";
        } else {
            $_SESSION['error'] = "Dirección no encontrada.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al eliminar dirección: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Solicitud no válida.";
}

// Redirigir nuevamente a la lista de direcciones del cliente
header("Location: ../../front/ventas/direcciones.php?id=" . urlencode($id_cliente));
exit();
?>
