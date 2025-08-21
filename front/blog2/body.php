<section class="faq-section">
    <!-- Imagen de fondo con título -->
    <div class="faq-banner">
        <h1>PREGUNTAS FRECUENTES</h1>
    </div>

    <!-- Lista de preguntas -->
    <div class="container my-5">
        <div class="accordion" id="faqAccordion">

            <?php for ($i = 1; $i <= 8; $i++): ?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading<?= $i ?>">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $i ?>" aria-expanded="false" aria-controls="collapse<?= $i ?>">
                            ¿SÓLO SON PARA MAYORES DE EDAD LOS SUPLEMENTOS?
                        </button>
                    </h2>
                    <div id="collapse<?= $i ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $i ?>" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Todos nuestros productos son de venta libre y aptos para adultos. Consulta a tu médico antes de consumir cualquier suplemento.
                        </div>
                    </div>
                </div>
            <?php endfor; ?>

        </div>
    </div>
</section>
