<?php
include '../../back/db/connection.php';

// Obtener blogs activos
$stmt = $conn->prepare("SELECT * FROM blogs WHERE estatus = 'activo' ORDER BY id_blog DESC");
$stmt->execute();
$blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="dashboard.php" class="btn btn-secondary">← Regresar al Dashboard</a>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNuevoBlog">+ Nuevo Blog</button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Imagen</th>
                <th>Título</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($blogs as $b): ?>
                <tr>
                    <td><?= $b['id_blog'] ?></td>
                    <td><img src="../../front/blogs/img/<?= $b['imagen'] ?>" alt="Blog" width="80"></td>
                    <td><?= htmlspecialchars($b['titulo']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalContenido<?= $b['id_blog'] ?>">Contenido</button>
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $b['id_blog'] ?>">Editar</button>
                        <form action="../../back/admin/eliminar_blog.php" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este blog?')">
                            <input type="hidden" name="id_blog" value="<?= $b['id_blog'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Nuevo Blog -->
<div class="modal fade" id="modalNuevoBlog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="../../back/admin/insertar_blog.php" method="POST" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Blog</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="titulo" class="form-label">Título</label>
                    <input type="text" class="form-control" name="titulo" required>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción corta</label>
                    <textarea class="form-control" name="descripcion" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="imagen" class="form-label">Imagen principal</label>
                    <input type="file" class="form-control" name="imagen" accept="image/*" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Crear Blog</button>
            </div>
        </form>
    </div>
</div>

<?php foreach ($blogs as $b): ?>
    <!-- Modal Editar Blog -->
    <div class="modal fade" id="modalEditar<?= $b['id_blog'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="../../back/admin/editar_blog.php" method="POST" enctype="multipart/form-data" class="modal-content">
                <input type="hidden" name="id_blog" value="<?= $b['id_blog'] ?>">
                <input type="hidden" name="imagen_actual" value="<?= $b['imagen'] ?>"> <!-- NECESARIO -->
                <div class="modal-header">
                    <h5 class="modal-title">Editar Blog</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" class="form-control" name="titulo" value="<?= htmlspecialchars($b['titulo']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="3" required><?= htmlspecialchars($b['descripcion']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reemplazar imagen (opcional)</label>
                        <input type="file" class="form-control" name="imagen" accept="image/*">
                        <?php if (!empty($b['imagen'])): ?>
                            <div class="mt-2">
                                <img src="../../front/blogs/img/<?= $b['imagen'] ?>" alt="Imagen actual" class="img-fluid rounded shadow-sm" style="max-height: 150px;">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>


    <!-- Modal Contenido Completo -->
    <div class="modal fade" id="modalContenido<?= $b['id_blog'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <form action="../../back/admin/guardar_contenido_blog.php" method="POST" class="modal-content">
                <input type="hidden" name="id_blog" value="<?= $b['id_blog'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Contenido Completo: <?= $b['titulo'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Editor -->
                        <div class="col-md-6">
                            <label class="form-label">Contenido HTML</label>
                            <textarea name="contenido_<?= $b['id_blog'] ?>" class="form-control editor-contenido" rows="20"><?= htmlspecialchars($b['contenido']) ?></textarea>
                        </div>

                        <!-- Vista previa -->
                        <div class="col-md-6">
                            <label class="form-label">Vista previa</label>
                            <div class="border rounded p-3" style="min-height: 400px; background-color: #f9f9f9;" id="preview_contenido_<?= $b['id_blog'] ?>">
                                <?= $b['contenido'] ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Guardar Contenido</button>
                </div>
            </form>
        </div>
    </div>

<?php endforeach; ?>
