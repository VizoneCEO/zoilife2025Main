<?php
header('Content-Type: application/json');
include '../../back/db/connection.php';

if (!isset($_POST['id_cotizacion']) || !isset($_FILES['foto'])) {
    echo json_encode(["success" => false, "error" => "Faltan datos."]);
    exit();
}

$id_cotizacion = intval($_POST['id_cotizacion']);
$archivo = $_FILES['foto'];

// Validar tipo de archivo
$permitidos = ['image/jpeg', 'image/png', 'image/jpg'];
if (!in_array($archivo['type'], $permitidos)) {
    echo json_encode(["success" => false, "error" => "Archivo no permitido."]);
    exit();
}

// Crear carpeta si no existe
$carpetaDestino = "../../front/evidencias/" . $id_cotizacion;
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
    $rutaParaBD = "evidencias/" . $id_cotizacion . "/" . $nombreArchivo;

    // Actualizar campo evidencia en cotizaciones
    $stmt = $conn->prepare("UPDATE cotizaciones SET evidencia = :ruta WHERE id_cotizacion = :id");
    $stmt->bindParam(':ruta', $rutaParaBD);
    $stmt->bindParam(':id', $id_cotizacion, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(["success" => true, "archivo" => $nombreArchivo]);
} else {
    echo json_encode(["success" => false, "error" => "No se pudo guardar la imagen."]);
}
?>
