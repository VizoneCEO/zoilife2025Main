<?php
session_start();
include '../../back/db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $imagen_nombre = '';

    // Validación básica
    if (empty($titulo) || empty($descripcion)) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header("Location: ../../front/admin/blog_web.php");
        exit();
    }

    // Subir imagen si viene
    if (!empty($_FILES['imagen']['name'])) {
        $imagen_tmp = $_FILES['imagen']['tmp_name'];
        $imagen_nombre = time() . '_' . basename($_FILES['imagen']['name']);
        $ruta_destino = '../../front/blogs/img/' . $imagen_nombre;

        if (!move_uploaded_file($imagen_tmp, $ruta_destino)) {
            $_SESSION['error'] = "Error al subir la imagen.";
            header("Location: ../../front/admin/blog_web.php");
            exit();
        }
    }

    try {
        $stmt = $conn->prepare("INSERT INTO blogs (titulo, descripcion, imagen) VALUES (:titulo, :descripcion, :imagen)");
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':imagen', $imagen_nombre);
        $stmt->execute();

        $_SESSION['success'] = "Blog registrado correctamente.";
        header("Location: ../../front/admin/blog_web.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al guardar el blog: " . $e->getMessage();
        header("Location: ../../front/admin/blog_web.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Método no permitido.";
    header("Location: ../../front/admin/blog_web.php");
    exit();
}
