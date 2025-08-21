<?php

session_start();
include '../../back/db/connection.php'; // Conexión a la BD con PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_producto_web'] ?? null;
    $estado = $_POST['estado'] ?? 0;

    if ($id !== null) {
        $stmt = $conn->prepare("UPDATE productos_web SET en_nuevos_productos = :estado WHERE id_producto_web = :id");
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
}

header("Location: ../../front/admin/productos_web.php");
exit;
