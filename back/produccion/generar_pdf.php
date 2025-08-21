<?php
require_once('../tcpdf/tcpdf.php');
include '../db/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Cotización no válida.");
}

$id_cotizacion = $_GET['id'];

$queryCotizacion = "SELECT c.*, 
                           CONCAT(cl.nombre, ' ', cl.apellido_paterno, ' ', cl.apellido_materno) AS nombre_cliente,
                           d.calle, d.numero_casa, d.numero_interior, d.alcaldia_municipio, d.colonia, d.entre_calles,
                           d.ciudad, d.estado, d.codigo_postal
                    FROM cotizaciones c
                    JOIN clientes cl ON c.id_cliente = cl.id_cliente
                    JOIN direcciones d ON c.id_direccion = d.id_direccion
                    WHERE c.id_cotizacion = :id_cotizacion";

$stmt = $conn->prepare($queryCotizacion);
$stmt->bindParam(':id_cotizacion', $id_cotizacion, PDO::PARAM_INT);
$stmt->execute();
$cotizacion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cotizacion) {
    die("Cotización no encontrada.");
}

// Obtener productos
$queryProductos = "SELECT p.nombre_producto, cp.cantidad, cp.precio_unitario 
                   FROM cotizaciones_productos cp
                   JOIN recetas p ON cp.id_producto = p.id_receta
                   WHERE cp.id_cotizacion = :id_cotizacion";
$stmt = $conn->prepare($queryProductos);
$stmt->bindParam(':id_cotizacion', $id_cotizacion, PDO::PARAM_INT);
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener regalos
$queryRegalos = "SELECT r.nombre, cr.cantidad 
                 FROM cotizaciones_regalos cr
                 JOIN regalos r ON cr.id_regalo = r.id_regalo
                 WHERE cr.id_cotizacion = :id_cotizacion";
$stmt = $conn->prepare($queryRegalos);
$stmt->bindParam(':id_cotizacion', $id_cotizacion, PDO::PARAM_INT);
$stmt->execute();
$regalos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Crear PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Zoi Life');
$pdf->SetTitle('PEDIDO #'.$id_cotizacion);
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

// Agregar logo en la parte superior derecha
$pdf->Image('../../front/multimedia/logod.jpg', 160, 10, 40); // Ajusta ruta y tamaño si es necesario


$pdf->Ln(20);

// Título
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'PEDIDO #'.$id_cotizacion, 0, 1, 'C');
$pdf->Ln(5);

// Datos del cliente
$pdf->SetFont('helvetica', '', 11);
$pdf->Cell(0, 8, 'Cliente: ' . $cotizacion['nombre_cliente'], 0, 1);
$pdf->Cell(0, 8, 'Calle: ' . $cotizacion['calle'] . ' ' . $cotizacion['numero_casa'], 0, 1);
if ($cotizacion['numero_interior']) {
    $pdf->Cell(0, 8, 'Número Interior: ' . $cotizacion['numero_interior'], 0, 1);
}
$pdf->Cell(0, 8, 'Colonia: ' . $cotizacion['colonia'], 0, 1);
$pdf->Cell(0, 8, 'Municipio/Alcaldía: ' . $cotizacion['alcaldia_municipio'], 0, 1);
$pdf->Cell(0, 8, 'Ciudad: ' . $cotizacion['ciudad'], 0, 1);
$pdf->Cell(0, 8, 'Estado: ' . $cotizacion['estado'], 0, 1);
$pdf->Cell(0, 8, 'Código Postal: ' . $cotizacion['codigo_postal'], 0, 1);
if ($cotizacion['entre_calles']) {
    $pdf->Cell(0, 8, 'Entre Calles: ' . $cotizacion['entre_calles'], 0, 1);
}
$pdf->Cell(0, 8, 'Tipo de Envío: ' . $cotizacion['tipo_envio'], 0, 1);
$pdf->SetFillColor(255, 255, 153); // Amarillo pastel
$pdf->MultiCell(0, 8, 'Fecha de Entrega: ' . date('d/m/Y', strtotime($cotizacion['fecha_entrega'])), 0, 'L', 1);
$pdf->Ln(2);
$pdf->Cell(0, 8, 'Observaciones: ' . ($cotizacion['observaciones'] ?: 'N/A'), 0, 1);
$pdf->Cell(0, 8, 'Costo de Envío: $' . number_format($cotizacion['costo_envio'], 2), 0, 1);
$pdf->Ln(5);

// Tabla de productos
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(80, 10, 'Producto', 1, 0, 'C');
$pdf->Cell(30, 10, 'Cantidad', 1, 0, 'C');
$pdf->Cell(30, 10, 'Precio Unitario', 1, 0, 'C');
$pdf->Cell(30, 10, 'Subtotal', 1, 1, 'C');

$pdf->SetFont('helvetica', '', 11);
foreach ($productos as $producto) {
    $subtotal = $producto['cantidad'] * $producto['precio_unitario'];
    $pdf->Cell(80, 10, $producto['nombre_producto'], 1, 0);
    $pdf->Cell(30, 10, $producto['cantidad'], 1, 0, 'C');
    $pdf->Cell(30, 10, '$' . number_format($producto['precio_unitario'], 2), 1, 0, 'C');
    $pdf->Cell(30, 10, '$' . number_format($subtotal, 2), 1, 1, 'C');
}

// Tabla de regalos
if (count($regalos) > 0) {
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(80, 10, 'Regalo', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Cantidad', 1, 1, 'C');

    $pdf->SetFont('helvetica', '', 11);
    foreach ($regalos as $regalo) {
        $pdf->Cell(80, 10, $regalo['nombre'], 1, 0);
        $pdf->Cell(30, 10, $regalo['cantidad'], 1, 1, 'C');
    }
}

// Total
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Sub Total: $' . number_format($cotizacion['total'], 2), 0, 1, 'R');
$totalFinal = $cotizacion['total'] + $cotizacion['costo_envio'];
$pdf->Cell(0, 10, 'Total (productos + envío): $' . number_format($totalFinal, 2), 0, 1, 'R');


// Mostrar
$pdf->Output('Cotizacion_'.$id_cotizacion.'.pdf', 'I');
?>
