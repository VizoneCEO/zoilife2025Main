<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1484493516050229');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=1484493516050229&ev=PageView&noscript=1"
/></noscript>

<?php
session_start();
$total_items = array_sum($_SESSION['carrito'] ?? []);
?>

<?php
        // --- INICIO CÓDIGO PARA EVENTO AddToCart ---
        if (isset($_SESSION['meta_event_addtocart'])) {
            $eventData = $_SESSION['meta_event_addtocart'];
        ?>
        <script>
            // Disparamos el evento AddToCart con los datos que guardamos en la sesión
            fbq('track', 'AddToCart', {
                content_ids: ['<?php echo $eventData['id']; ?>'],
                content_name: '<?php echo addslashes($eventData['name']); ?>', // addslashes por si el nombre tiene comillas
                value: <?php echo $eventData['price']; ?>,
                currency: 'MXN'
            });
        </script>
        <?php
            // MUY IMPORTANTE: Borramos la variable de sesión para que no se dispare de nuevo
            unset($_SESSION['meta_event_addtocart']);
        }
        // --- FIN CÓDIGO PARA EVENTO AddToCart ---
        ?>

<!-- front/general/header.php -->
<link rel="stylesheet" href="../general/header.css">

<!-- Barra superior -->
<div class="top-bar bg-success text-white py-1 text-center small fw-bold">
    ¡ENVÍO GRATIS! &nbsp; | &nbsp;
    &nbsp;
    ¡LA OFERTA TERMINA ESTA NOCHE – EL TIEMPO SE AGOTA! &nbsp; | &nbsp;
    SOMOS ZOILIFE: SALUD Y NATURALEZA PARA TU BIENESTAR
</div>

<!-- Header Principal -->
<header class="main-header">
    <!-- Logo -->

    <a class="logo" href="../../index.php">
        <img src="../multimedia/logod.jpeg" alt="Zoilife Logo">
    </a>


    <!-- Barra de búsqueda -->
    <div class="search-container position-relative" style="margin-top: 10px;">
        <input type="text" id="buscador-productos" placeholder="Buscar Productos" class="form-control">
        <button class="search-btn"><i class="bi bi-search"></i></button>

        <!-- Resultados dinámicos -->
        <div id="resultados-busqueda" class="position-absolute bg-white w-100 rounded shadow-sm mt-1" style="z-index: 999;"></div>
    </div>


    <script>
        document.getElementById('buscador-productos').addEventListener('keyup', function () {
            const texto = this.value.trim();

            if (texto.length >= 2) {
                fetch('../../back/productos/buscar_productos.php?query=' + encodeURIComponent(texto))
                    .then(res => res.json())
                    .then(data => {
                        const resultados = document.getElementById('resultados-busqueda');
                        resultados.innerHTML = '';

                        if (data.length > 0) {
                            data.forEach(p => {
                                const item = document.createElement('div');
                                item.classList.add('d-flex', 'align-items-center', 'p-2', 'border-bottom');
                                item.innerHTML = `
                                <img src="../productosWeb/${p.foto_principal}" alt="${p.nombre_producto}" style="width:50px; height:50px; object-fit:cover; border-radius:5px; margin-right:10px;">
                                <div>
                                    <strong>${p.nombre_producto}</strong><br>
                                    <small>${p.categoria}</small>
                                </div>
                            `;
                                item.style.cursor = 'pointer';
                                item.onclick = () => {
                                    window.location.href = "../productos/detalleProducto.php?id=" + p.id_producto_web;
                                };
                                resultados.appendChild(item);
                            });
                        } else {
                            resultados.innerHTML = '<div class="p-2 text-muted">Sin resultados.</div>';
                        }
                    });
            } else {
                document.getElementById('resultados-busqueda').innerHTML = '';
            }
        });
    </script>

    <!-- Iconos de usuario y carrito -->
    <div class="user-cart">
        <a href="../auth/login.php" class="user-btn">
            <i class="bi bi-person"></i> LOG-IN
        </a>


        <a href="../../front/carrito/carrito.php" class="cart-btn position-relative">
            <i class="bi bi-bag" style="font-size: 1.5rem;"></i>
            <?php if ($total_items > 0): ?>
                <span class="cart-count position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            <?= $total_items ?>
        </span>
            <?php endif; ?>
        </a>



    </div>
</header>

<!-- Menú de navegación -->
<nav class="main-nav">
    <ul>
        <li><a href="../nosotros/nosotros.php">NOSOTROS</a></li>
        <li><a href="../productos/productos.php">PRODUCTOS</a></li>
        <li><a href="../blog/blog.php">BLOG</a></li>
        <li><a href="../contacto/contacto.php">CONTACTO</a></li>
    </ul>
</nav>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<!-- Botón flotante de WhatsApp -->
<a href="https://wa.me/525554749094" class="whatsapp-float" target="_blank">
    <i class="fab fa-whatsapp"></i>
</a>


<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
