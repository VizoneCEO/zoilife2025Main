<?php
session_start();
include '../../back/db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_blog = $_POST['id_blog'] ?? null;

    if (!$id_blog || !is_numeric($id_blog)) {
        $_SESSION['error'] = "ID inválido.";
        header("Location: ../../front/admin/blog_web.php");
        exit();
    }

    try {
        $stmt = $conn->prepare("UPDATE blogs SET estatus = 'inactivo' WHERE id_blog = :id");
        $stmt->bindParam(':id', $id_blog);
        $stmt->execute();

        $_SESSION['success'] = "Blog eliminado correctamente.";
        header("Location: ../../front/admin/blog_web.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al eliminar: " . $e->getMessage();
        header("Location: ../../front/admin/blog_web.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Método no permitido.";
    header("Location: ../../front/admin/blog_web.php");
    exit();
}
