<?php
include '../../back/db/connection.php';

// Obtener blogs activos
$stmt = $conn->prepare("SELECT * FROM blogs WHERE estatus = 'activo' ORDER BY id_blog DESC");
$stmt->execute();
$blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Sección de Introducción -->
<section class="about-hero">
    <div class="container-fluid d-flex align-items-center about-banner">
        <div class="text-section">
            <h1>HISTORIAS QUE INSPIRAN,<br><span>PRODUCTOS QUE TRANSFORMAN.</span></h1>
            <p>TIPS PRÁCTICOS PARA CUIDAR TU SALUD.</p>
        </div>
        <div class="image-section">
            <img src="../multimedia/ot/11.png" alt="Nosotros Zoi Life">
        </div>
    </div>
</section>

<!-- Sección de Artículos -->
<section class="articles-section">
    <div class="container">
        <div class="row">
            <?php foreach ($blogs as $blog): ?>
                <div class="col-md-4 mb-4">
                    <div class="article-card h-100 d-flex flex-column">
                        <img src="../blogs/img/<?= htmlspecialchars($blog['imagen']) ?>" alt="<?= htmlspecialchars($blog['titulo']) ?>" class="img-fluid mb-2">
                        <h3><?= htmlspecialchars($blog['titulo']) ?></h3>
                        <p><?= htmlspecialchars($blog['descripcion']) ?></p>
                        <a href="detalle_blog.php?id=<?= $blog['id_blog'] ?>" class="btn-read mt-auto">Leer Completo</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
