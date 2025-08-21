<?php
session_start();
include '../db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_receta = intval($_POST['id_receta']);
    $nombre_producto = trim($_POST['nombre_producto']);
    $cantidad = intval($_POST['cantidad']);
    $unidad_medida = trim($_POST['unidad_medida']);

    // Validar los campos básicos
    if (empty($id_receta) || empty($nombre_producto) || $cantidad <= 0 || empty($unidad_medida)) {
        $_SESSION['error'] = "Datos inválidos. Por favor, verifique la información.";
        header('Location: ../../front/produccion/editar_receta.php?id=' . $id_receta);
        exit();
    }

    try {
        // Actualizar los datos de la receta
        $query = "UPDATE recetas SET nombre_producto = :nombre_producto, cantidad = :cantidad, unidad_medida = :unidad_medida WHERE id_receta = :id_receta";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':nombre_producto', $nombre_producto, PDO::PARAM_STR);
        $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
        $stmt->bindParam(':unidad_medida', $unidad_medida, PDO::PARAM_STR);
        $stmt->bindParam(':id_receta', $id_receta, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Receta actualizada correctamente.";
        } else {
            $_SESSION['error'] = "Error al actualizar la receta. Intente nuevamente.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error en la base de datos: " . $e->getMessage();
    }

    // Redirigir al catálogo de recetas
    header('Location: ../../front/produccion/catalogo_recetas.php');
    exit();
} else {
    $_SESSION['error'] = "Método no permitido.";
    header('Location: ../../front/produccion/catalogo_recetas.php');
    exit();
}
?>
