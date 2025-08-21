<?php
session_start();
include '../db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_ingrediente = intval($_POST['id_ingrediente']);
    $id_receta = intval($_POST['id_receta']);

    if (empty($id_ingrediente) || empty($id_receta)) {
        $_SESSION['error'] = "Datos inválidos. Verifique la información.";
        header("Location: ../../front/produccion/ingredientes_receta.php?id=$id_receta");
        exit();
    }

    try {
        $query = "UPDATE ingredientes_receta SET estatus = 'inactivo' WHERE id_ingrediente = :id_ingrediente";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_ingrediente', $id_ingrediente, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Ingrediente eliminado correctamente.";
        } else {
            $_SESSION['error'] = "Error al eliminar el ingrediente. Intente nuevamente.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error en la base de datos: " . $e->getMessage();
    }

    header("Location: ../../front/produccion/ingredientes_receta.php?id=$id_receta");
    exit();
} else {
    $_SESSION['error'] = "Método no permitido.";
    header('Location: ../../front/produccion/catalogo_recetas.php');
    exit();
}
?>
