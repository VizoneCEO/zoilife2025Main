<div class="col-12 col-sm-6 col-md-4 col-lg-3">
    <div class="card h-100">
        <img src="../../front/multimedia/materia_prima/<?php echo htmlspecialchars($imagen); ?>"
             class="card-img-top card-image"
             alt="Imagen de <?php echo htmlspecialchars($nombre); ?>">
        <div class="card-body text-center">
            <h5 class="card-title"><?php echo htmlspecialchars($nombre); ?></h5>
            <div class="d-flex justify-content-center gap-2">
                <a href="editar_materia_prima.php?id=<?php echo $id; ?>" class="btn btn-primary btn-sm">Editar</a>
                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalEliminar" data-id="<?php echo $id; ?>" data-nombre="<?php echo htmlspecialchars($nombre); ?>">Eliminar</button>
            </div>
        </div>
    </div>
</div>
