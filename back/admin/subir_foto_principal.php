<?php
session_start();
include '../../back/db/connection.php'; // Conexión a la BD con PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto'])) {
    $id_producto_web = $_POST['id_producto_web'] ?? null;
    $foto = $_FILES['foto'];

    if (!$id_producto_web || $foto['error'] !== 0) {
        die('Error al subir la imagen.');
    }

    $nombre_tmp = $foto['tmp_name'];
    $nombre_final = uniqid() . '_' . basename($foto['name']);
    $ruta_destino = '../../front/productosWeb/' . $nombre_final;

    if (!move_uploaded_file($nombre_tmp, $ruta_destino)) {
        die('Error al guardar la imagen.');
    }

    // Guardar en base de datos solo la ruta relativa
    $stmt = $conn->prepare("UPDATE productos_web SET foto_principal = :foto WHERE id_producto_web = :id");
    $stmt->bindParam(':foto', $nombre_final);
    $stmt->bindParam(':id', $id_producto_web);
    $stmt->execute();

    header("Location: ../../front/admin/productos_web.php");
    exit;
} else {
    echo "Acceso no permitido.";
}
