<?php
session_start();
include '../../back/db/connection.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $rol = trim($_POST['rol']);
    $estado = 'activo';

    // Validar que los campos no estén vacíos
    if (empty($nombre) || empty($apellido) || empty($email) || empty($password) || empty($rol)) {
        header('Location: ../../front/admin/nuevo_usuario.php?error=Todos los campos son obligatorios.');
        exit();
    }

    try {
        // Verificar si el correo ya está registrado
        $query_check = "SELECT COUNT(*) FROM usuarios WHERE email = :email";
        $stmt_check = $conn->prepare($query_check);
        $stmt_check->bindParam(':email', $email);
        $stmt_check->execute();
        $existe = $stmt_check->fetchColumn();

        if ($existe > 0) {
            header('Location: ../../front/admin/nuevo_usuario.php?error=El correo ya está registrado.');
            exit();
        }

        // Hashear la contraseña antes de guardarla
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insertar el nuevo usuario
        $query = "INSERT INTO usuarios (nombre, apellido, email, password, rol, estado, fecha_creacion, fecha_modificacion) 
                  VALUES (:nombre, :apellido, :email, :password, :rol, :estado, NOW(), NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':rol', $rol);
        $stmt->bindParam(':estado', $estado);

        if ($stmt->execute()) {
            header('Location: ../../front/admin/gestion_usuarios.php?success=Usuario registrado correctamente.');
            exit();
        } else {
            header('Location: ../../front/admin/nuevo_usuario.php?error=Error al registrar el usuario.');
            exit();
        }
    } catch (PDOException $e) {
        header('Location: ../../front/admin/nuevo_usuario.php?error=Error en la base de datos.');
        exit();
    }
} else {
    header('Location: ../../front/admin/gestion_usuarios.php?error=Método no permitido.');
    exit();
}
?>
