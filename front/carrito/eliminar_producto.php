<?php
session_start();

$id = $_POST['id_producto'] ?? null;

if ($id && isset($_SESSION['carrito'][$id])) {
    unset($_SESSION['carrito'][$id]);
}

header("Location: carrito.php");
exit;
