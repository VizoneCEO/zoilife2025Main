<style>
    /* Contenedor principal */
    .product-container {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        max-width: 1200px;
        margin: 50px auto;
        padding: 20px;
    }

    /* Imagen del producto */
    .product-image {
        width: 45%;
        text-align: center;
    }

    .product-image img {
        width: 100%;
        max-width: 400px;
    }

    .product-thumbnails {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 10px;
    }

    .product-thumbnails img {
        width: 80px;
        height: auto;
        cursor: pointer;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .product-thumbnails img:hover {
        border-color: #8BC34A;
    }

    /* Información del producto */
    .product-info {
        width: 50%;
    }

    .product-info h1 {
        font-size: 24px;
        margin-bottom: 10px;
    }

    .product-price {
        font-size: 28px;
        font-weight: bold;
        margin: 10px 0;
    }

    .product-rating {
        display: flex;
        align-items: center;
        gap: 5px;
        color: #8BC34A;
    }

    .product-rating i {
        font-size: 18px;
    }

    /* Controles de cantidad */
    .product-quantity {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 15px 0;
    }

    .quantity-box {
        display: flex;
        align-items: center;
        gap: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 5px 10px;
    }

    .quantity-box button {
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        color: #555;
    }

    .quantity-box input {
        width: 30px;
        text-align: center;
        border: none;
        font-size: 16px;
        outline: none;
    }

    /* Botón de comprar */
    .buy-button {
        display: block;
        width: 100%;
        background-color: #8BC34A;
        color: white;
        padding: 12px;
        text-align: center;
        font-size: 16px;
        font-weight: bold;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 20px;
    }

    .buy-button:hover {
        background-color: #7CB342;
    }

    /* Descripción */
    .product-description {
        margin-top: 20px;
        font-size: 14px;
        line-height: 1.5;

    }

    /* Tabla de información */
    .product-info-table {
        width: 100%;
        margin-top: 20px;
        border-collapse: collapse;
    }

    .product-info-table th,
    .product-info-table td {
        border-bottom: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }

    .product-info-table th {
        font-weight: bold;
        background-color: #f9f9f9;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .product-container {
            flex-direction: column;
            align-items: center;
        }

        .product-image,
        .product-info {
            width: 100%;
            text-align: center;
        }

        .product-thumbnails {
            justify-content: center;
        }

        .product-info h1 {
            font-size: 22px;
        }

        .buy-button {
            font-size: 14px;
        }
    }

</style>
<?php
include '../../back/db/connection.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "Producto no encontrado.";
    exit;
}

$stmt = $conn->prepare("
    SELECT pw.*, r.nombre_producto 
    FROM productos_web pw
    JOIN recetas r ON pw.id_receta = r.id_receta
    WHERE pw.id_producto_web = :id AND pw.estatus = 'activo'
");
$stmt->bindParam(':id', $id);
$stmt->execute();
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    echo "Producto no disponible.";
    exit;
}

// Obtener fotos anexas
$stmt_fotos = $conn->prepare("
    SELECT url_foto FROM productos_web_fotos
    WHERE id_producto_web = :id AND estatus = 'activo'
    ORDER BY orden ASC
");
$stmt_fotos->bindParam(':id', $id);
$stmt_fotos->execute();
$fotos_anexas = $stmt_fotos->fetchAll(PDO::FETCH_ASSOC);
?>



<!-- Contenedor del producto -->
<div class="product-container">
    <!-- Imagen principal del producto -->
    <div class="product-image">
        <img id="main-product-image" src="../productosWeb/<?= $producto['foto_principal'] ?>" alt="<?= $producto['nombre_producto'] ?>">

        <div class="product-thumbnails">
            <!-- Agregamos la imagen principal como primer miniatura -->
            <img src="../productosWeb/<?= $producto['foto_principal'] ?>" alt="Vista principal" onclick="cambiarImagen(this)">

            <?php foreach ($fotos_anexas as $foto): ?>
                <img src="../productosWeb/<?= $foto['url_foto'] ?>" alt="Vista extra" onclick="cambiarImagen(this)">
            <?php endforeach; ?>


        </div>
    </div>

    <!-- Información del producto -->
    <div class="product-info">
        <span class="product-new">Nuevo</span>
        <h1><?= $producto['nombre_producto'] ?></h1>

        <div class="product-rating">
            <?php for ($i = 0; $i < $producto['estrellas']; $i++): ?>
                <i class="bi bi-star-fill"></i>
            <?php endfor; ?>
            <span><?= $producto['estrellas'] ?> Estrellas</span>
        </div>

        <div class="product-price">$<?= number_format($producto['precio'], 2) ?></div>

        <!-- Control de cantidad -->
        <div class="product-quantity">
            <label>Cantidad:</label>
            <div class="quantity-box">
                <button onclick="cambiarCantidad(-1)">-</button>
                <input type="text" id="cantidad" value="1">
                <button onclick="cambiarCantidad(1)">+</button>
            </div>
        </div>

        <!-- Botón de compra -->
        <form method="POST" action="agregar_al_carrito.php">
            <input type="hidden" name="id_producto" value="<?= $producto['id_producto_web'] ?>">
            <input type="hidden" name="cantidad" id="inputCantidad" value="1">
            <button type="submit" class="buy-button">AGREGAR AL CARRITO</button>
        </form>


        <!-- Descripción del producto -->
        <div class="product-description">
            <?= html_entity_decode($producto['descripcion']) ?>
        </div>

        <!-- Información del producto -->
        <?php if (!empty($producto['contenido'])): ?>
            <table class="product-info-table">
                <tr>
                    <th>INFORMACIÓN DEL PRODUCTO</th>
                </tr>
                <tr>
                    <td colspan="2"><?= html_entity_decode($producto['contenido']) ?></td>
                </tr>
            </table>
        <?php endif; ?>
    </div>
</div>

<script>
    function cambiarCantidad(cambio) {
        const input = document.getElementById('cantidad');
        const hidden = document.getElementById('inputCantidad');
        let valor = parseInt(input.value);
        valor = isNaN(valor) ? 1 : valor + cambio;
        if (valor < 1) valor = 1;
        input.value = valor;
        hidden.value = valor; // Actualiza también el input hidden
    }

</script>

<script>
    function cambiarImagen(imagen) {
        const principal = document.getElementById('main-product-image');
        principal.src = imagen.src;
    }
</script>

