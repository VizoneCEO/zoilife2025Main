<?php
session_start();
include '../../back/db/connection.php'; // Conexión a la BD con PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_receta = $_POST['id_receta'] ?? null;
    $categoria = $_POST['categoria'] ?? '';
    $precio = $_POST['precio'] ?? 0;
    $estrellas = $_POST['estrellas'] ?? 0;

    if (!$id_receta || !$categoria || $precio <= 0 || $estrellas < 0 || $estrellas > 5) {
        echo "Error: Datos inválidos.";
        exit;
    }

    try {
        $query = "INSERT INTO productos_web (id_receta, categoria, precio, estrellas, estatus) 
                  VALUES (:id_receta, :categoria, :precio, :estrellas, 'activo')";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_receta', $id_receta);
        $stmt->bindParam(':categoria', $categoria);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':estrellas', $estrellas);
        $stmt->execute();

        header("Location: ../../front/admin/productos_web.php");
        exit;

    } catch (PDOException $e) {
        echo "Error al insertar: " . $e->getMessage();
    }
} else {
    echo "Método no permitido.";
}
