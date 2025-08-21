<?php
session_start();
include '../../back/db/connection.php'; // Conexión a la BD con PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = intval($_POST['id_usuario']);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $rol = $_POST['rol'];
    $estado = $_POST['estado'];

    // Validación de datos
    if (empty($id_usuario) || empty($nombre) || empty($apellido) || empty($email) || empty($rol) || empty($estado)) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header("Location: ../../front/admin/editar_usuario.php?id=$id_usuario");
        exit();
    }

    try {
        // Verificar si el correo ya está registrado en otro usuario
        $queryCheck = "SELECT COUNT(*) FROM usuarios WHERE email = ? AND id_usuario != ?";
        $stmtCheck = $conn->prepare($queryCheck);
        $stmtCheck->execute([$email, $id_usuario]);

        if ($stmtCheck->fetchColumn() > 0) {
            $_SESSION['error'] = "El correo electrónico ya está registrado en otro usuario.";
            header("Location: ../../front/admin/editar_usuario.php?id=$id_usuario");
            exit();
        }

        // Actualizar usuario
        $query = "UPDATE usuarios SET nombre = ?, apellido = ?, email = ?, rol = ?, estado = ?, fecha_modificacion = NOW() WHERE id_usuario = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$nombre, $apellido, $email, $rol, $estado, $id_usuario]);

        $_SESSION['success'] = "Usuario actualizado correctamente.";
        header("Location: ../../front/admin/gestion_usuarios.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al actualizar el usuario.";
        header("Location: ../../front/admin/editar_usuario.php?id=$id_usuario");
        exit();
    }
}
?>
