<?php
include '../../back/db/connection.php';

// Verificar si se recibe un ID de cliente válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Cliente no válido.";
    header("Location: ../front/ventas/clientes.php"); // Redirige al módulo correcto
    exit();
}

$id_cliente = $_GET['id'];

// Obtener datos del cliente
$queryCliente = "SELECT CONCAT(nombre, ' ', apellido_paterno, ' ', apellido_materno) AS nombre_completo FROM clientes WHERE id_cliente = :id_cliente";
$stmt = $conn->prepare($queryCliente);
$stmt->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);
$stmt->execute();
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

// Si el cliente no existe, redirige
if (!$cliente) {
    $_SESSION['error'] = "Cliente no encontrado.";
    header("Location: ../front/ventas/clientes.php");
    exit();
}

// Obtener direcciones activas del cliente
$queryDirecciones = "SELECT id_direccion, CONCAT(calle, ' ', numero_casa, ', ', ciudad, ', ', estado) AS direccion FROM direcciones WHERE id_cliente = :id_cliente AND estatus = 'activo'";
$stmt = $conn->prepare($queryDirecciones);
$stmt->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);
$stmt->execute();
$direcciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener productos disponibles (recetas activas)
$queryProductos = "SELECT id_receta, nombre_producto FROM recetas WHERE estatus = 'activo'";
$stmt = $conn->prepare($queryProductos);
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener regalos disponibles (activos)
$queryRegalos = "SELECT id_regalo, nombre FROM regalos WHERE estatus = 'activo'";
$stmt = $conn->prepare($queryRegalos);
$stmt->execute();
$regalos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>






