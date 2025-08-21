<?php
session_start();
include '../db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $materia_prima_id = intval($_POST['materia_prima']);
    $cantidad = floatval($_POST['cantidad']);
    $unidad_medida = trim($_POST['unidad_medida']);
    $costo_total = floatval($_POST['costo_total']);
    $ticket_factura = trim($_POST['ticket_factura']);
    $proveedor_id = intval($_POST['proveedor']);
    $usuario_responsable = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Desconocido';

    // Validar que todos los campos sean correctos
    if (empty($materia_prima_id) || $cantidad <= 0 || empty($unidad_medida) || $costo_total <= 0 || empty($proveedor_id) || empty($ticket_factura)) {
        header('Location: ../../front/produccion/ingreso_materia_prima.php?error=Datos inválidos. Verifique la información e intente nuevamente.');
        exit();
    }

    try {
        // Registrar el ingreso en la tabla de control
        $insertQuery = "INSERT INTO control_ingresos 
                        (id_materia_prima, cantidad_ingresada, costo_total, unidad_medida, usuario_responsable, ticket_factura, id_proveedor)
                        VALUES (:materia_prima_id, :cantidad, :costo_total, :unidad_medida, :usuario_responsable, :ticket_factura, :proveedor_id)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bindParam(':materia_prima_id', $materia_prima_id, PDO::PARAM_INT);
        $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_STR);
        $stmt->bindParam(':costo_total', $costo_total, PDO::PARAM_STR);
        $stmt->bindParam(':unidad_medida', $unidad_medida, PDO::PARAM_STR);
        $stmt->bindParam(':usuario_responsable', $usuario_responsable, PDO::PARAM_STR);
        $stmt->bindParam(':ticket_factura', $ticket_factura, PDO::PARAM_STR);
        $stmt->bindParam(':proveedor_id', $proveedor_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header('Location: ../../front/produccion/stock_materia_prima.php?success=Ingreso registrado correctamente.');
            exit();
        } else {
            header('Location: ../../front/produccion/ingreso_materia_prima.php?error=Error al registrar el ingreso.');
            exit();
        }
    } catch (PDOException $e) {
        header('Location: ../../front/produccion/ingreso_materia_prima.php?error=Error en la base de datos: ' . $e->getMessage());
        exit();
    }
} else {
    header('Location: ../../front/produccion/catalogo.php?error=Método no permitido.');
    exit();
}
?>
