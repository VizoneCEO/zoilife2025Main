<?php
session_start();
include '../db/connection.php'; // Conexión con PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_receta = intval($_POST['id_receta']);
    $id_materia_prima = intval($_POST['materia_prima']);
    $cantidad = floatval($_POST['cantidad']);
    $unidad_medida = trim($_POST['unidad_medida']);

    // Validar los datos recibidos
    if (empty($id_receta) || empty($id_materia_prima) || $cantidad <= 0 || empty($unidad_medida)) {
        $_SESSION['error'] = "Datos inválidos. Verifique la información.";
        header("Location: ../../front/produccion/ingredientes_receta.php?id=$id_receta");
        exit();
    }

    try {
        // Verificar si el ingrediente ya existe en la receta
        $query_check = "SELECT COUNT(*) AS total FROM ingredientes_receta WHERE id_receta = :id_receta AND id_materia_prima = :id_materia_prima AND estatus = 'activo'";
        $stmt_check = $conn->prepare($query_check);
        $stmt_check->bindParam(':id_receta', $id_receta, PDO::PARAM_INT);
        $stmt_check->bindParam(':id_materia_prima', $id_materia_prima, PDO::PARAM_INT);
        $stmt_check->execute();
        $row_check = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($row_check['total'] > 0) {
            $_SESSION['error'] = "Este ingrediente ya está agregado a la receta.";
            header("Location: ../../front/produccion/ingredientes_receta.php?id=$id_receta");
            exit();
        }

        // Insertar el nuevo ingrediente en la tabla
        $query_insert = "INSERT INTO ingredientes_receta (id_receta, id_materia_prima, cantidad, unidad_medida) VALUES (:id_receta, :id_materia_prima, :cantidad, :unidad_medida)";
        $stmt_insert = $conn->prepare($query_insert);
        $stmt_insert->bindParam(':id_receta', $id_receta, PDO::PARAM_INT);
        $stmt_insert->bindParam(':id_materia_prima', $id_materia_prima, PDO::PARAM_INT);
        $stmt_insert->bindParam(':cantidad', $cantidad, PDO::PARAM_STR);
        $stmt_insert->bindParam(':unidad_medida', $unidad_medida, PDO::PARAM_STR);

        if ($stmt_insert->execute()) {
            $_SESSION['success'] = "Ingrediente agregado correctamente.";
        } else {
            $_SESSION['error'] = "Error al agregar el ingrediente. Intente nuevamente.";
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error en la base de datos: " . $e->getMessage();
    }

    // Redirigir a la página de ingredientes
    header("Location: ../../front/produccion/ingredientes_receta.php?id=$id_receta");
    exit();

} else {
    $_SESSION['error'] = "Método no permitido.";
    header('Location: ../../front/produccion/catalogo_recetas.php');
    exit();
}
?>
