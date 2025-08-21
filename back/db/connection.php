<?php
// Configuración de la conexión
$host = 'localhost';
$user = 'zoilife_Control';
$password = 'L29edesma16$!';
$dbname = 'zoilife2025';

try {
    // Crear conexión con PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Manejo de errores
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
