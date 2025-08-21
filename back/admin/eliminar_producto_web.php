<?php
session_start();
include '../../back/db/connection.php'; // Conexión a la BD con PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_producto_web'] ?? null;

    if ($id) {
        $stmt = $conn->prepare("UPDATE productos_web SET estatus = 'inactivo' WHERE id_producto_web = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header("Location: ../../front/admin/productos_web.php");
        exit;
    } else {
        echo "ID inválido.";
    }
} else {
    echo "Método no permitido.";
}
