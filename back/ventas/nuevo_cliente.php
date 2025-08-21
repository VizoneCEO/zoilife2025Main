<?php
session_start();
include '../../back/db/connection.php'; // Conexión a la base de datos

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Debe iniciar sesión para registrar clientes.";
    header("Location: ../../front/auth/login.php");
    exit();
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido_paterno = trim($_POST['apellido_paterno']);
    $apellido_materno = trim($_POST['apellido_materno']);
    $telefono1 = trim($_POST['telefono1']);
    $telefono2 = trim($_POST['telefono2']);
    $usuario_creador = $_SESSION['user_id']; // ID del usuario autenticado

    // Generar un número de cliente automático basado en timestamp
    $numero_cliente = "CL-" . time();

    // Validación de datos básicos
    if (empty($nombre) || empty($apellido_paterno) || empty($telefono1)) {
        $_SESSION['error'] = "Nombre, apellido paterno y teléfono principal son obligatorios.";
        header("Location: ../../front/ventas/nuevo_cliente.php");
        exit();
    }

    try {
        // Insertar nuevo cliente
        $query = "INSERT INTO clientes (nombre, apellido_paterno, apellido_materno, numero_cliente, telefono1, telefono2, usuario_creador, estatus) 
                  VALUES (:nombre, :apellido_paterno, :apellido_materno, :numero_cliente, :telefono1, :telefono2, :usuario_creador, 'activo')";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':apellido_paterno', $apellido_paterno, PDO::PARAM_STR);
        $stmt->bindParam(':apellido_materno', $apellido_materno, PDO::PARAM_STR);
        $stmt->bindParam(':numero_cliente', $numero_cliente, PDO::PARAM_STR);
        $stmt->bindParam(':telefono1', $telefono1, PDO::PARAM_STR);
        $stmt->bindParam(':telefono2', $telefono2, PDO::PARAM_STR);
        $stmt->bindParam(':usuario_creador', $usuario_creador, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['success'] = "Cliente registrado correctamente.";
        header("Location: ../../front/ventas/clientes.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al registrar cliente: " . $e->getMessage();
        header("Location: ../../front/ventas/nuevo_cliente.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Método no permitido.";
    header("Location: ../../front/ventas/nuevo_cliente.php");
    exit();
}
?>
