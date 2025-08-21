<?php
include '../../back/db/connection.php';

// Obtener productos_web con nombre del producto desde recetas
$stmt = $conn->prepare("
    SELECT pw.*, r.nombre_producto 
FROM productos_web pw
JOIN recetas r ON pw.id_receta = r.id_receta
WHERE pw.estatus = 'activo'
ORDER BY pw.id_producto_web DESC
");
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="dashboard.php" class="btn btn-secondary">← Regresar al Dashboard</a>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNuevoProducto">
            + Nuevo producto
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Precio</th>
                <th>Estrellas</th>
                <th>Mostrar en Nuevos</th>
                <th>Mostrar en Recomendados</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($productos as $p): ?>
                    <td><?= $p['id_producto_web'] ?></td>
                    <td><?= $p['nombre_producto'] ?></td>
                    <td><?= $p['categoria'] ?></td>
                    <td>$<?= number_format($p['precio'], 2) ?></td>
                    <td><?= str_repeat('⭐', $p['estrellas']) ?></td>
                    <td>
                        <form action="../../back/admin/toggle_nuevo.php" method="POST">
                            <input type="hidden" name="id_producto_web" value="<?= $p['id_producto_web'] ?>">
                            <input type="hidden" name="estado" value="<?= $p['en_nuevos_productos'] ? 0 : 1 ?>">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" onchange="this.form.submit()" <?= $p['en_nuevos_productos'] ? 'checked' : '' ?>>
                            </div>
                        </form>
                    </td>
                    <td>
                        <form action="../../back/admin/toggle_recomendado.php" method="POST">
                            <input type="hidden" name="id_producto_web" value="<?= $p['id_producto_web'] ?>">
                            <input type="hidden" name="estado" value="<?= $p['productos_r'] ? 0 : 1 ?>">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" onchange="this.form.submit()" <?= $p['productos_r'] ? 'checked' : '' ?>>
                            </div>
                        </form>

                    </td>

                    <td>
                        <!-- Botón que lanza el modal con ID único por producto -->
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalFotoPrincipal<?= $p['id_producto_web'] ?>">
                            Foto Principal
                        </button>

                        <!-- Botón que lanza el modal de fotos anexas -->
                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalFotosAnexas<?= $p['id_producto_web'] ?>">
                            Fotos Anexas
                        </button>


                        <button class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#modalDescripcion<?= $p['id_producto_web'] ?>">
                            Descripción
                        </button>

                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalContenido<?= $p['id_producto_web'] ?>">
                            Contenido
                        </button>

                        <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $p['id_producto_web'] ?>">
                            Editar
                        </button>

                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalEliminar<?= $p['id_producto_web'] ?>">
                            Eliminar
                        </button>

                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>



<?php foreach ($productos as $p): ?>





    <div class="modal fade" id="modalEliminar<?= $p['id_producto_web'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="../../back/admin/eliminar_producto_web.php" method="POST" class="modal-content">
                <input type="hidden" name="id_producto_web" value="<?= $p['id_producto_web'] ?>">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">¿Eliminar producto?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro que deseas eliminar <b><?= $p['nombre_producto'] ?></b> de la vista pública?</p>
                    <p class="text-muted small">Esto solo lo ocultará de la web, pero se conservará en la base de datos.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </form>
        </div>
    </div>






    <div class="modal fade" id="modalEditar<?= $p['id_producto_web'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="../../back/admin/editar_producto_web.php" method="POST" class="modal-content">
                <input type="hidden" name="id_producto_web" value="<?= $p['id_producto_web'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Producto: <?= $p['nombre_producto'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select class="form-select" name="categoria" required>
                            <option value="Sistema Celular" <?= $p['categoria'] == 'Sistema Celular' ? 'selected' : '' ?>>Sistema Celular</option>
                            <option value="Sistema Cardiovascular" <?= $p['categoria'] == 'Sistema Cardiovascular' ? 'selected' : '' ?>>Sistema Cardiovascular</option>
                            <option value="Sistema Digestivo" <?= $p['categoria'] == 'Sistema Digestivo' ? 'selected' : '' ?>>Sistema Digestivo</option>
                            <option value="Sistema Inmunológico" <?= $p['categoria'] == 'Sistema Inmunológico' ? 'selected' : '' ?>>Sistema Inmunológico</option>
                            <option value="Sistema Esquelético" <?= $p['categoria'] == 'Sistema Esquelético' ? 'selected' : '' ?>>Sistema Esquelético</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Precio</label>
                        <input type="number" step="0.01" name="precio" class="form-control" value="<?= $p['precio'] ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Estrellas (0-5)</label>
                        <input type="number" min="0" max="5" name="estrellas" class="form-control" value="<?= $p['estrellas'] ?>" required>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>



    <div class="modal fade" id="modalContenido<?= $p['id_producto_web'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form action="../../back/admin/guardar_contenido.php" method="POST" class="modal-content">
                <input type="hidden" name="id_producto_web" value="<?= $p['id_producto_web'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Contenido: <?= $p['nombre_producto'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Editor -->
                        <div class="col-md-6">
                            <label class="form-label">Contenido HTML</label>
                            <textarea class="form-control editor-contenido" name="contenido_<?= $p['id_producto_web'] ?>"><?= htmlspecialchars($p['contenido']) ?></textarea>
                        </div>

                        <!-- Vista previa -->
                        <div class="col-md-6">
                            <label class="form-label">Vista previa</label>
                            <div class="border rounded p-3" style="min-height:250px; background-color:#f9f9f9;" id="preview_contenido_<?= $p['id_producto_web'] ?>">
                                <?= $p['contenido'] ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar Contenido</button>
                </div>
            </form>
        </div>
    </div>






    <div class="modal fade" id="modalDescripcion<?= $p['id_producto_web'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form action="../../back/admin/guardar_descripcion.php" method="POST" class="modal-content">
                <input type="hidden" name="id_producto_web" value="<?= $p['id_producto_web'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Descripción: <?= $p['nombre_producto'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Editor -->
                        <div class="col-md-6">
                            <label class="form-label">Contenido HTML</label>
                            <textarea class="form-control editor-descripcion" name="descripcion_<?= $p['id_producto_web'] ?>"><?= htmlspecialchars($p['descripcion']) ?></textarea>
                        </div>

                        <!-- Vista previa -->
                        <div class="col-md-6">
                            <label class="form-label">Vista previa</label>
                            <div class="border rounded p-3" style="min-height:250px; background-color:#f9f9f9;" id="preview_descripcion_<?= $p['id_producto_web'] ?>">
                                <?= $p['descripcion'] ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar Descripción</button>
                </div>
            </form>
        </div>
    </div>









    <?php
// Obtener fotos anexas activas
    $stmt_fotos = $conn->prepare("SELECT * FROM productos_web_fotos WHERE id_producto_web = :id AND estatus = 'activo' ORDER BY orden ASC");
    $stmt_fotos->bindParam(':id', $p['id_producto_web']);
    $stmt_fotos->execute();
    $fotos = $stmt_fotos->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <!-- Modal de fotos anexas -->
    <div class="modal fade" id="modalFotosAnexas<?= $p['id_producto_web'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Fotos Anexas de <?= $p['nombre_producto'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <?php if (count($fotos) > 0): ?>
                            <?php foreach ($fotos as $foto): ?>
                                <div class="col-md-3 text-center mb-4">
                                    <div class="card shadow-sm">
                                        <img src="../../front/productosWeb/<?= $foto['url_foto'] ?>" class="card-img-top img-fluid" style="max-height: 150px; object-fit: contain;">
                                        <div class="card-body p-2">
                                            <form action="../../back/admin/eliminar_foto_anexa.php" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta foto?')">
                                                <input type="hidden" name="id_foto" value="<?= $foto['id_foto'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger w-100">🗑 Eliminar</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No hay fotos anexas registradas.</p>
                        <?php endif; ?>
                    </div>

                    <hr>
                    <!-- Este formulario SÍ es el de subida -->
                    <form action="../../back/admin/subir_foto_anexa.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id_producto_web" value="<?= $p['id_producto_web'] ?>">
                        <div class="mb-3">
                            <label class="form-label">Subir nueva foto anexa</label>
                            <input type="file" name="foto_anexa" accept="image/*" class="form-control" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Guardar Foto</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>





    <!-- Modal Foto Principal -->
    <div class="modal fade" id="modalFotoPrincipal<?= $p['id_producto_web'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="../../back/admin/subir_foto_principal.php" method="POST" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Foto Principal de <?= $p['nombre_producto'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">

                    <?php if (!empty($p['foto_principal'])): ?>
                        <img src="../../front/productosWeb/<?= $p['foto_principal'] ?>" class="img-fluid mb-3" style="max-height: 200px;">
                    <?php else: ?>
                        <p class="text-muted">Foto principal aún no guardada.</p>
                    <?php endif; ?>

                    <input type="hidden" name="id_producto_web" value="<?= $p['id_producto_web'] ?>">

                    <div class="mb-3">
                        <label for="foto" class="form-label">Subir nueva foto</label>
                        <input type="file" name="foto" accept="image/*" class="form-control" required>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>

<?php endforeach; ?>












<!-- Modal para nuevo producto -->
<div class="modal fade" id="modalNuevoProducto" tabindex="-1" aria-labelledby="modalNuevoProductoLabel" aria-hidden="true">
    <div class="modal-dialog">

        <form action="../../back/admin/insertar_producto_web.php" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNuevoProductoLabel">Nuevo producto web</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="id_receta" class="form-label">Producto (desde receta)</label>
                    <select name="id_receta" id="id_receta" class="form-select" required>
                        <option value="">Selecciona uno...</option>
                        <?php
                        $recetas = $conn->query("SELECT id_receta, nombre_producto FROM recetas WHERE estatus = 'activo' ORDER BY nombre_producto ASC");
                        foreach ($recetas as $r) {
                            echo "<option value='{$r['id_receta']}'>{$r['nombre_producto']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="categoria" class="form-label">Categoría</label>
                    <select class="form-select" name="categoria" required>
                        <option value="">Selecciona una categoría...</option>
                        <option value="Sistema Celular">Sistema Celular</option>
                        <option value="Sistema Cardiovascular">Sistema Cardiovascular</option>
                        <option value="Sistema Digestivo">Sistema Digestivo</option>
                        <option value="Sistema Inmunológico">Sistema Inmunológico</option>
                        <option value="Sistema Esquelético">Sistema Esquelético</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="precio" class="form-label">Precio</label>
                    <input type="number" step="0.01" class="form-control" name="precio" required>
                </div>
                <div class="mb-3">
                    <label for="estrellas" class="form-label">Estrellas (0-5)</label>
                    <input type="number" min="0" max="5" class="form-control" name="estrellas" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Guardar producto</button>
            </div>
        </form>
    </div>
</div>
