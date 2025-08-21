<?php
session_start();
include '../../back/db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cliente = intval($_POST['id_cliente']);
    $nombre = trim($_POST['nombre']);
    $apellido_paterno = trim($_POST['apellido_paterno']);
    $apellido_materno = trim($_POST['apellido_materno']);
    $telefono1 = trim($_POST['telefono1']);
    $telefono2 = trim($_POST['telefono2']);

    // Validación básica
    if (empty($nombre) || empty($apellido_paterno) || empty($telefono1)) {
        $_SESSION['error'] = "Los campos Nombre, Apellido Paterno y Teléfono Principal son obligatorios.";
        header("Location: ../../front/ventas/editar_cliente.php?id=$id_cliente");
        exit();
    }

    try {
        // Actualizar datos del cliente en la base de datos
        $query = "UPDATE clientes 
                  SET nombre = ?, apellido_paterno = ?, apellido_materno = ?, telefono1 = ?, telefono2 = ?, fecha_modificacion = NOW()
                  WHERE id_cliente = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$nombre, $apellido_paterno, $apellido_materno, $telefono1, $telefono2, $id_cliente]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = "Cliente actualizado correctamente.";
            header("Location: ../../front/ventas/clientes.php");
        } else {
            $_SESSION['error'] = "No se realizaron cambios en la información.";
            header("Location: ../../front/ventas/editar_cliente.php?id=$id_cliente");
        }
        exit();
    } catch (PDOException $e) {
        error_log("Error en la actualización del cliente: " . $e->getMessage()); // Log del error
        $_SESSION['error'] = "Error en la base de datos: " . $e->getMessage(); // Mensaje detallado
        header("Location: ../../front/ventas/editar_cliente.php?id=$id_cliente");
        exit();
    }
} else {
    $_SESSION['error'] = "Método no permitido.";
    header("Location: ../../front/ventas/clientes.php");
    exit();
}
?>
