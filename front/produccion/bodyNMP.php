<div class="container mt-3">
    <a href="catalogo_materia_prima.php" class="btn btn-success">
        ← Regresar a Catalogo de Materia Prima
    </a>
</div>


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

    <!-- Título -->
    <h1 class="mb-4">Nueva Materia Prima</h1>

    <!-- Formulario -->
    <!-- Formulario -->
    <form action="../../back/produccion/save_materia_prima.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre de la Materia Prima</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>



        <div class="mb-3">
            <label for="imagen" class="form-label">Imagen de la Materia Prima</label>
            <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*" required onchange="previewImage(event)">
            <div class="image-preview mt-3" id="imagePreview">Vista previa</div>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
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


