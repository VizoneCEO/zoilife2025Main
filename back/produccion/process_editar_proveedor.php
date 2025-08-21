<?php
session_start();
include '../db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar que se reciba el ID del proveedor
    if (!isset($_POST['id_proveedor']) || empty($_POST['id_proveedor'])) {
        header('Location: ../../front/produccion/proveedores.php?error=ID del proveedor no válido.');
        exit();
    }

    $id_proveedor = intval($_POST['id_proveedor']);
    $nombre = trim($_POST['nombre']);
    $contacto = trim($_POST['contacto']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    $direccion = trim($_POST['direccion']);

    // Validar campos básicos
    if (empty($nombre) || empty($contacto) || empty($telefono) || empty($email) || empty($direccion)) {
        header('Location: ../../front/produccion/editar_proveedor.php?id=' . $id_proveedor . '&error=Todos los campos son obligatorios.');
        exit();
    }

    try {
        // Actualizar los datos del proveedor
        $query = "UPDATE proveedores SET nombre = :nombre, contacto = :contacto, telefono = :telefono, email = :email, direccion = :direccion WHERE id_proveedor = :id_proveedor";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':contacto', $contacto, PDO::PARAM_STR);
        $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':direccion', $direccion, PDO::PARAM_STR);
        $stmt->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header('Location: ../../front/produccion/proveedores.php?success=Proveedor actualizado correctamente.');
            exit();
        } else {
            header('Location: ../../front/produccion/editar_proveedor.php?id=' . $id_proveedor . '&error=Error al actualizar el proveedor.');
            exit();
        }
    } catch (PDOException $e) {
        header('Location: ../../front/produccion/editar_proveedor.php?id=' . $id_proveedor . '&error=Error en la base de datos: ' . $e->getMessage());
        exit();
    }
} else {
    header('Location: ../../front/produccion/proveedores.php?error=Método no permitido.');
    exit();
}
?>
