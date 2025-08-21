<?php
include '../db/connection.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar datos del formulario
    $nombre = trim($_POST['nombre']);
    $imagen = $_FILES['imagen'];

    // Validar que el nombre no esté vacío
    if (empty($nombre)) {
        header('Location: ../../front/produccion/nueva_materia_prima.php?error=Por favor, ingrese el nombre de la materia prima.');
        exit();
    }

    // Manejo de la imagen subida
    $directorioImagenes = '../../front/multimedia/materia_prima/';
    if (!file_exists($directorioImagenes)) {
        mkdir($directorioImagenes, 0777, true); // Crear el directorio si no existe
    }

    $nombreImagen = uniqid() . '-' . basename($imagen['name']);
    $rutaImagen = $directorioImagenes . $nombreImagen;

    if (!move_uploaded_file($imagen['tmp_name'], $rutaImagen)) {
        header('Location: ../../front/produccion/nueva_materia_prima.php?error=Error al subir la imagen.');
        exit();
    }

    try {
        // Insertar los datos en la base de datos (sin cantidad ni unidad_medida)
        $query = "INSERT INTO materia_prima (nombre, imagen, estatus) VALUES (:nombre, :imagen, 'activo')";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':imagen', $nombreImagen, PDO::PARAM_STR);
        $stmt->execute();

        header('Location: ../../front/produccion/catalogo.php?success=Materia prima guardada correctamente.');
    } catch (PDOException $e) {
        header('Location: ../../front/produccion/nueva_materia_prima.php?error=Error al guardar la materia prima: ' . $e->getMessage());
    }
} else {
    header('Location: ../../front/produccion/nueva_materia_prima.php?error=Método no permitido.');
}
?>
