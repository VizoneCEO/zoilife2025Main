<?php
session_start();
include '../db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_producto = trim($_POST['nombre_producto']);
    $cantidad = floatval($_POST['cantidad']);
    $unidad_medida = trim($_POST['unidad_medida']);

    // Validar los campos
    if (empty($nombre_producto) || $cantidad <= 0 || empty($unidad_medida)) {
        header('Location: ../../front/produccion/nueva_receta.php?error=Datos inválidos. Verifique la información e intente nuevamente.');
        exit();
    }

    try {
        // Insertar la receta en la base de datos
        $query = "INSERT INTO recetas (nombre_producto, cantidad, unidad_medida) VALUES (:nombre_producto, :cantidad, :unidad_medida)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':nombre_producto', $nombre_producto, PDO::PARAM_STR);
        $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_STR);
        $stmt->bindParam(':unidad_medida', $unidad_medida, PDO::PARAM_STR);

        if ($stmt->execute()) {
            header('Location: ../../front/produccion/catalogo_recetas.php?success=Receta registrada correctamente.');
            exit();
        } else {
            header('Location: ../../front/produccion/nueva_receta.php?error=Error al registrar la receta.');
            exit();
        }
    } catch (PDOException $e) {
        header('Location: ../../front/produccion/nueva_receta.php?error=Error en la base de datos: ' . $e->getMessage());
        exit();
    }
} else {
    header('Location: ../../front/produccion/catalogo_recetas.php?error=Método no permitido.');
    exit();
}
?>
