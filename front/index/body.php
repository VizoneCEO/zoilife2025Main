<!-- front/index/body.php -->
<link rel="stylesheet" href="front/index/index.css">

<style>

    /* 👇 SOLO uno por slide en móvil */
    @media (max-width: 768px) {
        .carousel .carousel-item > .container > .row > div {
            display: none;
        }

        .carousel .carousel-item.active > .container > .row > div:first-child {
            display: block;
            flex: 0 0 100%;
            max-width: 100%;
        }
    }




    .modal-content video {
        width: 100%;
        max-height: 400px;
        object-fit: contain;
        border-radius: 10px;
    }














    .inspiration-content h1 {
        font-size: 3rem;
        font-weight: bold;
        line-height: 1.3;
        margin-bottom: 15px;
        text-shadow: 2px 2px 5px rgba(0,0,0,0.6);
    }

    .inspiration-content p {
        font-size: 1.2rem;
        text-shadow: 1px 1px 4px rgba(0,0,0,0.5);
    }







    .seccion-bienestar-visual {
        width: 100%;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        position: relative;
        overflow: hidden;
    }


    .fondo-bienestar-visual {
        background-image: url('front/multimedia/ot/a13.png'); /* asegúrate que la ruta sea correcta */
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        padding-left: 5%;
        padding-right: 5%;
    }

    .contenido-bienestar-visual {
        color: white;
        max-width: 500px;
        z-index: 2;
        text-align: left;
    }

    .contenido-bienestar-visual h1 {
        font-size: 2.5rem;
        font-weight: bold;
        line-height: 1.3;
        margin-bottom: 20px;
    }

    .contenido-bienestar-visual h1 span {
        color: #cbe8a2;
    }

    .btn-recomendaciones-visual {
        border: 1px solid #fff;
        padding: 10px 20px;
        color: white;
        text-decoration: none;
        border-radius: 20px;
        font-size: 14px;
        transition: all 0.3s ease-in-out;
        display: inline-block;
    }

    .btn-recomendaciones-visual:hover {
        background-color: white;
        color: #000;
    }


    @media (max-width: 768px) {
        .fondo-bienestar-visual {
            background-size: cover;
                background-image: url('front/multimedia/ot/a13-mobile.png');
            background-position: top center;
            padding-left: 20px;
            padding-right: 20px;
            justify-content: center;
        }

        .contenido-bienestar-visual {
            position: relative;
            z-index: 2;
            margin-top: -340px; /* Subimos el texto */
            padding-left: 10px;
            padding-right: 10px;
        }

        .contenido-bienestar-visual h1 {
            font-size: 1.8rem;
            line-height: 1.3;
        }

        .btn-recomendaciones-visual {
            font-size: 13px;
            padding: 8px 18px;
        }
    }


















    /* Botón de Testimonio */
    .testimonial-btn {
        background-color: #8BC34A;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        font-weight: bold;
        display: inline-block;
        transition: background 0.3s ease-in-out;
    }

    .testimonial-btn:hover {
        background-color: #7CB342;
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        width: 90%;
        max-width: 600px;
        position: relative;
        text-align: center;
    }

    .modal video {
        width: 100%;
        border-radius: 5px;
    }

    /* Botón de Cerrar */
    .close-modal {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 20px;
        cursor: pointer;
        background: none;
        border: none;
        color: #555;
    }

    .close-modal:hover {
        color: black;
    }

    /* Mostrar el modal cuando esté activo */
    .modal.show {
        display: flex;
    }





