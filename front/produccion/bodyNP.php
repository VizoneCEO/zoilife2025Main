
    <style>
        .form-container {
            max-width: 600px;
            margin: 50px auto;
        }
    </style>


<div class="container form-container">
    <a href="../produccion/dashboard.php" class="btn btn-secondary mb-3">← Regresar al Dashboard</a>
    <h1 class="mb-4 text-center">Nuevo Proveedor</h1>

    <form action="../../back/produccion/process_nuevo_proveedor.php" method="POST">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Proveedor</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="mb-3">
            <label for="contacto" class="form-label">Nombre del Contacto</label>
            <input type="text" class="form-control" id="contacto" name="contacto" required>
        </div>
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" class="form-control" id="telefono" name="telefono" required>
        </div>
        <div class="mb-3">
            <label for="correo" class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" id="correo" name="correo" required>
        </div>
        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <textarea class="form-control" id="direccion" name="direccion" required></textarea>
        </div>
        <button type="submit" class="btn btn-success">Registrar Proveedor</button>
    </form>

</div>



