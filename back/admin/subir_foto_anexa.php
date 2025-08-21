<?php
session_start();
include '../../back/db/connection.php'; // Conexión a la BD con PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto_anexa'])) {
    $id_producto_web = $_POST['id_producto_web'] ?? null;
    $foto = $_FILES['foto_anexa'];

    if (!$id_producto_web || $foto['error'] !== 0) {
        die('Error al subir imagen anexa.');
    }

    $tmp = $foto['tmp_name'];
    $nombre_final = 'anexa_' . uniqid() . '_' . basename($foto['name']);
    $ruta = '../../front/productosWeb/' . $nombre_final;

    if (!move_uploaded_file($tmp, $ruta)) {
        die('Error al guardar imagen.');
    }

    // Insertar en la tabla
    $stmt = $conn->prepare("INSERT INTO productos_web_fotos (id_producto_web, url_foto, orden, estatus) VALUES (:id, :url, 0, 'activo')");
    $stmt->bindParam(':id', $id_producto_web);
    $stmt->bindParam(':url', $nombre_final);
    $stmt->execute();

    header("Location: ../../front/admin/productos_web.php");
    exit;
} else {
    echo "Acceso no permitido.";
}
