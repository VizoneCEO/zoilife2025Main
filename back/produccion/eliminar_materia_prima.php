<?php
include '../db/connection.php'; // Conexión a la base de datos

if (isset($_GET['id'])) {
    // Capturamos el ID del producto a eliminar
    $id = intval($_GET['id']);

    try {
        // Query para realizar la eliminación lógica
        $query = "UPDATE materia_prima SET estatus = 'inactivo' WHERE id_materia_prima = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecución y redirección
        if ($stmt->execute()) {
            header('Location: ../../front/produccion/catalogo.php?success=Materia prima eliminada correctamente.');
        } else {
            header('Location: ../../front/produccion/catalogo.php?error=No se pudo eliminar la materia prima.');
        }
    } catch (PDOException $e) {
        header('Location: ../../front/produccion/catalogo.php?error=Error en la base de datos: ' . $e->getMessage());
    }
} else {
    header('Location: ../../front/produccion/catalogo.php?error=ID no válido.');
}
?>
