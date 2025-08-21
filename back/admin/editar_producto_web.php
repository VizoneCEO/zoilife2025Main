<?php
session_start();
include '../../back/db/connection.php'; // Conexión a la BD con PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_producto_web'] ?? null;
    $categoria = $_POST['categoria'] ?? '';
    $precio = $_POST['precio'] ?? 0;
    $estrellas = $_POST['estrellas'] ?? 0;

    if ($id && $categoria && $precio > 0 && $estrellas >= 0 && $estrellas <= 5) {
        $stmt = $conn->prepare("UPDATE productos_web SET categoria = :categoria, precio = :precio, estrellas = :estrellas WHERE id_producto_web = :id");
        $stmt->bindParam(':categoria', $categoria);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':estrellas', $estrellas);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header("Location: ../../front/admin/productos_web.php");
        exit;
    } else {
        echo "Datos inválidos.";
    }
} else {
    echo "Acceso no permitido.";
}
