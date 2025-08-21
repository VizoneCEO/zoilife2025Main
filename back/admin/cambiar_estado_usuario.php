<?php
session_start();
include '../../back/db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id_usuario = intval($_GET['id']);

    // Validación básica
    if ($id_usuario <= 0) {
        $_SESSION['error'] = "ID de usuario no válido.";
        header("Location: ../../front/admin/gestion_usuarios.php");
        exit();
    }

    try {
        // Obtener el estado actual
        $query = "SELECT estado FROM usuarios WHERE id_usuario = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id_usuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            $_SESSION['error'] = "El usuario no existe.";
            header("Location: ../../front/admin/gestion_usuarios.php");
            exit();
        }

        // Cambiar el estado del usuario
        $nuevo_estado = ($usuario['estado'] === 'activo') ? 'inactivo' : 'activo';
        $queryUpdate = "UPDATE usuarios SET estado = ? WHERE id_usuario = ?";
        $stmtUpdate = $conn->prepare($queryUpdate);
        $stmtUpdate->execute([$nuevo_estado, $id_usuario]);

        $_SESSION['success'] = "El usuario ha sido " . ($nuevo_estado === 'activo' ? 'activado' : 'desactivado') . " correctamente.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al cambiar el estado del usuario.";
    }

    header("Location: ../../front/admin/gestion_usuarios.php");
    exit();
}
?>
