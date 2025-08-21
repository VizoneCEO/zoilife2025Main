<?php
session_start();
include '../db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Validar que el ID del proveedor esté presente
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id_proveedor = intval($_GET['id']);

        try {
            // Actualizar el estatus del proveedor a "inactivo"
            $query = "UPDATE proveedores SET estatus = 'inactivo' WHERE id_proveedor = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id_proveedor, PDO::PARAM_INT);

            if ($stmt->execute()) {
                // Redirigir con mensaje de éxito
                header('Location: ../../front/produccion/catalogoProveedores.php?success=Proveedor eliminado correctamente.');
                exit();
            } else {
                // Error en la ejecución de la consulta
                header('Location: ../../front/produccion/catalogoProveedores.php?error=Error al eliminar el proveedor. Intenta nuevamente.');
                exit();
            }
        } catch (PDOException $e) {
            header('Location: ../../front/produccion/catalogoProveedores.php?error=Error en la base de datos: ' . $e->getMessage());
            exit();
        }
    } else {
        // ID inválido o no proporcionado
        header('Location: ../../front/produccion/catalogoProveedores.php?error=ID de proveedor inválido.');
        exit();
    }
} else {
    // Método HTTP no permitido
    header('Location: ../../front/produccion/catalogoProveedores.php?error=Método no permitido.');
    exit();
}
?>
