
<style>


    body {
        background-color: #f8f8f8;
    }

    .navbar {
        background-color: #f2ece9;
    }

    .search-bar {
        border-radius: 20px;
        width: 250px;
        border: 1px solid #ccc;
    }

    .navbar-brand img {
        height: 40px;
    }

    .btn-login {
        background-color: #2d4c48;
        color: white;
        border-radius: 20px;
        padding: 5px 20px;
        font-weight: bold;
    }

    .btn-login:hover {
        background-color: #1d3331;
        color: white;
    }

    .nav-icons i {
        font-size: 1.2rem;
        color: #2d4c48;
        margin-left: 15px;
    }

    .nav-icons i:hover {
        color: #1d3331;
    }

    .nav-links {
        background-color: white;
        padding: 10px 0;
    }

    .nav-links .nav-link {
        font-weight: bold;
        color: #2d4c48;
        margin: 0 20px;
    }

    .nav-links .nav-link:hover {
        color: #1d3331;
    }

    @media (max-width: 992px) {
        .search-bar {
            width: 100%;
        }

        .navbar .btn-login,
        .nav-icons {
            display: none;
        }

        .navbar-toggler {
            border: none;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        .nav-links .nav-link {
            display: block;
            text-align: center;
        }
    }

    .text-green {
        color: #2d4c48; /* El tono de verde que ya hemos estado utilizando */
    }


    .text-green {
        color: #2d4c48; /* Tono verde que estamos usando */
    }

    hr {
        border: none;
        height: 1px;
        background-color: #2d4c48;
        width: 100%;
    }

    .img-fluid {
        max-height: 500px;
        object-fit: cover;
    }


    .text-green {
        color: #2d4c48; /* Tono de verde usado en el resto del proyecto */
    }

    .img-fluid {
        border-radius: 20px;
        max-height: 400px;
        object-fit: cover;
    }

    .fw-bold {
        font-weight: bold;
    }


    .banner-mision {
        max-height: 400px;
        overflow: hidden;
        border-radius: 10px;
        position: relative;
    }



    .overlay-mision-text h1 {
        font-size: 2.5rem;
        font-weight: bold;
        color: #fff;
        margin: 0;
    }

    /* Ajuste del alto del banner en móvil y evitar salto de línea */
    @media (max-width: 768px) {


        .overlay-mision-text h1 {
            font-size: 1.4rem;
            white-space: nowrap; /* 👈 mantiene en un solo renglón */
            overflow: hidden;
            text-overflow: ellipsis; /* por si llega a ser muy largo */
        }
    }



    /* Bloque de imagen */
    .image-box {
        position: relative;
        overflow: hidden;
        border-radius: 10px;
    }
    .image-box img {
        width: 80%;
        height: 300px;
        transition: transform 0.3s ease;
    }

    .image-box:hover img {
        transform: scale(1.1);
    }

</style>



<div class="container my-5">
    <div class="position-relative banner-mision">
        <img src="../multimedia/ot/18.png" alt="Nuestra misión es contigo" class="img-fluid rounded w-100">
        <div class="overlay-mision-text position-absolute top-50 start-50 translate-middle text-white text-center px-4 py-2">
            <h1 class="mb-0">Nuestra misión es contigo</h1>
        </div>
    </div>
</div>


<div class="container text-center my-5">
    <h2 class="display-5 fw-bold text-green">Regenerar, sanar, Recupera tu bienestar</h2>
    <p class="lead mt-3 text-green">
        Tu cuerpo está diseñado para recuperarse, renovarse y florecer. Cada día, trabaja
        silenciosamente para restaurar su equilibrio, pero necesita ayuda para lograrlo.
        En Zoi Life, creemos en esa capacidad innata de regeneración. No solo creamos
        productos, creamos un camino para que la naturaleza y la ciencia trabajen juntas en
        favor de tu salud.
    </p>
</div>



<div class="container my-5 py-5 bg-white rounded shadow-sm">



    <div class="row g-4 justify-content-center">

        <!-- Tarjeta Productos -->
        <div class="col-12 col-md-4 d-flex align-items-stretch">
            <div class="card border-0 text-center bg-light w-100 py-4" style="background-color: #d9e2de;">
                <div class="card-body">

                    <h5 class="fw-bold" style="color: #2d4c48;">El valor de lo puro, el valor de lo real</h5>
                    <p class="text-muted">
                        La salud no se trata de fórmulas artificiales ni de soluciones pasajeras. Se trata de
                        nutrir el cuerpo con lo que la naturaleza ya nos ofrece.<br>
                        Respaldado por la ciencia: desarrollamos nuestras fórmulas con expertos
                        egresados del IPN.<br>
                        100% natural: sin químicos, sin toxinas, solo lo que tu cuerpo reconoce y
                        necesita.<br>
                        Pensado para ti: cada ingrediente cumple un propósito: ayudarte a regenerarte
                        desde adentro.<br>                   </p>
                </div>
            </div>
        </div>

        <!-- Tarjeta Productos -->
        <div class="col-12 col-md-4 d-flex align-items-stretch">
            <div class="card border-0 text-center bg-light w-100 py-4" style="background-color: #d9e2de;">
                <div class="card-body">

                    <h5 class="fw-bold" style="color: #2d4c48;">Productos</h5>
                    <p class="text-muted">
                        Tu cuerpo ya sabe cómo sanarse, solo necesita los elementos adecuados. Nuestras
                        fórmulas están diseñadas para impulsar la regeneración celular y fortalecer cada
                        sistema.<br>
                         Nutre, protege y equilibra tu bienestar con el poder de la naturaleza.                    </p>
                </div>
            </div>
        </div>

        <!-- Tarjeta Comunidad -->
        <div class="col-12 col-md-4 d-flex align-items-stretch">
            <div class="card border-0 text-center bg-light w-100 py-4" style="background-color: #d9e2de;">
                <div class="card-body">

                    <h5 class="fw-bold" style="color: #2d4c48;">Comunidad</h5>
                    <p class="text-muted">
                        Sanar es un proceso que se comparte. En Zoi Life, construimos una comunidad de
                        personas que han decidido tomar el control de su bienestar de manera natural.
                         Porque juntos, transformamos nuestra salud y nuestra vida.                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container my-5">
    <h2 class="text-center fw-bold mb-4 text-green">Compartimos nuestra visión con consciencia</h2>
    <hr class="mb-5">

    <div class="row align-items-start mb-5">
        <div class="col-md-6 mb-4 mb-md-0 image-box">
           <center> <img src="../multimedia/ot/17.png" alt="Manzanas orgánicas" class="img-fluid rounded shadow"></center>
        </div>
        <div class="col-md-6">
            <h5 class="fw-bold text-green mb-3">Nuestra Historia </h5>
            <p>
                La naturaleza siempre ha tenido las respuestas. Desde tiempos ancestrales, ha sido
                nuestra mejor aliada en la salud y el bienestar. En Zoi Life, nacimos con una misión
                clara: devolverle al cuerpo lo que el tiempo y la vida moderna le han quitado.
                Creemos que la regeneración es posible, que cada célula puede restaurarse cuando
                recibe los elementos adecuados.

            </p>

        </div>
    </div>

    <div class="row align-items-start">
        <div class="col-md-6 order-md-2 mb-4 mb-md-0 image-box">
            <center> <img src="../multimedia/ot/16.png" alt="Lettuce field" class="img-fluid rounded shadow"></center>
        </div>
        <div class="col-md-6 order-md-1">
            <p>
                Nuestra historia comenzó con un equipo de científicos egresados del Instituto
                Politécnico Nacional, apasionados por combinar el conocimiento más avanzado con
                la pureza de la naturaleza. Investigamos, exploramos y perfeccionamos fórmulas para
                brindarte productos libres de químicos, hechos para trabajar en armonía con tu
                organismo. Porque la salud no se trata solo de vivir más, sino de vivir mejor.            </p>

        </div>
    </div>


    <div class="row align-items-start mb-5" style="margin-top: 30px">
        <div class="col-md-6 mb-4 mb-md-0 image-box">
            <center>  <img src="../multimedia/ot/15.png" alt="Manzanas orgánicas" class="img-fluid rounded shadow"></center>
        </div>
        <div class="col-md-6">
            <h5 class="fw-bold text-green mb-3">Nuestra Misión</h5>
            <p>
                Regenerar la salud desde la raíz. Queremos que más personas descubran el poder de
                la naturaleza y cómo puede transformar su bienestar. Por eso, creamos productos
                100% naturales, sin químicos ni toxinas, que nutren el cuerpo, fortalecen el sistema
                inmune y ayudan a recuperar el equilibrio. No vendemos suplementos, ofrecemos
                soluciones respaldadas por la ciencia para que cada persona pueda sanar y florecer.

            </p>

        </div>
    </div>

    <div class="row align-items-start">
        <div class="col-md-6 order-md-2 mb-4 mb-md-0 image-box">
            <center>  <img src="../multimedia/ot/14.png" alt="Lettuce field" class="img-fluid rounded shadow"></center>
        </div>
        <div class="col-md-6 order-md-1">
            <h5 class="fw-bold text-green mb-3">Nuestra Visión</h5>
            <p>
                Ser el referente en bienestar natural, impulsando una nueva forma de entender la
                salud: una donde la prevención, la regeneración y el respeto por el cuerpo sean la
                base de una vida plena. Visualizamos un mundo donde más personas confían en la
                naturaleza para sanar, donde la ciencia y lo natural van de la mano para devolverle al
                cuerpo su capacidad innata de recuperación. Porque creemos en un futuro donde la
                salud se construye con lo mejor que la naturaleza nos da.            </p>

        </div>
    </div>


</div>