</style>
<!-- Sección principal -->
<section class="hero-section">
    <!-- Video de fondo -->
    <video autoplay muted loop class="bg-video">
        <source src="front/multimedia/fondo.mp4" type="video/mp4">
        Tu navegador no soporta videos.
    </video>

    <!-- Capa oscura para mejor contraste -->
    <div class="overlay"></div>

    <!-- Contenido sobre el video -->
    <div class="container content-overlay">
        <div class="row align-items-center">
            <!-- Lado izquierdo: Texto e iconos -->
            <div class="col-lg-6 text-center text-lg-start">
                <center><h1 class="promo-title">2X1 <br>
                    <span class="promo-subtitle">CÉLULA MASTER</span></h1>
                </center>
                <!-- Iconos -->
                <div class="benefits-icons d-flex justify-content-center  gap-3 my-4">

                    <img src="front/multimedia/security.png" alt="Beneficio 1">
                    <img src="front/multimedia/nw.png" alt="Beneficio 2">
                    <img src="front/multimedia/heart.png" alt="Beneficio 3">
                    <img src="front/multimedia/dna.png" alt="Beneficio 4">

                </div>

                <!-- Botón de compra -->
                <center><a href="front/productos/productos.php" class=" btn-buy">COMPRAR</a></center>
            </div>

            <!-- Lado derecho: Imagen del producto -->
            <div class="col-lg-6 text-center">
                <img src="front/multimedia/Slide1.png" alt="Célula Master" class="product-image">
            </div>
        </div>
    </div>
</section>







