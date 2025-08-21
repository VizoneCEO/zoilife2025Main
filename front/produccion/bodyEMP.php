<?php

include '../../back/db/connection.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        // Consultar los datos de la materia prima con PDO
        $query = "SELECT * FROM materia_prima WHERE id_materia_prima = ? AND estatus = 'activo'";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        $materiaPrima = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$materiaPrima) {
            header('Location: catalogo.php?error=Materia prima no encontrada.');
            exit();
        }
    } catch (PDOException $e) {
        header('Location: catalogo.php?error=Error al consultar la materia prima.');
        exit();
    }
} else {
    header('Location: catalogo.php?error=ID no válido.');
    exit();
}
?>

<style>
    .image-preview {
        width: 100%;
        height: 200px;
        background-color: #f0f0f0;
        border: 2px dashed #ccc;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #aaa;
        font-size: 16px;
        overflow: hidden;
    }
    .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>

<div class="container mt-4">
    <!-- Botón regresar -->
    <div class="mb-3">
        <a href="catalogo.php" class="btn btn-secondary">← Regresar al Catálogo</a>
    </div>

    <!-- Título -->
    <h1 class="mb-4">Editar Materia Prima</h1>

    <!-- Formulario -->
    <form action="../../back/produccion/update_materia_prima.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($materiaPrima['id_materia_prima']); ?>">

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre de la Materia Prima</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($materiaPrima['nombre']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="imagen" class="form-label">Actualizar Imagen (opcional)</label>
            <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*" onchange="previewImage(event)">
            <div class="image-preview mt-3" id="imagePreview">
                <img src="../../front/multimedia/materia_prima/<?php echo htmlspecialchars($materiaPrima['imagen']); ?>" alt="Imagen actual">
            </div>
        </div>

        <button type="submit" class="btn btn-success">Guardar Cambios</button>
    </form>
</div>

<script>
    // Mostrar vista previa de la imagen seleccionada
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = ''; // Limpiar el contenido previo

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                preview.appendChild(img);
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.textContent = 'Vista previa';
        }
    }
</script>
