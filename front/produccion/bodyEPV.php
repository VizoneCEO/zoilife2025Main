<?php
include '../../back/db/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Cotización no válida.");
}

$id_cotizacion = $_GET['id'];

$sqlCot = "SELECT c.*, cl.nombre, cl.apellido_paterno, cl.apellido_materno, c.fecha_entrega  
           FROM cotizaciones c 
           JOIN clientes cl ON c.id_cliente = cl.id_cliente 
           WHERE c.id_cotizacion = ?";
$stmt = $conn->prepare($sqlCot);
$stmt->execute([$id_cotizacion]);
$cotizacion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cotizacion) {
    die("Cotización no encontrada.");
}

$id_cliente = $cotizacion['id_cliente'];
$cliente_nombre = $cotizacion['nombre'] . ' ' . $cotizacion['apellido_paterno'] . ' ' . $cotizacion['apellido_materno'];

$stmt = $conn->prepare("SELECT id_direccion, CONCAT(calle, ' ', numero_casa, ', ', ciudad, ', ', estado) AS direccion 
                        FROM direcciones 
                        WHERE id_cliente = ? AND estatus = 'activo'");
$stmt->execute([$id_cliente]);
$direcciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

$productos = $conn->query("SELECT id_receta, nombre_producto FROM recetas WHERE estatus = 'activo'")->fetchAll(PDO::FETCH_ASSOC);
$regalos = $conn->query("SELECT id_regalo, nombre FROM regalos WHERE estatus = 'activo'")->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT cp.id_producto AS id, r.nombre_producto AS nombre, cp.cantidad
                        FROM cotizaciones_productos cp
                        JOIN recetas r ON cp.id_producto = r.id_receta
                        WHERE cp.id_cotizacion = ?");
$stmt->execute([$id_cotizacion]);
$productos_cot = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT cr.id_regalo AS id, r.nombre, cr.cantidad
                        FROM cotizaciones_regalos cr
                        JOIN regalos r ON cr.id_regalo = r.id_regalo
                        WHERE cr.id_cotizacion = ?");
$stmt->execute([$id_cotizacion]);
$regalos_cot = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Regresar al Dashboard</a>
    <h1 class="text-center">Editar Cotización</h1>

    <form id="form-cotizacion" action="../../back/produccion/modificar_cotizacion.php" method="POST">
        <input type="hidden" name="id_cotizacion" value="<?= $id_cotizacion ?>">
        <input type="hidden" id="productos-hidden" name="productos">
        <input type="hidden" id="regalos-hidden" name="regalos">

        <div class="mb-3">
            <label class="form-label">Cliente</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($cliente_nombre) ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Dirección de Envío</label>
            <select class="form-select" id="direccion" name="id_direccion" required>
                <option value="">Selecciona una dirección</option>
                <?php foreach ($direcciones as $dir): ?>
                    <option value="<?= $dir['id_direccion'] ?>" <?= $dir['id_direccion'] == $cotizacion['id_direccion'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($dir['direccion']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Productos</label>
            <div class="input-group mb-2">
                <select class="form-select" id="producto">
                    <option value="">Selecciona un producto</option>
                    <?php foreach ($productos as $prod): ?>
                        <option value="<?= $prod['id_receta'] ?>"><?= htmlspecialchars($prod['nombre_producto']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" id="cantidad-producto" class="form-control" placeholder="Cantidad" min="1">
                <button type="button" id="agregar-producto" class="btn btn-success">+</button>
            </div>
            <ul id="lista-productos" class="list-group"></ul>
        </div>

        <div class="mb-3">
            <label class="form-label">Regalos</label>
            <div class="input-group mb-2">
                <select class="form-select" id="regalo">
                    <option value="">Selecciona un regalo</option>
                    <?php foreach ($regalos as $reg): ?>
                        <option value="<?= $reg['id_regalo'] ?>"><?= htmlspecialchars($reg['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" id="cantidad-regalo" class="form-control" placeholder="Cantidad" min="1">
                <button type="button" id="agregar-regalo" class="btn btn-info">+</button>
            </div>
            <ul id="lista-regalos" class="list-group"></ul>
        </div>

        <div class="mb-3">
            <label class="form-label">Tipo de Envío</label><br>
            <input type="radio" name="tipo_envio" value="local" <?= $cotizacion['tipo_envio'] == 'local' ? 'checked' : '' ?>> Local
            <input type="radio" name="tipo_envio" value="foraneo" <?= $cotizacion['tipo_envio'] == 'foraneo' ? 'checked' : '' ?>> Foráneo
        </div>
        <div class="mb-3">
            <label class="form-label">Fecha de Entrega</label>
            <input type="date" class="form-control" name="fecha_entrega" value="<?= date('Y-m-d', strtotime($cotizacion['fecha_entrega'] ?? date('Y-m-d'))) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Observaciones</label>
            <textarea name="observaciones" class="form-control" rows="3"><?= htmlspecialchars($cotizacion['observaciones']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Costo de Envío</label>
            <input type="number" class="form-control" name="costo_envio" value="<?= $cotizacion['costo_envio'] ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Costo Total</label>
            <input type="number" class="form-control" name="costo_total" value="<?= $cotizacion['total'] ?>">
        </div>

        <button type="submit" class="btn btn-primary">Actualizar Cotización</button>
    </form>
</div>

<script>
    let productos = <?= json_encode($productos_cot) ?>;
    let regalos = <?= json_encode($regalos_cot) ?>;

    function actualizarOcultos() {
        document.getElementById("productos-hidden").value = JSON.stringify(productos);
        document.getElementById("regalos-hidden").value = JSON.stringify(regalos);
    }

    function crearElemento(item, tipo) {
        const li = document.createElement("li");
        li.className = "list-group-item d-flex justify-content-between align-items-center";
        li.innerHTML = `${item.nombre} (x${item.cantidad}) <button class="btn btn-danger btn-sm eliminar">X</button>`;
        li.querySelector(".eliminar").addEventListener("click", function () {
            if (tipo === "producto") {
                productos = productos.filter(p => p.id != item.id);
                document.getElementById("lista-productos").removeChild(li);
            } else {
                regalos = regalos.filter(r => r.id != item.id);
                document.getElementById("lista-regalos").removeChild(li);
            }
            actualizarOcultos();
        });
        return li;
    }

    productos.forEach(p => {
        document.getElementById("lista-productos").appendChild(crearElemento(p, "producto"));
    });

    regalos.forEach(r => {
        document.getElementById("lista-regalos").appendChild(crearElemento(r, "regalo"));
    });

    document.getElementById("agregar-producto").addEventListener("click", function () {
        const select = document.getElementById("producto");
        const cantidad = parseInt(document.getElementById("cantidad-producto").value, 10);
        if (select.value && cantidad > 0) {
            const item = {
                id: select.value,
                nombre: select.selectedOptions[0].text,
                cantidad: cantidad
            };
            productos.push(item);
            document.getElementById("lista-productos").appendChild(crearElemento(item, "producto"));
            select.value = "";
            document.getElementById("cantidad-producto").value = "";
            actualizarOcultos();
        }
    });

    document.getElementById("agregar-regalo").addEventListener("click", function () {
        const select = document.getElementById("regalo");
        const cantidad = parseInt(document.getElementById("cantidad-regalo").value, 10);
        if (select.value && cantidad > 0) {
            const item = {
                id: select.value,
                nombre: select.selectedOptions[0].text,
                cantidad: cantidad
            };
            regalos.push(item);
            document.getElementById("lista-regalos").appendChild(crearElemento(item, "regalo"));
            select.value = "";
            document.getElementById("cantidad-regalo").value = "";
            actualizarOcultos();
        }
    });

    document.getElementById("form-cotizacion").addEventListener("submit", function () {
        actualizarOcultos();
    });
</script>
