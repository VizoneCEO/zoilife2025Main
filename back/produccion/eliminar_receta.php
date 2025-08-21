<?php
session_start();
include '../db/connection.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_receta = intval($_GET['id']);

    try {
        // Cambiar el estado de la receta a 'inactivo'
        $query = "UPDATE recetas SET estatus = 'inactivo' WHERE id_receta = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id_receta, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success'] = "La receta ha sido eliminada correctamente.";
        } else {
            $_SESSION['error'] = "Error al eliminar la receta. Intente de nuevo.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error en la base de datos: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "ID de receta no válido.";
}

// Redirigir al catálogo de recetas
header('Location: ../../front/produccion/catalogo_recetas.php');
exit();
