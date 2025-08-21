<?php
include '../../back/db/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(["success" => false, "error" => "ID inválido"]);
    exit();
}

$id_cotizacion = $_GET['id'];

// Obtener el nombre del cliente
$query = "SELECT CONCAT(cl.nombre, ' ', cl.apellido_paterno, ' ', cl.apellido_materno) AS nombre_cliente 
          FROM cotizaciones c
          JOIN clientes cl ON c.id_cliente = cl.id_cliente
          WHERE c.id_cotizacion = :id_cotizacion";

$stmt = $conn->prepare($query);
$stmt->bindParam(':id_cotizacion', $id_cotizacion, PDO::PARAM_INT);
$stmt->execute();
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if ($cliente) {
    echo json_encode(["success" => true, "cliente" => $cliente['nombre_cliente']]);
} else {
    echo json_encode(["success" => false, "error" => "Cliente no encontrado"]);
}
?>
