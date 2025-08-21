<?php
session_start();
include '../../back/db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_regalo = $_POST['id_regalo'];
    $cantidad = $_POST['cantidad'];
    $usuario_creador = $_SESSION['user_id']; // Usuario que realiza el ingreso

    // Validación básica
    if (empty($id_regalo) || empty($cantidad) || !is_numeric($cantidad) || $cantidad <= 0) {
        $_SESSION['error'] = "Debe seleccionar un regalo y una cantidad válida.";
        header("Location: ../../front/produccion/ingreso_regalos.php");
        exit();
    }

    try {
        // Registrar ingreso en la tabla 'ingreso_regalos'
        $query = "INSERT INTO ingreso_regalos (id_regalo, cantidad, usuario_creador) 
                  VALUES (:id_regalo, :cantidad, :usuario_creador)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_regalo', $id_regalo, PDO::PARAM_INT);
        $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
        $stmt->bindParam(':usuario_creador', $usuario_creador, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['success'] = "Ingreso de regalo registrado correctamente.";
        header("Location: ../../front/produccion/stock_regalos.php"); // Redirige al FRONT correctamente
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al registrar el ingreso: " . $e->getMessage();
        header("Location: ../../front/produccion/ingreso_regalos.php"); // Redirige a la pantalla correcta
        exit();
    }
} else {
    $_SESSION['error'] = "Método no permitido.";
    header("Location: ../../front/produccion/stock_regalos.php"); // Redirige a la pantalla correcta
    exit();
}
