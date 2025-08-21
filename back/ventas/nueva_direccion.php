<?php
session_start();
include '../../back/db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cliente = $_POST['id_cliente'];
    $calle = trim($_POST['calle']);
    $numero_casa = trim($_POST['numero_casa']);
    $numero_interior = trim($_POST['numero_interior']);
    $colonia = trim($_POST['colonia']); // ✅ nuevo campo
    $alcaldia_municipio = trim($_POST['alcaldia_municipio']);
    $entre_calles = trim($_POST['entre_calles']);
    $ciudad = trim($_POST['ciudad']);
    $estado = trim($_POST['estado']);
    $codigo_postal = trim($_POST['codigo_postal']);
    $pais = trim($_POST['pais']);
    $observaciones = trim($_POST['observaciones']);
    $tipo_vivienda = $_POST['tipo_vivienda'];
    $latitud = trim($_POST['latitud']);
    $longitud = trim($_POST['longitud']);

    // Validación básica
    if (
        empty($id_cliente) || empty($calle) || empty($numero_casa) || empty($colonia) ||
        empty($ciudad) || empty($estado) || empty($codigo_postal) || empty($pais) ||
        empty($latitud) || empty($longitud) || empty($tipo_vivienda)
    ) {
        $_SESSION['error'] = "Todos los campos obligatorios deben estar llenos.";
        header("Location: ../../front/ventas/nueva_direccion.php?id=$id_cliente");
        exit();
    }

    // Generar link de Google Maps
    $google_maps_link = "https://www.google.com/maps?q=" . urlencode($latitud) . "," . urlencode($longitud);
    $estatus = 'activo';

    try {
        $query = "INSERT INTO direcciones 
        (id_cliente, calle, numero_casa, numero_interior, colonia, alcaldia_municipio, entre_calles, ciudad, estado, codigo_postal, pais, observaciones, tipo_vivienda, latitud, longitud, google_maps_link, estatus) 
        VALUES 
        (:id_cliente, :calle, :numero_casa, :numero_interior, :colonia, :alcaldia_municipio, :entre_calles, :ciudad, :estado, :codigo_postal, :pais, :observaciones, :tipo_vivienda, :latitud, :longitud, :google_maps_link, :estatus)";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);
        $stmt->bindParam(':calle', $calle);
        $stmt->bindParam(':numero_casa', $numero_casa);
        $stmt->bindParam(':numero_interior', $numero_interior);
        $stmt->bindParam(':colonia', $colonia); // ✅ nuevo campo
        $stmt->bindParam(':alcaldia_municipio', $alcaldia_municipio);
        $stmt->bindParam(':entre_calles', $entre_calles);
        $stmt->bindParam(':ciudad', $ciudad);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':codigo_postal', $codigo_postal);
        $stmt->bindParam(':pais', $pais);
        $stmt->bindParam(':observaciones', $observaciones);
        $stmt->bindParam(':tipo_vivienda', $tipo_vivienda);
        $stmt->bindParam(':latitud', $latitud);
        $stmt->bindParam(':longitud', $longitud);
        $stmt->bindParam(':google_maps_link', $google_maps_link);
        $stmt->bindParam(':estatus', $estatus);
        $stmt->execute();

        $_SESSION['success'] = "Dirección registrada correctamente.";
        header("Location: ../../front/ventas/direcciones.php?id=$id_cliente");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al registrar la dirección: " . $e->getMessage();
        header("Location: ../../front/ventas/nueva_direccion.php?id=$id_cliente");
        exit();
    }
} else {
    $_SESSION['error'] = "Método no permitido.";
    header("Location: ../../front/ventas/clientes.php");
    exit();
}
?>
