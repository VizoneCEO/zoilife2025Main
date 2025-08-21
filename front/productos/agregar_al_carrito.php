<?php
session_start();

$id = $_POST['id_producto'] ?? null;
$cantidad = $_POST['cantidad'] ?? 1;

if (!$id) {
    header("Location: productos.php");
    exit;
}

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$_SESSION['carrito'][$id] = ($_SESSION['carrito'][$id] ?? 0) + intval($cantidad);

header("Location: ../carrito/carrito.php");
exit;
