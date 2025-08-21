<?php

include '../db/connection.php'; // Conexión con PDO

$query = $_GET['query'] ?? '';
$resultados = [];

if (!empty($query)) {
    $stmt = $conn->prepare("SELECT pw.id_producto_web, pw.foto_principal, r.nombre_producto, pw.categoria 
                            FROM productos_web pw 
                            JOIN recetas r ON pw.id_receta = r.id_receta 
                            WHERE pw.estatus = 'activo' AND r.nombre_producto LIKE :busqueda 
                            ORDER BY r.nombre_producto ASC 
                            LIMIT 10");
    $busqueda = '%' . $query . '%';
    $stmt->bindParam(':busqueda', $busqueda);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

header('Content-Type: application/json');
echo json_encode($resultados);

