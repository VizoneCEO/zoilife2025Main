<?php
require_once('../tcpdf/tcpdf.php');
include '../db/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Pedido no válido.");
}

$id_pedido = $_GET['id'];

// Traer pedido
$queryPedido = "SELECT pw.*, 
                       CONCAT(pw.nombre, ' ', pw.apellido_paterno, ' ', pw.apellido_materno) AS nombre_cliente
                FROM pedidos_web pw
                WHERE pw.id_pedido_web = :id_pedido";

$stmt = $conn->prepare($queryPedido);
$stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
$stmt->execute();
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    die("Pedido no encontrado.");
}

// Traer productos
$queryProductos = "SELECT r.nombre_producto, pwp.cantidad, pwp.precio_unitario
                   FROM pedidos_web_productos pwp
                   JOIN productos_web pw ON pwp.id_producto_web = pw.id_producto_web
                   JOIN recetas r ON pw.id_receta = r.id_receta
                   WHERE pwp.id_pedido_web = :id_pedido";
$stmt = $conn->prepare($queryProductos);
$stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Crear PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Zoilife');
$pdf->SetTitle('Pedido Online #'.$pedido['order_number']);
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

// Logo
$pdf->Image('../../front/multimedia/logod.jpg', 160, 10, 40);
$pdf->Ln(20);

// Título
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'PEDIDO ONLINE #'.$pedido['order_number'], 0, 1, 'C');
$pdf->Ln(5);

// Datos del cliente
$pdf->SetFont('helvetica', '', 11);
$pdf->Cell(0, 8, 'Cliente: ' . $pedido['nombre_cliente'], 0, 1);
$pdf->Cell(0, 8, 'Correo: ' . $pedido['correo'], 0, 1);
$pdf->Cell(0, 8, 'Teléfono: ' . $pedido['telefono'], 0, 1);
$pdf->Cell(0, 8, 'Calle: ' . $pedido['calle'] . ' ' . $pedido['numero'], 0, 1);
$pdf->Cell(0, 8, 'Colonia: ' . $pedido['colonia'], 0, 1);
$pdf->Cell(0, 8, 'Municipio/Alcaldía: ' . $pedido['municipio'], 0, 1);
$pdf->Cell(0, 8, 'Ciudad: ' . $pedido['ciudad'], 0, 1);
$pdf->Cell(0, 8, 'Estado: ' . $pedido['estado'], 0, 1);
$pdf->Cell(0, 8, 'Código Postal: ' . $pedido['codigo_postal'], 0, 1);
$pdf->Cell(0, 8, 'Tipo de Envío: ' . ucfirst($pedido['tipo_envio']), 0, 1);
$pdf->SetFillColor(255, 255, 153);
$pdf->MultiCell(0, 8, 'Observaciones: ' . ($pedido['referencias'] ?: 'N/A'), 0, 'L', 1);
$pdf->Ln(2);

// Totales iniciales
$pdf->Cell(0, 8, 'Subtotal: $' . number_format($pedido['subtotal'], 2), 0, 1);
$pdf->Cell(0, 8, 'Costo de Envío: $' . number_format($pedido['costo_envio'], 2), 0, 1);
$pdf->Ln(5);

// Tabla de productos
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(80, 10, 'Producto', 1, 0, 'C');
$pdf->Cell(30, 10, 'Cantidad', 1, 0, 'C');
$pdf->Cell(40, 10, 'Precio Unitario', 1, 0, 'C');
$pdf->Cell(30, 10, 'Subtotal', 1, 1, 'C');

$pdf->SetFont('helvetica', '', 11);
foreach ($productos as $producto) {
    $subtotal = $producto['cantidad'] * $producto['precio_unitario'];
    $pdf->Cell(80, 10, $producto['nombre_producto'], 1, 0);
    $pdf->Cell(30, 10, $producto['cantidad'], 1, 0, 'C');
    $pdf->Cell(40, 10, '$' . number_format($producto['precio_unitario'], 2), 1, 0, 'C');
    $pdf->Cell(30, 10, '$' . number_format($subtotal, 2), 1, 1, 'C');
}

// Total final
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'TOTAL: $' . number_format($pedido['total'], 2), 0, 1, 'R');

// Mostrar
$pdf->Output('PedidoWeb_'.$pedido['order_number'].'.pdf', 'I');
?>
