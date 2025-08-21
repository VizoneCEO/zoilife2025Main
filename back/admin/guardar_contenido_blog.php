<?php
session_start();
include '../../back/db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id_blog'])) {
        $_SESSION['error'] = "ID de blog no recibido.";
        header("Location: ../../front/admin/blog_web.php");
        exit();
    }

    $id_blog = $_POST['id_blog'];
    $campo = 'contenido_' . $id_blog;

    if (!isset($_POST[$campo])) {
        $_SESSION['error'] = "Contenido no recibido.";
        header("Location: ../../front/admin/blog_web.php");
        exit();
    }

    $contenido = trim($_POST[$campo]);

    try {
        $stmt = $conn->prepare("UPDATE blogs SET contenido = :contenido WHERE id_blog = :id");
        $stmt->bindParam(':contenido', $contenido);
        $stmt->bindParam(':id', $id_blog, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['success'] = "Contenido actualizado correctamente.";
        header("Location: ../../front/admin/blog_web.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al guardar el contenido: " . $e->getMessage();
        header("Location: ../../front/admin/blog_web.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Método no permitido.";
    header("Location: ../../front/admin/blog_web.php");
    exit();
}
