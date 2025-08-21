<div class="container mt-4">
    <h1 class="text-center mb-4">Registrar Nuevo Regalo</h1>

    <!-- Mensajes de error o éxito -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form action="../../back/produccion/nuevo_regalo.php" method="POST">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Regalo</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label for="costo_estimado" class="form-label">Costo Estimado ($)</label>
            <input type="number" class="form-control" id="costo_estimado" name="costo_estimado" step="0.01" min="0" required>
        </div>

        <div class="mb-3">
            <label for="estatus" class="form-label">Estatus</label>
            <select class="form-select" id="estatus" name="estatus" required>
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>
        </div>

        <div class="d-flex justify-content-between">
            <a href="catalogo_regalos.php" class="btn btn-secondary">← Regresar</a>
            <button type="submit" class="btn btn-success">Registrar Regalo</button>
        </div>
    </form>
</div>
