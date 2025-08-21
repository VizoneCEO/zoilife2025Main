<?php
include '../../back/db/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p class='text-danger text-center mt-5'>Blog no válido.</p>";
    exit();
}

$id_blog = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM blogs WHERE id_blog = :id_blog AND estatus = 'activo'");
$stmt->bindParam(':id_blog', $id_blog, PDO::PARAM_INT);
$stmt->execute();
$blog = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$blog) {
    echo "<p class='text-danger text-center mt-5'>El artículo no fue encontrado.</p>";
    exit();
}
?>

    <style>
        .blog-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease;
        }

        .blog-title {
            font-size: 2rem;
            font-weight: bold;
            color: #69bd45; /* verde Zoilife */
            margin-bottom: 30px;
        }

        .blog-image {
            max-height: 400px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .blog-content {
            font-size: 1.1rem;
            line-height: 1.7;
            color: #444;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(20px);}
            to {opacity: 1; transform: translateY(0);}
        }

        .btn-volver {
            margin-top: 40px;
        }
    </style>

<div class="container blog-container">
    <h1 class="blog-title"><?= htmlspecialchars($blog['titulo']) ?></h1>

    <?php if (!empty($blog['imagen'])): ?>
        <img src="../blogs/img/<?= htmlspecialchars($blog['imagen']) ?>" class="img-fluid blog-image" alt="<?= htmlspecialchars($blog['titulo']) ?>">
    <?php endif; ?>

    <div class="blog-content">
        <?= $blog['contenido'] ?>
    </div>

    <a href="blog.php" class="btn btn-outline-primary btn-volver">← Volver al Blog</a>
</div>

