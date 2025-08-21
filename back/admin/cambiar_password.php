<?php
session_start();
include '../../back/db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = intval($_POST['id_usuario']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validar que los campos no estén vacíos
    if (empty($id_usuario) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header("Location: ../../front/admin/cambiar_password.php?id=$id_usuario");
        exit();
    }

    // Verificar que las contraseñas coincidan
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Las contraseñas no coinciden.";
        header("Location: ../../front/admin/cambiar_password.php?id=$id_usuario");
        exit();
    }

    // Encriptar la nueva contraseña
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    try {
        // Actualizar la contraseña del usuario
        $query = "UPDATE usuarios SET password = ? WHERE id_usuario = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$hashed_password, $id_usuario]);

        $_SESSION['success'] = "Contraseña actualizada correctamente.";
        header("Location: ../../front/admin/gestion_usuarios.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al actualizar la contraseña.";
        header("Location: ../../front/admin/cambiar_password.php?id=$id_usuario");
        exit();
    }
} else {
    $_SESSION['error'] = "Método no permitido.";
    header("Location: ../../front/admin/gestion_usuarios.php");
    exit();
}
?>
