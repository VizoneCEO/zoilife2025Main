<?php
session_start();
include '../../back/db/connection.php'; // Conexión a la BD con PDO




if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_foto = $_POST['id_foto'] ?? null;

    if (!$id_foto) {
        die("ID inválido.");
    }

    $stmt = $conn->prepare("UPDATE productos_web_fotos SET estatus = 'inactivo' WHERE id_foto = :id");
    $stmt->bindParam(':id', $id_foto);
    $stmt->execute();

    header("Location: ../../front/admin/productos_web.php");
    exit;
} else {
    echo "Método no permitido.";
}
