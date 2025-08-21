<?php
session_start();
include '../../back/db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_regalo = $_POST['id_regalo'];
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $costo_estimado = trim($_POST['costo_estimado']);
    $estatus = trim($_POST['estatus']);

    // Validación básica
    if (empty($id_regalo) || empty($nombre) || empty($costo_estimado) || empty($estatus)) {
        $_SESSION['error'] = "Todos los campos obligatorios deben completarse.";
        header("Location: ../../front/produccion/editar_regalo.php?id=$id_regalo");
        exit();
    }

    try {
        // Actualizar en la base de datos
        $query = "UPDATE regalos SET nombre = :nombre, descripcion = :descripcion, 
                  costo_estimado = :costo_estimado, estatus = :estatus WHERE id_regalo = :id_regalo";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':costo_estimado', $costo_estimado, PDO::PARAM_STR);
        $stmt->bindParam(':estatus', $estatus, PDO::PARAM_STR);
        $stmt->bindParam(':id_regalo', $id_regalo, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['success'] = "Regalo actualizado correctamente.";
        header("Location: ../../front/produccion/catalogo_regalos.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al actualizar el regalo: " . $e->getMessage();
        header("Location: ../../front/produccion/editar_regalo.php?id=$id_regalo");
        exit();
    }
} else {
    $_SESSION['error'] = "Método no permitido.";
    header("Location: ../../front/produccion/catalogo_regalos.php");
    exit();
}
?>
