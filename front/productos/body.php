<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda - Zoilife</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>


        .product-list {
            display: flex;
            flex-wrap: wrap;
            gap: 25px; /* más aire entre las tarjetas */
            justify-content: center;
        }

        .product-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 240px;
            height: 420px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .product-card img {
            height: 160px;
            width: auto;
            object-fit: contain;
            margin: 0 auto 10px;
            display: block;
        }






        body {
            font-family: Arial, sans-serif;
            background: #f8f8f8;
            margin: 0;
            padding: 0;
        }

        .nav-bar {
            background: #eaeaea;
            padding: 15px 0;
            text-align: center;
        }
        .nav-bar ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        .nav-bar ul li a {
            text-decoration: none;
            color: black;
            font-weight: bold;
            font-size: 14px;
        }

        .filter-section {
            background: #dedede;
            padding: 20px;
        }
        .filter-section h4 {
            font-weight: bold;
        }
        .filter-category {
            margin-bottom: 15px;
        }
        .filter-category h5 {
            margin-bottom: 10px;
            font-size: 14px;
        }

        .title {
            text-align: center;
            color: #315c2b;
            font-weight: bold;
            margin: 20px 0;
        }


        .new-tag {
            color: green;
            font-size: 14px;
        }
        .rating i {
            color: gold;
        }
        .reviews, .price {
            font-size: 14px;
        }
        .buy-btn {
            background: #68b04a;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        .buy-btn:hover {
            background: #4d8a38;
        }

        /* Ocultar header de móvil en escritorio */
        .header-mobile {
            display: none;
        }

        /* Mostrar header de escritorio en pantallas grandes */
        @media (min-width: 769px) {
            .header-desktop {
                display: block;
            }
        }

        /* Ocultar header de escritorio en móviles y mostrar el de móvil */
        @media (max-width: 768px) {
            .header-desktop {
                display: none;
            }

            .header-mobile {
                display: block;
            }
        }

    </style>
</head>

<body>

<?php
include '../../back/db/connection.php';

$categoria = $_GET['categoria'] ?? 'Todos';

$query = "SELECT pw.*, r.nombre_producto FROM productos_web pw 
          JOIN recetas r ON pw.id_receta = r.id_receta 
          WHERE pw.estatus = 'activo'";

if ($categoria !== 'Todos') {
    $query .= " AND pw.categoria = :categoria";
}

$query .= " ORDER BY pw.id_producto_web DESC";
$stmt = $conn->prepare($query);

if ($categoria !== 'Todos') {
    $stmt->bindParam(':categoria', $categoria);
}

$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Header para escritorio -->
<header class="header-desktop">
    <nav class="nav-bar">
        <?php
        $categorias = [
            "Todos",
            "Sistema Celular",
            "Sistema Cardiovascular",
            "Sistema Digestivo",
            "Sistema Inmunológico",
            "Sistema Esquelético"
        ];
        ?>

        <ul>
            <?php foreach ($categorias as $cat): ?>
                <li>
                    <a href="?categoria=<?= urlencode($cat) ?>" style="<?= $categoria === $cat ? 'color:#68b04a; text-decoration:underline;' : '' ?>">
                        <?= $cat ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

    </nav>
</header>

<!-- Header para móvil -->
<!-- Header para móvil -->
<header class="header-mobile">
    <nav class="nav-bar">
        <ul>
            <li><a href="?categoria=<?= urlencode('Todos') ?>" style="<?= $categoria === 'Todos' ? 'color:#68b04a; text-decoration:underline;' : '' ?>">Todos</a></li>
            <li><a href="?categoria=<?= urlencode('Sistema Celular') ?>" style="<?= $categoria === 'Sistema Celular' ? 'color:#68b04a; text-decoration:underline;' : '' ?>">Sistema Celular</a></li>
            <li><a href="?categoria=<?= urlencode('Sistema Cardiovascular') ?>" style="<?= $categoria === 'Sistema Cardiovascular' ? 'color:#68b04a; text-decoration:underline;' : '' ?>">Sistema Cardiovascular</a></li>
        </ul>
        <ul style="margin-top: 15px;">
            <li><a href="?categoria=<?= urlencode('Sistema Digestivo') ?>" style="<?= $categoria === 'Sistema Digestivo' ? 'color:#68b04a; text-decoration:underline;' : '' ?>">Sistema Digestivo</a></li>
            <li><a href="?categoria=<?= urlencode('Sistema Inmunológico') ?>" style="<?= $categoria === 'Sistema Inmunológico' ? 'color:#68b04a; text-decoration:underline;' : '' ?>">Sistema Inmunológico</a></li>
            <li><a href="?categoria=<?= urlencode('Sistema Esquelético') ?>" style="<?= $categoria === 'Sistema Esquelético' ? 'color:#68b04a; text-decoration:underline;' : '' ?>">Sistema Esquelético</a></li>
        </ul>
    </nav>
</header>



<div class="container">
    <div class="row">
        <h2 class="title">SALUD AL ALCANCE DE TU MANO</h2>

        <div class="product-list mx-auto">
            <?php foreach ($productos as $p): ?>
                <div class="product-card">
                    <img src="../productosWeb/<?= $p['foto_principal'] ?>" alt="<?= $p['nombre_producto'] ?>">
                    <?php if ($p['en_nuevos_productos']): ?>
                        <span class="new-tag">Nuevo</span>
                    <?php endif; ?>
                    <h5><?= $p['nombre_producto'] ?></h5>
                    <div class="rating">
                        <?php for ($i = 0; $i < $p['estrellas']; $i++): ?>
                            <i class="fas fa-star"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="reviews">2 Reviews</p>
                    <p class="price">$<?= number_format($p['precio'], 2) ?></p>
                    <a href="detalleProducto.php?id=<?= $p['id_producto_web'] ?>">
                        <button class="buy-btn">COMPRAR</button>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

    </div>

</div>




<script src="script.js"></script>
</body>
</html>
