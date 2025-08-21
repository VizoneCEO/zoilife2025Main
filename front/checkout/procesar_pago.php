<?php
session_start();
require_once '../../back/db/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Método no permitido";
    exit;
}

// Datos del formulario
$nombre           = $_POST['nombre'];
$apellido_paterno = $_POST['apellido_paterno'];
$apellido_materno = $_POST['apellido_materno'];
$correo           = $_POST['correo'];
$telefono         = $_POST['telefono'];

$calle     = $_POST['calle'];
$numero    = $_POST['numero_casa'];
$colonia   = $_POST['colonia'];
$municipio = $_POST['alcaldia_municipio'];
$ciudad    = $_POST['ciudad'];
$estado    = $_POST['estado'];
$cp        = $_POST['codigo_postal'];
$pais      = $_POST['pais'];

$tipo_envio    = $_POST['tipo_envio'];
$observaciones = $_POST['observaciones'];
$total_form    = (float)$_POST['total'];

$carrito = $_SESSION['carrito'] ?? [];
if (empty($carrito)) {
    echo "El carrito está vacío.";
    exit;
}

try {
    $conn->beginTransaction();

    $order_number = 'ZL-' . date('YmdHis') . '-' . rand(1000, 9999);

    $subtotal    = $total_form - (($tipo_envio === 'foraneo') ? 150 : 0);
    if ($subtotal < 0) $subtotal = 0;
    $costo_envio = ($tipo_envio === 'foraneo') ? 150 : 0;
    $total_srv   = $subtotal + $costo_envio;

    // 1. Insert cabecera en pedidos_web
    $sql = "INSERT INTO pedidos_web
        (nombre, apellido_paterno, apellido_materno, correo, telefono,
         calle, numero, colonia, municipio, ciudad, estado, codigo_postal, pais, referencias,
         subtotal, costo_envio, total, tipo_envio,
         estatus_pago, estatus_pedido, order_number)
        VALUES (
            '$nombre','$apellido_paterno','$apellido_materno','$correo','$telefono',
            '$calle','$numero','$colonia','$municipio','$ciudad','$estado','$cp','$pais','$observaciones',
            '$subtotal','$costo_envio','$total_srv','$tipo_envio',
            'pendiente','pendiente','$order_number'
        )";

    if (!$conn->query($sql)) {
        throw new Exception('Error en INSERT pedidos_web');
    }

    $id_pedido = $conn->lastInsertId();

    // 2. Insert productos en pedidos_web_productos
    $ids = array_keys($carrito);
    $ids_str = implode(',', array_map('intval',$ids));

    if ($ids_str !== '') {
        $sqlProd = "SELECT pw.id_producto_web, r.nombre_producto, pw.precio
                    FROM productos_web pw
                    JOIN recetas r ON r.id_receta = pw.id_receta
                    WHERE pw.id_producto_web IN ($ids_str)";
        $productos = $conn->query($sqlProd)->fetchAll(PDO::FETCH_ASSOC);

        foreach ($productos as $p) {
            $idpw = (int)$p['id_producto_web'];
            $cant = (int)$carrito[$idpw];
            if ($cant <= 0) continue;

            $precio    = (float)$p['precio'];
            $subtotalL = $precio * $cant;

            $sqlItem = "INSERT INTO pedidos_web_productos
                (id_pedido_web, id_producto_web, nombre_producto, precio_unitario, cantidad, subtotal)
                VALUES (
                    $id_pedido, $idpw, ".$conn->quote($p['nombre_producto']).",
                    $precio, $cant, $subtotalL
                )";
            if (!$conn->query($sqlItem)) {
                throw new Exception('Error insertando producto '.$idpw);
            }
        }
    }

    // 3. Confirmar todo
    $conn->commit();

    $_SESSION['order_number']  = $order_number;
    $_SESSION['id_pedido_web'] = $id_pedido;

    // --------- CLIP ---------
    $nombreCompleto = trim("$nombre $apellido_paterno $apellido_materno");

    $apiKey = '1a1e87fe-a066-41a8-a29b-60abd6e119ee';
    $secret = '910a5683-8c3d-4528-b909-c70b5da94d02';
    $basicToken = base64_encode("$apiKey:$secret");

    $successUrl = "https://zoilife.com.mx/front/checkout/success.php?order=$order_number";
    $errorUrl   = "https://zoilife.com.mx/error.php?order=$order_number";
    $defaultUrl = "https://zoilife.com.mx/";

    $data = [
        "amount" => (float)$total_srv,
        "currency" => "MXN",
        "purchase_description" => "Pedido de $nombreCompleto",
        "redirection_url" => [
            "success" => $successUrl,
            "error" => $errorUrl,
            "default" => $defaultUrl
        ],
        "metadata" => [
            "external_reference" => $order_number,
            "customer_info" => [
                "name" => $nombreCompleto,
                "email" => $correo,
                "phone" => $telefono
            ]
        ]
    ];

    $ch = curl_init("https://api.payclip.com/v2/checkout");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Basic $basicToken",
        "Content-Type: application/json"
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $res = json_decode($response, true);

    if ($httpCode === 200 && !empty($res["payment_request_url"])) {
        header("Location: " . $res["payment_request_url"]);
        exit;
    } else {
        echo "<h3>❌ Error al generar link de pago</h3>";
        echo "<strong>HTTP: $httpCode</strong><br><pre>";
        print_r($res);
        echo "</pre>";
        echo "<br>Order: $order_number | Total: $" . number_format($total_srv,2);
    }

} catch (Throwable $e) {
    if ($conn && $conn->inTransaction()) $conn->rollBack();
    echo "❌ Error: " . $e->getMessage();
}