<div class="container mt-4">
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Regresar al Dashboard</a>

    <h1 class="text-center">Nueva Cotización</h1>

    <form id="form-cotizacion" action="../../back/ventas/guardar_cotizacion.php" method="POST">
        <input type="hidden" name="id_cliente" value="<?= htmlspecialchars($id_cliente) ?>">
        <input type="hidden" id="productos-hidden" name="productos">
        <input type="hidden" id="regalos-hidden" name="regalos">

        <!-- Nombre del Cliente -->
        <div class="mb-3">
            <label class="form-label">Cliente</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($cliente['nombre_completo']) ?>" readonly>
        </div>

        <!-- Selección de Dirección -->
        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección de Envío</label>
            <select class="form-select" id="direccion" name="id_direccion" required>
                <option value="">Selecciona una dirección</option>
                <?php foreach ($direcciones as $direccion): ?>
                    <option value="<?= $direccion['id_direccion'] ?>"><?= htmlspecialchars($direccion['direccion']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Agregar Productos -->
        <div class="mb-3">
            <label class="form-label">Productos</label>
            <div class="input-group mb-2">
                <select class="form-select" id="producto">
                    <option value="">Selecciona un producto</option>
                    <?php foreach ($productos as $producto): ?>
                        <option value="<?= $producto['id_receta'] ?>"><?= htmlspecialchars($producto['nombre_producto']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" id="cantidad-producto" class="form-control" placeholder="Cantidad" min="1">
                <button type="button" id="agregar-producto" class="btn btn-success">+</button>
            </div>
            <ul id="lista-productos" class="list-group"></ul>
        </div>

        <!-- Agregar Regalos -->
        <div class="mb-3">
            <label class="form-label">Regalos</label>
            <div class="input-group mb-2">
                <select class="form-select" id="regalo">
                    <option value="">Selecciona un regalo</option>
                    <?php foreach ($regalos as $regalo): ?>
                        <option value="<?= $regalo['id_regalo'] ?>"><?= htmlspecialchars($regalo['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" id="cantidad-regalo" class="form-control" placeholder="Cantidad" min="1">
                <button type="button" id="agregar-regalo" class="btn btn-info">+</button>
            </div>
            <ul id="lista-regalos" class="list-group"></ul>
        </div>

        <!-- Tipo de Envío -->
        <div class="mb-3">
            <label class="form-label">Tipo de Envío</label>
            <div>
                <input type="radio" id="local" name="tipo_envio" value="local" checked>
                <label for="local">Local</label>
                <input type="radio" id="foraneo" name="tipo_envio" value="foraneo">
                <label for="foraneo">Foráneo</label>
            </div>
        </div>
        <!-- Fecha de Entrega -->
        <div class="mb-3">
            <label class="form-label">Fecha de Entrega</label>
            <input type="date" class="form-control" name="fecha_entrega" required>
        </div>

        <!-- Observaciones -->
        <div class="mb-3">
            <label for="observaciones" class="form-label">Observaciones</label>
            <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
        </div>

        <!-- Costo de Envío -->
        <div class="mb-3">
            <label class="form-label">Costo de Envío</label>
            <input type="number" class="form-control" id="costo_envio" name="costo_envio">
        </div>

        <!-- Costo Total -->
        <div class="mb-3">
            <label class="form-label">Costo de la Cotización</label>
            <input type="number" class="form-control" id="costo_total" name="costo_total">
        </div>

        <!-- Botón de Vista Previa -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalVistaPrevia">Vista Previa</button>

        <!-- Modal de Vista Previa -->
        <div class="modal fade" id="modalVistaPrevia" tabindex="-1" aria-labelledby="modalVistaPreviaLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalVistaPreviaLabel">Vista Previa de la Cotización</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Cliente:</strong> <span id="preview-cliente"></span></p>
                        <p><strong>Dirección de Envío:</strong> <span id="preview-direccion"></span></p>
                        <p><strong>Productos:</strong></p>
                        <ul id="preview-productos"></ul>
                        <p><strong>Regalos:</strong></p>
                        <ul id="preview-regalos"></ul>
                        <p><strong>Tipo de Envío:</strong> <span id="preview-envio"></span></p>
                        <p><strong>Fecha de Entrega:</strong> <span id="preview-fecha-entrega"></span></p>
                        <p><strong>Observaciones:</strong> <span id="preview-observaciones"></span></p>
                        <p><strong>Costo de Envío:</strong> <span id="preview-costo-envio"></span></p>
                        <p><strong>Costo Total:</strong> <span id="preview-costo-total"></span></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-success" id="guardarCotizacion">Guardar Cotización</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>






<script>
    document.addEventListener("DOMContentLoaded", function () {
        let productos = [];
        let regalos = [];

        // Función para actualizar los campos ocultos antes de enviar el formulario
        function actualizarCamposOcultos() {
            document.getElementById("productos-hidden").value = JSON.stringify(productos);
            document.getElementById("regalos-hidden").value = JSON.stringify(regalos);
        }

        // Evento para mostrar datos en la Vista Previa antes de abrir el modal
        document.getElementById("modalVistaPrevia").addEventListener("show.bs.modal", function () {
            document.getElementById("preview-cliente").textContent = "<?= htmlspecialchars($cliente['nombre_completo']) ?>";
            document.getElementById("preview-direccion").textContent = document.getElementById("direccion").selectedOptions[0].text;
            document.getElementById("preview-envio").textContent = document.querySelector('input[name="tipo_envio"]:checked').nextElementSibling.textContent;
            document.getElementById("preview-fecha-entrega").textContent = document.querySelector('input[name="fecha_entrega"]').value;

            document.getElementById("preview-observaciones").textContent = document.getElementById("observaciones").value;
            document.getElementById("preview-costo-envio").textContent = document.getElementById("costo_envio").value;
            document.getElementById("preview-costo-total").textContent = document.getElementById("costo_total").value;

            // Mostrar lista de productos
            let productosList = document.getElementById("preview-productos");
            productosList.innerHTML = "";
            productos.forEach((p) => {
                let item = document.createElement("li");
                item.textContent = `${p.nombre} (x${p.cantidad})`;
                productosList.appendChild(item);
            });

            // Mostrar lista de regalos
            let regalosList = document.getElementById("preview-regalos");
            regalosList.innerHTML = "";
            regalos.forEach((r) => {
                let item = document.createElement("li");
                item.textContent = `${r.nombre} (x${r.cantidad})`;
                regalosList.appendChild(item);
            });

            // Asegurar que los campos ocultos tengan los datos correctos antes de enviar el formulario
            actualizarCamposOcultos();
        });

        // Evento para agregar productos
        document.getElementById("agregar-producto").addEventListener("click", function () {
            let select = document.getElementById("producto");
            let cantidad = parseInt(document.getElementById("cantidad-producto").value, 10);
            let lista = document.getElementById("lista-productos");

            if (select.value && cantidad > 0) {
                let producto = {
                    id: select.value,
                    nombre: select.selectedOptions[0].text,
                    cantidad: cantidad
                };
                productos.push(producto);

                let item = document.createElement("li");
                item.className = "list-group-item d-flex justify-content-between align-items-center";
                item.innerHTML = `${producto.nombre} (x${producto.cantidad}) <button class='btn btn-danger btn-sm eliminar'>X</button>`;
                lista.appendChild(item);

                // Evento para eliminar producto
                item.querySelector(".eliminar").addEventListener("click", function () {
                    productos = productos.filter(p => p.id !== producto.id);
                    item.remove();
                });

                // Limpiar selección
                select.value = "";
                document.getElementById("cantidad-producto").value = "";
            }
        });

        // Evento para agregar regalos
        document.getElementById("agregar-regalo").addEventListener("click", function () {
            let select = document.getElementById("regalo");
            let cantidad = parseInt(document.getElementById("cantidad-regalo").value, 10);
            let lista = document.getElementById("lista-regalos");

            if (select.value && cantidad > 0) {
                let regalo = {
                    id: select.value,
                    nombre: select.selectedOptions[0].text,
                    cantidad: cantidad
                };
                regalos.push(regalo);

                let item = document.createElement("li");
                item.className = "list-group-item d-flex justify-content-between align-items-center";
                item.innerHTML = `${regalo.nombre} (x${regalo.cantidad}) <button class='btn btn-danger btn-sm eliminar'>X</button>`;
                lista.appendChild(item);

                // Evento para eliminar regalo
                item.querySelector(".eliminar").addEventListener("click", function () {
                    regalos = regalos.filter(r => r.id !== regalo.id);
                    item.remove();
                });

                // Limpiar selección
                select.value = "";
                document.getElementById("cantidad-regalo").value = "";
            }
        });

        // Evento para enviar formulario
        document.getElementById("guardarCotizacion").addEventListener("click", function () {
            // Asegurar que los datos de productos y regalos se pasen correctamente
            actualizarCamposOcultos();

            // Enviar el formulario
            document.getElementById("form-cotizacion").submit();
        });
    });

</script>
