<?php
session_start();
include '../../back/db/connection.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $costo_estimado = trim($_POST['costo_estimado']);
    $usuario_creador = $_SESSION['user_id']; // ID del usuario que está registrando el regalo

    // Validación de campos obligatorios
    if (empty($nombre) || empty($costo_estimado)) {
        $_SESSION['error'] = "El nombre y el costo estimado son obligatorios.";
        header("Location: ../../front/produccion/nuevo_regalo.php");
        exit();
    }

    // Asignar estado activo por defecto
    $estatus = 'activo';

    try {
        // Insertar en la base de datos
        $query = "INSERT INTO regalos (nombre, descripcion, costo_estimado, estatus, usuario_creador) 
                  VALUES (:nombre, :descripcion, :costo_estimado, :estatus, :usuario_creador)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':costo_estimado', $costo_estimado, PDO::PARAM_STR);
        $stmt->bindParam(':estatus', $estatus, PDO::PARAM_STR);
        $stmt->bindParam(':usuario_creador', $usuario_creador, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['success'] = "Regalo registrado correctamente.";
        header("Location: ../../front/produccion/catalogo_regalos.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al registrar el regalo: " . $e->getMessage();
        header("Location: ../../front/produccion/nuevo_regalo.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Método no permitido.";
    header("Location: ../../front/produccion/catalogo_regalos.php");
    exit();
}
?>
