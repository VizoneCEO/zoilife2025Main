
<div class="container mt-4">
    <!-- Botón regresar -->
    <div class="mb-3">
        <a href="dashboard.php" class="btn btn-secondary">← Regresar al Dashboard</a>
    </div>

    <!-- Título -->
    <h1 class="text-center mb-4">Crear Nueva Receta</h1>

    <!-- Formulario -->
    <form action="../../back/produccion/process_nueva_receta.php" method="POST">
        <div class="mb-3">
            <label for="nombre_producto" class="form-label">Nombre del Producto Terminado</label>
            <input type="text" class="form-control" id="nombre_producto" name="nombre_producto" placeholder="Ejemplo: Ajo Negro 20 Caps" required>
        </div>

        <div class="mb-3">
            <label for="cantidad" class="form-label">Cantidad</label>
            <input type="number" class="form-control" id="cantidad" name="cantidad" placeholder="Cantidad por presentación" required>
        </div>

        <div class="mb-3">
            <label for="unidad_medida" class="form-label">Unidad de Medida</label>
            <select class="form-select" id="unidad_medida" name="unidad_medida" required>
                <option value="">Seleccione una unidad</option>
                <option value="kg">Kilogramos</option>
                <option value="gr">Gramos</option>
                <option value="pieza">Pieza</option>
                <option value="L">Litros</option>
                <option value="ml">Mililitros</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Guardar Receta</button>
    </form>
</div>

