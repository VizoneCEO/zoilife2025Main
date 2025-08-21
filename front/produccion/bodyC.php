<div class="container mt-3">
    <a href="catalogo_materia_prima.php" class="btn btn-success">
        ← Regresar a Catálogo de Materia Prima
    </a>
</div>

<style>
    .card-image {
        width: 100%;
        height: 120px; /* Tamaño homogéneo para las imágenes */
        object-fit: cover;
    }
</style>

<div class="container mt-4">
    <!-- Título -->
    <h1 class="mb-4 text-center">Catálogo de Materia Prima</h1>

    <!-- Contenedor de tarjetas -->
    <div class="row g-4">
        <?php
        include '../../back/db/connection.php';

        try {
            // Consulta con PDO
            $query = "SELECT id_materia_prima, nombre, imagen FROM materia_prima WHERE estatus = 'activo'";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($result) > 0) {
                foreach ($result as $row) {
                    $id = $row['id_materia_prima'];
                    $nombre = $row['nombre'];
                    $imagen = $row['imagen'];
                    include 'views/materia_prima_card.php';
                }
            } else {
                echo '<div class="col-12">
                        <p class="text-center">No hay materias primas registradas.</p>
                      </div>';
            }
        } catch (PDOException $e) {
            echo '<div class="col-12">
                    <p class="text-center text-danger">Error al cargar las materias primas.</p>
                  </div>';
        }
        ?>
    </div>
</div>

<!-- Modal de confirmación -->
<div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEliminarLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar <strong id="nombreProducto"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmarEliminar" class="btn btn-danger">Eliminar</a>
            </div>
        </div>
    </div>
</div>

<script>
    const modalEliminar = document.getElementById('modalEliminar');
    const nombreProducto = document.getElementById('nombreProducto');
    const confirmarEliminar = document.getElementById('confirmarEliminar');

    modalEliminar.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const nombre = button.getAttribute('data-nombre');

        nombreProducto.textContent = nombre;
        confirmarEliminar.href = `../../back/produccion/eliminar_materia_prima.php?id=${id}`;
    });
</script>
