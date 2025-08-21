<?php
session_start();
include '../../back/db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_producto = intval($_POST['producto']);
    $cantidad_producto = intval($_POST['cantidad']);
    $usuario_responsable = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Desconocido';

    if ($id_producto <= 0 || $cantidad_producto <= 0) {
        header("Location: ../../front/produccion/nueva_requisicion.php?error=Datos inválidos. Verifique e intente nuevamente.");
        exit();
    }

    try {
        $conn->beginTransaction();

        // Insertar la requisición
        $query = "INSERT INTO requisiciones (id_producto, cantidad, usuario_responsable, estatus) 
                  VALUES (:id_producto, :cantidad, :usuario_responsable, 'pendiente')";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
        $stmt->bindParam(':cantidad', $cantidad_producto, PDO::PARAM_INT);
        $stmt->bindParam(':usuario_responsable', $usuario_responsable, PDO::PARAM_STR);
        $stmt->execute();

        $id_requisicion = $conn->lastInsertId();

        // Obtener ingredientes de la receta
        $query = "SELECT i.id_ingrediente, i.id_materia_prima, i.cantidad, i.unidad_medida, m.nombre 
                  FROM ingredientes_receta i
                  JOIN materia_prima m ON i.id_materia_prima = m.id_materia_prima
                  WHERE i.id_receta = :id_producto AND i.estatus = 'activo'";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
        $stmt->execute();
        $ingredientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$ingredientes) {
            $conn->query("DELETE FROM requisiciones WHERE id_requisicion = $id_requisicion");
            $conn->commit();
            header("Location: ../../front/produccion/nueva_requisicion.php?error=No hay ingredientes registrados para esta receta.");
            exit();
        }

        // Insertar ingredientes en salidas_producto
        foreach ($ingredientes as $row) {
            $id_ingrediente = $row['id_ingrediente'];
            $cantidad_total_necesaria = $row['cantidad'] * $cantidad_producto;
            $unidad_medida = $row['unidad_medida'];

            $query_salida = "INSERT INTO salidas_producto (id_requisicion, id_ingrediente, cantidad_salida, unidad_medida) 
                             VALUES (:id_requisicion, :id_ingrediente, :cantidad_salida, :unidad_medida)";
            $stmt_salida = $conn->prepare($query_salida);
            $stmt_salida->bindParam(':id_requisicion', $id_requisicion, PDO::PARAM_INT);
            $stmt_salida->bindParam(':id_ingrediente', $id_ingrediente, PDO::PARAM_INT);
            $stmt_salida->bindParam(':cantidad_salida', $cantidad_total_necesaria, PDO::PARAM_STR);
            $stmt_salida->bindParam(':unidad_medida', $unidad_medida, PDO::PARAM_STR);
            $stmt_salida->execute();
        }

        $conn->commit();
        header("Location: ../../front/produccion/requiciones_realizadas.php?success=Requisición procesada correctamente.");
        exit();
    } catch (PDOException $e) {
        $conn->rollBack();
        header("Location: ../../front/produccion/nueva_requisicion.php?error=Error en la base de datos: " . $e->getMessage());
        exit();
    }
}
?>
