<?php
include '../db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $imagen = $_FILES['imagen'];

    // Validar campos básicos
    if (empty($nombre)) {
        header('Location: ../../front/produccion/editar_materia_prima.php?id=' . $id . '&error=Por favor, ingrese el nombre de la materia prima.');
        exit();
    }

    // Manejo de la imagen (si se sube una nueva)
    $nuevaImagen = null;
    if ($imagen['error'] === UPLOAD_ERR_OK) {
        $directorioImagenes = '../../front/multimedia/materia_prima/';
        $nuevaImagen = uniqid() . '-' . basename($imagen['name']);
        $rutaCompleta = $directorioImagenes . $nuevaImagen;

        if (!move_uploaded_file($imagen['tmp_name'], $rutaCompleta)) {
            header('Location: ../../front/produccion/editar_materia_prima.php?id=' . $id . '&error=Error al subir la imagen.');
            exit();
        }
    }

    try {
        // Actualizar los datos sin modificar la imagen si no se subió una nueva
        if ($nuevaImagen) {
            $query = "UPDATE materia_prima SET nombre = :nombre, imagen = :imagen WHERE id_materia_prima = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':imagen', $nuevaImagen, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        } else {
            $query = "UPDATE materia_prima SET nombre = :nombre WHERE id_materia_prima = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        }

        if ($stmt->execute()) {
            header('Location: ../../front/produccion/catalogo.php?success=Materia prima actualizada correctamente.');
        } else {
            header('Location: ../../front/produccion/editar_materia_prima.php?id=' . $id . '&error=Error al actualizar la materia prima.');
        }
    } catch (PDOException $e) {
        header('Location: ../../front/produccion/editar_materia_prima.php?id=' . $id . '&error=Error: ' . $e->getMessage());
    }
} else {
    header('Location: ../../front/produccion/catalogo.php?error=Método no permitido.');
}
?>
