<?php
session_start();
include '../db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $contacto = trim($_POST['contacto']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['correo']);
    $direccion = trim($_POST['direccion']);

    // Validar campos obligatorios
    if (empty($nombre)) {
        header('Location: ../../front/produccion/nuevo_proveedor.php?error=El nombre del proveedor es obligatorio.');
        exit();
    }

    try {
        // Insertar proveedor en la base de datos
        $query = "INSERT INTO proveedores (nombre, contacto, telefono, email, direccion, estatus) 
                  VALUES (:nombre, :contacto, :telefono, :email, :direccion, 'activo')";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':contacto', $contacto, PDO::PARAM_STR);
        $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':direccion', $direccion, PDO::PARAM_STR);
        $stmt->execute();

        header('Location: ../../front/produccion/catalogoProveedores.php?success=Proveedor registrado correctamente.');
        exit();
    } catch (PDOException $e) {
        header('Location: ../../front/produccion/nuevo_proveedor.php?error=Error al registrar el proveedor: ' . $e->getMessage());
        exit();
    }
} else {
    header('Location: ../../front/produccion/dashboard.php?error=Método no permitido.');
    exit();
}
?>
