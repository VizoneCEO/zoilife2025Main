<?php
include '../db/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id_producto_web'];
    $estado = $_POST['estado'];

    $query = "UPDATE productos_web SET productos_r = :estado WHERE id_producto_web = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: {$_SERVER['HTTP_REFERER']}");
    exit();
}
?>
