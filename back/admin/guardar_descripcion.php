<?php

session_start();
include '../../back/db/connection.php'; // Conexión a la BD con PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_producto_web'];
    $key = "descripcion_$id";

    if (isset($_POST[$key])) {
        $descripcion = $_POST[$key];

        $stmt = $conn->prepare("UPDATE productos_web SET descripcion = :descripcion WHERE id_producto_web = :id");
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header("Location: ../../front/admin/productos_web.php");
        exit;
    }
}
echo "Error al guardar la descripción.";
