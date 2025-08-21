<?php
header('Content-Type: application/json');
include '../../back/db/connection.php';

if (!isset($_POST['id_pedido_web']) || !isset($_FILES['foto'])) {
    echo json_encode(["success" => false, "error" => "Faltan datos."]);
    exit();
}

$id_pedido_web = intval($_POST['id_pedido_web']);
$archivo = $_FILES['foto'];

// Validar tipo de archivo
$permitidos = ['image/jpeg', 'image/png', 'image/jpg'];
if (!in_array($archivo['type'], $permitidos)) {
    echo json_encode(["success" => false, "error" => "Archivo no permitido."]);
    exit();
}

// Crear carpeta si no existe
$carpetaDestino = "../../front/evidencias_web/" . $id_pedido_web;
if (!is_dir($carpetaDestino)) {
    mkdir($carpetaDestino, 0777, true);
}

// Nombre único
$extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
$nombreArchivo = "evidencia_" . date("Ymd_His") . "." . $extension;
$rutaFinal = $carpetaDestino . "/" . $nombreArchivo;

// Mover archivo
if (move_uploaded_file($archivo['tmp_name'], $rutaFinal)) {
    // Ruta para guardar en BD (relativa a front/)
    $rutaParaBD = "evidencias_web/" . $id_pedido_web . "/" . $nombreArchivo;

    // Actualizar campo evidencia_entrega en pedidos_web
    $stmt = $conn->prepare("UPDATE pedidos_web SET evidencia_entrega = :ruta WHERE id_pedido_web = :id");
    $stmt->bindParam(':ruta', $rutaParaBD);
    $stmt->bindParam(':id', $id_pedido_web, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(["success" => true, "archivo" => $nombreArchivo]);
} else {
    echo json_encode(["success" => false, "error" => "No se pudo guardar la imagen."]);
}
?>
