<?php
include '../../back/db/connection.php';

// Validar que el ID del producto (id_receta) es un número entero
$id_receta = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_receta === 0) {
    echo "<p class='container my-5'>Error: Producto no especificado.</p>";
    exit;
}

// Consulta principal para obtener los detalles del producto
$stmt = $conn->prepare(
    "SELECT 
        pw.id_receta, 
        pw.foto_principal, 
        pw.categoria, 
        pw.precio, 
        pw.descripcion, 
        r.nombre_producto 
     FROM productos_web pw
     JOIN recetas r ON pw.id_receta = r.id_receta
     WHERE pw.id_receta = ?"
);
$stmt->execute([$id_receta]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no se encuentra el producto, salir
if (!$producto) {
    echo "<p class='container my-5'>Producto no encontrado.</p>";
    exit;
}

// --- CÓDIGO PARA PRODUCTOS RELACIONADOS ---
$productos_relacionados = [];
if (!empty($producto['categoria'])) {
    $categoria_actual = $producto['categoria'];

    $stmt_relacionados = $conn->prepare(
        "SELECT 
            pw.id_receta, 
            r.nombre_producto, 
            pw.foto_principal, 
            pw.precio 
         FROM productos_web pw
         JOIN recetas r ON pw.id_receta = r.id_receta
         WHERE pw.categoria = ? AND pw.id_receta != ? AND pw.estatus = 'activo'
         ORDER BY RAND() 
         LIMIT 4"
    );
    $stmt_relacionados->execute([$categoria_actual, $id_receta]);
    $productos_relacionados = $stmt_relacionados->fetchAll(PDO::FETCH_ASSOC);
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .product-card { border: 1px solid #e1e1e1; border-radius: 8px; transition: all 0.3s ease; overflow: hidden; text-align: center; }
    .product-card:hover { transform: translateY(-5px); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    .product-card img { width: 100%; height: 200px; object-fit: cover; }
    .product-card-body { padding: 15px; }
    .product-card-title { font-size: 1rem; font-weight: 600; height: 40px; }
    .product-card-price { font-size: 1.2rem; font-weight: 700; color: #28a745; }
    .product-card .btn { width: 100%; }
</style>

<div class="container my-5">
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <img id="main-product-image" src="../productosWeb/<?php echo htmlspecialchars($producto['foto_principal']); ?>" class="img-fluid rounded border" alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>">
            </div>
        </div>

        <div class="col-md-6">
            <h1 class="display-5"><?php echo htmlspecialchars($producto['nombre_producto']); ?></h1>
            <p class="text-muted">Categoría: <?php echo htmlspecialchars($producto['categoria']); ?></p>
            <p class="h3 fw-bold text-success mb-4">$<?php echo number_format($producto['precio'], 2); ?></p>

            <div class="mb-4">
                <?php echo $producto['descripcion']; ?>
            </div>

            <form action="agregar_al_carrito.php" method="post">
                <input type="hidden" name="id_producto" value="<?php echo $producto['id_receta']; ?>">
                <div class="d-flex align-items-center mb-3">
                    <label for="cantidad" class="form-label me-3">Cantidad:</label>
                    <input type="number" name="cantidad" id="cantidad" class="form-control" value="1" min="1" style="width: 100px;">
                </div>
                <button type="submit" class="btn btn-success btn-lg w-100">
                    <i class="fas fa-shopping-cart me-2"></i> Añadir al carrito
                </button>
            </form>
        </div>
    </div>

    <?php if (!empty($productos_relacionados)): ?>
        <hr class="my-5">
        <div class="row mt-5">
            <div class="col-12">
                <h2 class="text-center mb-4">Productos Relacionados</h2>
            </div>

            <?php foreach ($productos_relacionados as $relacionado): ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card product-card h-100">
                        <a href="detalleProducto.php?id=<?php echo $relacionado['id_receta']; ?>">
                            <img src="../productosWeb/<?php echo htmlspecialchars($relacionado['foto_principal']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($relacionado['nombre_producto']); ?>">
                        </a>
                        <div class="card-body product-card-body d-flex flex-column">
                            <h5 class="card-title product-card-title">
                                <a href="detalleProducto.php?id=<?php echo $relacionado['id_receta']; ?>" class="text-dark text-decoration-none">
                                    <?php echo htmlspecialchars($relacionado['nombre_producto']); ?>
                                </a>
                            </h5>
                            <p class="card-text product-card-price mt-auto">$<?php echo number_format($relacionado['precio'], 2); ?></p>
                            <a href="detalleProducto.php?id=<?php echo $relacionado['id_receta']; ?>" class="btn btn-outline-success mt-2">Ver Producto</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    <?php endif; ?>
</div>