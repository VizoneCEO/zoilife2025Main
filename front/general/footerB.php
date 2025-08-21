<!-- front/general/header.php -->
<link rel="stylesheet" href="../general/footer.css">

<style>
    /* Modal estándar (ya lo tienes) */
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
        overflow-y: auto;
    }

    .modal.show {
        display: flex;
    }

    .modal-content {
        background-color: white;
        border-radius: 10px;
        position: relative;
        text-align: center;
        padding: 20px;
    }

    /* COFEPRIS modal específico */
    .cof-modal {
        max-width: 90%;
        width: 100%;
        max-height: 95vh;
        overflow-y: auto;
    }

    .cof-img-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 25px;
        margin-top: 20px;
    }

    .cof-img {
        width: 100%;
        max-width: 800px;
        height: auto;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        object-fit: contain;
    }

    /* Botón de cerrar */
    .close-modal {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 22px;
        cursor: pointer;
        background: none;
        border: none;
        color: #888;
        font-weight: bold;
    }

    .close-modal:hover {
        color: #000;
    }

</style>

<!-- Modal COFEPRIS FUNCIONAL -->
<!-- Modal COFEPRIS -->
<div id="modalCofepris" class="modal">
    <div class="modal-content cof-modal">
        <button class="close-modal" onclick="closeModal('modalCofepris')">✖</button>
        <div class="cof-img-wrapper">
            <img src="../multimedia/cof.jpeg" alt="Certificado COFEPRIS 1" class="cof-img">
            <img src="../multimedia/cof1.jpeg" alt="Certificado COFEPRIS 2" class="cof-img">
        </div>
    </div>
</div>


<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <!-- Información de la empresa -->
            <div class="col-md-4">
                <img src="../multimedia/logo3.png" alt="Zoilife Logo" class="footer-logo">
                <h5>ZOI LIFE® <br>PRODUCTOS POLITÉCNICOS</h5>
                <p>
                    Tu salud no es un lujo, es la base de tu vida. En Zoi Life, creemos en la capacidad del
                    cuerpo para regenerarse cuando recibe los elementos correctos. No creamos productos,
                    creamos soluciones naturales respaldadas por la ciencia.
                    <br>
                    Cada fórmula que ofrecemos es el resultado de años de investigación de científicos
                    egresados del Instituto Politécnico Nacional, combinando lo mejor de la naturaleza con
                    el conocimiento más avanzado.
                    <br>
                    -Sin químicos. Sin atajos. Solo ingredientes puros que trabajan en armonía con tu
                    cuerpo.
                    <br>
                    -Porque la salud no se trata solo de vivir más, sino de vivir mejor.
                    <br>
                    - Zoi Life es más que una marca, es un movimiento por el bienestar real.
                </p>

                <!-- Métodos de pago -->
                <div class="payment-icons">
                    <img src="../multimedia/visa.png" alt="Visa">
                    <img src="../multimedia/ae.png" alt="American Express">
                    <img src="../multimedia/ap.png" alt="Apple Pay">
                    <img src="../multimedia/paypal.png" alt="PayPal">
                </div>

                <p class="copyright">2025 Zoi Life Productos Politécnicos®</p>
            </div>

            <!-- Suscripción -->
            <div class="col-md-4">
                <h5>ÚNETE A NUESTRA FAMILIA Y RECIBE <br>20% EN TU PRIMERA COMPRA.</h5>
                <form class="subscribe-form">
                    <input type="email" placeholder="CORREO ELECTRÓNICO" required>
                    <button type="submit">SUSCRIBIRME</button>
                </form>
            </div>

            <!-- Enlaces útiles -->
            <div class="col-md-4">
                <div class="footer-links">
                    <div>
                        <h6>REDES</h6>
                        <a href="#">Instagram</a><br>
                        <a href="#">Facebook</a><br>
                        <a href="#">Blog</a>
                    </div>

                    <div>
                        <h6>INFO</h6>
                        <a href="#" onclick="openModal('modalCofepris')">Certificado COFEPRIS</a><br>                        <a href="#">Contacto</a><br>
                        <a href="#">Preguntas Frecuentes</a><br>
                        <a href="#">Políticas de Privacidad</a><br>
                        <a href="#">Envíos y Devoluciones</a><br>
                        <a href="#">Términos y Condiciones</a>
                    </div>

                    <div>
                        <h6>CUENTA</h6>
                        <a href="#">Mi Cuenta</a><br>
                        <a href="#">Mis Pedidos</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<script>
    function openModal(modalId) {
        let modal = document.getElementById(modalId);
        modal.classList.add("show");
    }

    function closeModal(modalId) {
        let modal = document.getElementById(modalId);
        modal.classList.remove("show");
    }

</script>