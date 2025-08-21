<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre    = htmlspecialchars($_POST["nombre"]);
    $apellido  = htmlspecialchars($_POST["apellido"]);
    $correo    = filter_var($_POST["correo"], FILTER_SANITIZE_EMAIL);
    $telefono  = htmlspecialchars($_POST["telefono"]);
    $ciudad    = htmlspecialchars($_POST["ciudad"]);
    $mensaje   = htmlspecialchars($_POST["mensaje"]);

    $destinatario = "atencion_clientes@zoilife.com.mx";
    $asunto = "Nuevo mensaje de contacto desde el sitio web";

    $cuerpo = "
    Has recibido un nuevo mensaje desde el formulario de contacto:\n\n
    Nombre: $nombre $apellido\n
    Correo: $correo\n
    Teléfono: $telefono\n
    Ciudad: $ciudad\n
    Mensaje:\n$mensaje
    ";

    $cabeceras = "From: $correo\r\n";
    $cabeceras .= "Reply-To: $correo\r\n";
    $cabeceras .= "Content-Type: text/plain; charset=UTF-8\r\n";

    if (mail($destinatario, $asunto, $cuerpo, $cabeceras)) {
        echo "<script>alert('Mensaje enviado correctamente'); window.location.href='../../front/contacto/contacto.php';</script>";
    } else {
        echo "<script>alert('Error al enviar el mensaje. Intenta más tarde'); window.location.href='contacto.php';</script>";
    }
} else {
    header("Location: contacto.php");
    exit();
}
