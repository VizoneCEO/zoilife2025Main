<?php
session_start();
include '../../back/db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_blog = $_POST['id_blog'];
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $ruta_final = '';

    // Validación básica
    if (empty($id_blog) || empty($titulo) || empty($descripcion)) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header("Location: ../../front/admin/blog_web.php");
        exit();
    }

    // Obtener la imagen actual
    $stmt = $conn->prepare("SELECT imagen FROM blogs WHERE id_blog = :id");
    $stmt->bindParam(':id', $id_blog);
    $stmt->execute();
    $blog = $stmt->fetch(PDO::FETCH_ASSOC);
    $imagen_actual = $blog ? $blog['imagen'] : '';
    $ruta_final = $imagen_actual;

    // Si se sube nueva imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $nombre = $_FILES['imagen']['name'];
        $tmp = $_FILES['imagen']['tmp_name'];
        $ext = pathinfo($nombre, PATHINFO_EXTENSION);
        $nuevoNombre = uniqid() . '.' . $ext;

        $directorio = '../../front/blogs/img/';
        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $ruta = $directorio . $nuevoNombre;

        if (move_uploaded_file($tmp, $ruta)) {
            $ruta_final = $nuevoNombre;
        } else {
            $_SESSION['error'] = "No se pudo guardar la nueva imagen.";
            header("Location: ../../front/admin/blog_web.php");
            exit();
        }
    }

    try {
        $stmt = $conn->prepare("UPDATE blogs SET titulo = :titulo, descripcion = :descripcion, imagen = :imagen WHERE id_blog = :id");
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':imagen', $ruta_final);
        $stmt->bindParam(':id', $id_blog);
        $stmt->execute();

        $_SESSION['success'] = "Blog actualizado correctamente.";
        header("Location: ../../front/admin/blog_web.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al actualizar el blog: " . $e->getMessage();
        header("Location: ../../front/admin/blog_web.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Método no permitido.";
    header("Location: ../../front/admin/blog_web.php");
    exit();
}
