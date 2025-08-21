<?php
session_start();
include '../../back/db/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_direccion = $_POST['id_direccion'];
    $id_cliente = $_POST['id_cliente'];
    $calle = trim($_POST['calle']);
    $numero_casa = trim($_POST['numero_casa']);
    $numero_interior = trim($_POST['numero_interior']);
    $colonia = trim($_POST['colonia']); // ✅ NUEVO
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
        empty($id_cliente) || empty($id_direccion) || empty($calle) || empty($numero_casa) ||
        empty($colonia) || empty($ciudad) || empty($estado) || empty($codigo_postal) || empty($pais) ||
        empty($latitud) || empty($longitud) || empty($tipo_vivienda)
    ) {
        $_SESSION['error'] = "Todos los campos obligatorios deben estar completos.";
        header("Location: ../../front/ventas/editar_direccion.php?id=$id_direccion");
        exit();
    }

    // Generar link de Google Maps
    $google_maps_link = "https://www.google.com/maps?q=" . urlencode($latitud) . "," . urlencode($longitud);

    try {
        $query = "UPDATE direcciones 
                  SET calle = :calle, 
                      numero_casa = :numero_casa, 
                      numero_interior = :numero_interior, 
                      colonia = :colonia, 
                      alcaldia_municipio = :alcaldia_municipio,
                      entre_calles = :entre_calles,
                      ciudad = :ciudad, 
                      estado = :estado, 
                      codigo_postal = :codigo_postal, 
                      pais = :pais, 
                      observaciones = :observaciones, 
                      tipo_vivienda = :tipo_vivienda, 
                      latitud = :latitud, 
                      longitud = :longitud, 
                      google_maps_link = :google_maps_link 
                  WHERE id_direccion = :id_direccion";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':calle', $calle);
        $stmt->bindParam(':numero_casa', $numero_casa);
        $stmt->bindParam(':numero_interior', $numero_interior);
        $stmt->bindParam(':colonia', $colonia); // ✅ NUEVO
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
        $stmt->bindParam(':id_direccion', $id_direccion);
        $stmt->execute();

        $_SESSION['success'] = "Dirección actualizada correctamente.";
        header("Location: ../../front/ventas/direcciones.php?id=$id_cliente");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al actualizar la dirección: " . $e->getMessage();
        header("Location: ../../front/ventas/editar_direccion.php?id=$id_direccion");
        exit();
    }
} else {
    $_SESSION['error'] = "Método no permitido.";
    header("Location: ../../front/ventas/clientes.php");
    exit();
}