<!-- Sección de Productos -->
<section class="product-section">
    <div class="container">
        <!-- Título de la sección -->
        <h2 class="section-title">Productos Recomendados</h2>

        <?php
        include 'back/db/connection.php';
        $stmt = $conn->prepare("
    SELECT pw.*, r.nombre_producto 
    FROM productos_web pw
    JOIN recetas r ON pw.id_receta = r.id_receta
    WHERE pw.estatus = 'activo' AND pw.productos_r = 1
    ORDER BY pw.id_producto_web DESC
");
        $stmt->execute();
        $recomendados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <div id="carouselRecomendados" class="carousel slide my-5" data-bs-ride="carousel">
            <div class="carousel-inner">

                <?php
                $grupo = [];
                foreach ($recomendados as $index => $p) {
                    $grupo[] = $p;

                    // Cada 3 productos o último producto, cerramos slide
                    if (count($grupo) === 3 || $index === array_key_last($recomendados)) {
                        $isFirst = ($index - count($grupo) + 1 === 0);
                        echo '<div class="carousel-item ' . ($isFirst ? 'active' : '') . '">';
                        echo '<div class="container">';
                        echo '<div class="row justify-content-center">';

                        foreach ($grupo as $item) {
                            echo '
                <div class="col-4 col-md-4 mb-4">
<div class="text-center p-3 bg-white rounded" style="box-shadow: none; border: none;">
                        <img src="front/productosWeb/' . $item['foto_principal'] . '" class="img-fluid mb-2" style="max-height: 200px; object-fit: contain;">
                        <h5 class="product-name">' . $item['nombre_producto'] . '</h5>
                        <div class="rating mb-2">';
                            for ($j = 0; $j < $item['estrellas']; $j++) {
                                echo '<i class="bi bi-star-fill text-warning"></i>';
                            }
                            echo '</div>
                        <p class="product-price">$' . number_format($item['precio'], 2) . '</p>
                        <a href="front/productos/detalleProducto.php?id=' . $item['id_producto_web'] . '" class="btn btn-success">COMPRAR</a>
                    </div>
                </div>';
                        }

                        echo '</div></div></div>'; // cierre de row, container y carousel-item
                        $grupo = []; // Reinicia grupo
                    }
                }
                ?>


            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#carouselRecomendados" data-bs-slide="prev">
                <span class="carousel-control-prev-icon bg-dark rounded-circle" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselRecomendados" data-bs-slide="next">
                <span class="carousel-control-next-icon bg-dark rounded-circle" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>





    </div> <!-- este faltaba -->
</section> <!-- este también -->





<!-- Sección de Valores -->
<section class="values-section">
    <div class="container">
        <!-- Bloque 1: Comunidad -->
        <div class="row align-items-center mb-4">
            <div class="col-lg-5">
                <div class="image-box">
                    <img src="front/multimedia/ot/10.png" alt="Comunidad">
                    <div class="overlay-text">SOMOS <span>ACCIÓN</span></div>
                </div>
            </div>
            <div class="col-lg-7">
                <h3 class="value-title" style="margin-top: 20px">Somos el Momento de Actuar
                </h3>
                <p class="value-text">
                    El tiempo no espera y tu cuerpo tampoco. Cada día es una oportunidad para nutrirlo,
                    fortalecerlo y devolverle el equilibrio que necesita. La verdadera salud no se compra en
                    una farmacia, se cultiva con lo que le das a tu cuerpo hoy. En Zoi Life, combinamos la
                    pureza de la naturaleza con la precisión de la ciencia para ofrecerte suplementos que
                    transforman tu bienestar desde la raíz. No dejes que el mañana llegue sin haber cuidado
                    de ti hoy.

                </p>
            </div>
        </div>

        <!-- Bloque 2: Prevención -->
        <div class="row align-items-center mb-4 flex-lg-row-reverse">
            <div class="col-lg-5">
                <div class="image-box">
                    <img src="front/multimedia/prevencion.png" alt="Prevención">
                    <div class="overlay-text">SOMOS <span>PREVENCIÓN</span></div>
                </div>
            </div>
            <div class="col-lg-7">
                <h3 class="value-title" style="margin-top: 20px">Somos Prevención y Corrección</h3>
                <p class="value-text">
                    Creemos que las enfermedades actuales no solo se tratan, se previenen y se revierten
                    con los nutrientes adecuados. Tu cuerpo tiene la capacidad de regenerarse si le das lo que
                    realmente necesita. En Zoi Life, diseñamos suplementos que no solo fortalecen tu
                    organismo, sino que también lo ayudan a recuperar su equilibrio natural. Cuidarte hoy es la
                    clave para un mañana más saludable.                </p>
            </div>
        </div>

        <!-- Bloque 3: Bienestar -->
        <div class="row align-items-center mb-4">
            <div class="col-lg-5">
                <div class="image-box">
                    <img src="front/multimedia/20.png" alt="Bienestar">
                    <div class="overlay-text">SOMOS <span>BIENESTAR</span></div>
                </div>
            </div>
            <div class="col-lg-7">
                <h3 class="value-title" style="margin-top: 20px">Somos Bienestar Real
                </h3>
                <p class="value-text">
                    Bienestar no es solo sentirse bien, es vivir con plenitud, moverte sin dolor, dormir
                    profundamente y despertar con energía cada día. Zoi Life es más que suplementos; es
                    una invitación a recuperar la conexión con lo natural y hacer de tu salud una
                    prioridad.                </p>
            </div>
        </div>


    </div>
</section>
























<section class="seccion-bienestar-visual">
    <div class="fondo-bienestar-visual">
        <div class="contenido-bienestar-visual">
            <h1>Recupera tu bienestar <br><span>Empieza desde adentro</span></h1>
            <a href="front/blog/blog.php" class="btn-recomendaciones-visual">RECOMENDACIONES</a>
        </div>
    </div>
</section>










<!-- Sección de Productos Nuevos Recomendados -->
<section class="new-products-section">
    <div class="container text-center">
        <!-- Título de la sección -->
        <h2><span>PRODUCTOS NUEVOS</span> RECOMENDADOS</h2>

        <a href="front/productos/productos.php" class="view-all">VER TODO</a>


        <!-- Producto -->
        <?php
        include 'back/db/connection.php';

        $stmt = $conn->prepare("
    SELECT pw.*, r.nombre_producto 
    FROM productos_web pw
    JOIN recetas r ON pw.id_receta = r.id_receta
    WHERE pw.estatus = 'activo' AND pw.en_nuevos_productos = 1
    ORDER BY pw.id_producto_web DESC
    LIMIT 8
");
        $stmt->execute();
        $nuevos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <div class="row">
            <?php foreach ($nuevos as $n): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="product-card">
                        <img src="front/productosWeb/<?= $n['foto_principal'] ?>" alt="<?= $n['nombre_producto'] ?>" class="product-img">

                        <p class="product-status">Nuevo</p>
                        <h3 class="product-name"><?= $n['nombre_producto'] ?></h3>

                        <!-- Calificación -->
                        <div class="rating">
                            <?php for ($j = 0; $j < $n['estrellas']; $j++): ?>
                                <i class="bi bi-star-fill"></i>
                            <?php endfor; ?>
                        </div>

                        <p class="reviews">2 Reviews</p> <!-- Simulado por ahora -->

                        <!-- Precio -->
                        <p class="product-price">$<?= number_format($n['precio'], 2) ?></p>

                        <!-- Botón de compra -->
                        <a href="front/productos/detalleProducto.php?id=<?= $n['id_producto_web'] ?>" class="btn-buy">COMPRAR</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>


        <!-- Botón para ir a la tienda -->
        <a href="front/productos/productos.php" class="btn-store">IR A LA TIENDA</a>
    </div>
</section>



<!-- Sección de Testimonios -->
<section class="testimonials-section">
    <div class="container text-center">
        <h2><span>TU SALUD, ES NUESTRA MISIÓN.</span></h2>
        <p class="subtitle">TU ENERGÍA, TU BIENESTAR, TU ELECCIÓN.</p>

        <div class="row justify-content-center mt-4">
            <?php
            $clientes = [
                [
                    "nombre" => "Cristina",
                    "rol" => "Cliente",
                    "foto" => "front/multimedia/v1i.png",
                    "testimonio" => "Mi energía era muy baja, me sentía cansada todo el tiempo y nada me ayudaba. Desde que empecé a tomar Célula Master, mi cuerpo ha cambiado por completo.",
                    "video" => "front/multimedia/v1.mp4"


                ],
                [
                    "nombre" => "Maria Teresa",
                    "rol" => "Cliente",
                    "foto" => "front/multimedia/v2i.png",
                    "testimonio" => "Tenía problemas para dormir, y mi digestión era un caos. Desde que uso Célula Master, descanso mejor y me siento ligera todo el día.",
                    "video" => "front/multimedia/v2.mp4"
                ],
                [
                    "nombre" => "Leticia",
                    "rol" => "Cliente",
                    "foto" => "front/multimedia/v3i.png",
                    "testimonio" => "Sufría de dolores constantes y falta de ánimo. Con Célula Master, tengo más energía y mi cuerpo responde mejor cada día",
                    "video" => "front/multimedia/v3.mp4"
                ]
            ];

            foreach ($clientes as $index => $cliente): ?>
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="testimonial-card">
                        <img src="<?= $cliente['foto'] ?>" class="client-photo rounded-circle mb-3" width="80" height="80" alt="<?= $cliente['nombre'] ?>">

                        <h5 class="client-name"><?= $cliente["nombre"] ?></h5>
                        <p class="client-role"><?= $cliente["rol"] ?></p>

                        <div class="stars">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <i class="bi bi-star-fill"></i>
                            <?php endfor; ?>
                        </div>

                        <p class="testimonial-text">
                            <?= $cliente["testimonio"] ?>
                        </p>

                        <!-- Botón para abrir el modal específico -->
                        <button class="testimonial-btn" onclick="openModal('modal<?= $index ?>', 'video<?= $index ?>')">Ver Testimonio</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>





<?php foreach ($clientes as $index => $cliente): ?>
    <div id="modal<?= $index ?>" class="modal">
        <div class="modal-content">
            <button class="close-modal" onclick="closeModal('modal<?= $index ?>', 'video<?= $index ?>')">✖</button>
            <br>
            <video id="video<?= $index ?>" controls>
                <source src="<?= $cliente['video'] ?>" type="video/mp4">
                Tu navegador no soporta el video.
            </video>
        </div>
    </div>
<?php endforeach; ?>



<script>
    function openModal(modalId, videoId) {
        let modal = document.getElementById(modalId);
        let video = document.getElementById(videoId);
        modal.classList.add("show");

        if (video) {
            video.play();
        }
    }

    function closeModal(modalId, videoId) {
        let modal = document.getElementById(modalId);
        let video = document.getElementById(videoId);
        modal.classList.remove("show");

        if (video) {
            video.pause();
            video.currentTime = 0;
        }
    }

    // Cierra el video si el usuario cambia de pestaña
    document.addEventListener("visibilitychange", function () {
        document.querySelectorAll(".modal video").forEach(video => {
            video.pause();
            video.currentTime = 0;
        });
    });

    document.addEventListener('click', function(e) {
        document.querySelectorAll('.modal').forEach(modal => {
            if (modal.classList.contains('show') && e.target === modal) {
                const video = modal.querySelector('video');
                modal.classList.remove('show');
                if (video) {
                    video.pause();
                    video.currentTime = 0;
                }
            }
        });
    });

</script>

