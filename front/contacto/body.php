<section class="contact-section">
    <!-- Imagen de fondo con texto -->
    <div class="contact-banner">
        <h1>CONTACTO</h1>
    </div>

    <!-- Información de contacto -->
    <div class="container text-center my-4">
        <p class="contact-number">01 800 3783 3399</p>
        <h2 class="contact-title">¿CÓMO PODEMOS AYUDARTE?</h2>
        <p class="contact-subtitle">ATENCIÓN INMEDIATA</p>
    </div>

    <!-- Formulario de contacto -->
    <div class="container">
        <form action="../../back/contacto/enviar_contacto.php" method="post" class="contact-form">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <input type="text" name="nombre" placeholder="NOMBRE" required>
                </div>
                <div class="col-md-6 mb-3">
                    <input type="text" name="apellido" placeholder="APELLIDO" required>
                </div>
                <div class="col-md-6 mb-3">
                    <input type="email" name="correo" placeholder="CORREO ELECTRÓNICO" required>
                </div>
                <div class="col-md-6 mb-3">
                    <input type="tel" name="telefono" placeholder="TELÉFONO" required>
                </div>
                <div class="col-12 mb-3">
                    <input type="text" name="ciudad" placeholder="CIUDAD">
                </div>
                <div class="col-12 mb-3">
                    <textarea name="mensaje" rows="5" placeholder="MENSAJE" required></textarea>
                </div>
                <div class="col-12 text-center">
                    <button type="submit" class="btn-send">ENVIAR MENSAJE</button>
                </div>
            </div>
        </form>
    </div>
</section>
