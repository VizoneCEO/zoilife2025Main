<?php
session_start();
// Incluimos la conexión a la base de datos
include '../../back/db/connection.php';

$id = $_POST['id_producto'] ?? null;
$cantidad = $_POST['cantidad'] ?? 1;

if (!$id) {
    // Si no hay ID, no hacemos nada y redirigimos.
    header("Location: ../productos/productos.php");
    exit;
}

// --- INICIO DE LA MODIFICACIÓN ---

// Buscamos los detalles del producto en la base de datos para el evento de Meta
try {
    $stmt = $conn->prepare("
        SELECT r.nombre_producto, pw.precio
        FROM productos_web pw
        JOIN recetas r ON pw.id_receta = r.id_receta
        WHERE pw.id_producto_web = :id
    ");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $producto_para_pixel = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($producto_para_pixel) {
        // Si encontramos el producto, preparamos los datos para el Píxel
        $_SESSION['meta_event_addtocart'] = [
            'id' => $id,
            'name' => $producto_para_pixel['nombre_producto'],
            'price' => $producto_para_pixel['precio'],
            'quantity' => $cantidad
        ];
    }
} catch (PDOException $e) {
    // Es buena práctica manejar errores, aunque sea solo registrándolos.
    error_log("Error al buscar producto para Píxel: " . $e->getMessage());
}

// --- FIN DE LA MODIFICACIÓN ---


if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$_SESSION['carrito'][$id] = ($_SESSION['carrito'][$id] ?? 0) + intval($cantidad);

header("Location: ../carrito/carrito.php");
exit;