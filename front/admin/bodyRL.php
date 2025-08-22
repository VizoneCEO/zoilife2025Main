<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php
include '../../back/db/connection.php';

// --- SECCIÓN DE FILTROS ---
// 1. Obtener valores del formulario (si existen)
// Usamos el operador de fusión de null (??) para dar un valor predeterminado
$filtro_estatus = $_GET['estatus'] ?? 'en curso';
$filtro_fecha = $_GET['fecha'] ?? date('Y-m-d');

// Array con los posibles estatus para el dropdown
$posibles_estatus = ['pendiente', 'en curso', 'entregado', 'cancelado'];

// --- FIN SECCIÓN DE FILTROS ---

// Formatear la fecha para mostrarla en el título
$fechaFormateada = date('d/m/Y', strtotime($filtro_fecha));

// 2. Modificar las consultas para usar los filtros
$sql_where = "WHERE c.estatus = :estatus AND c.fecha_entrega = :fecha";

// Obtener pedidos asignados
$stmt = $conn->prepare("
    SELECT
        ae.id_usuario,
        u.nombre AS repartidor,
        COUNT(*) AS total_entregas,
        SUM(c.total) AS monto_total
    FROM asignaciones_entrega ae
    JOIN usuarios u ON ae.id_usuario = u.id_usuario
    JOIN cotizaciones c ON ae.id_cotizacion = c.id_cotizacion
    {$sql_where}
    GROUP BY ae.id_usuario
    ORDER BY u.nombre
");
$stmt->execute([':estatus' => $filtro_estatus, ':fecha' => $filtro_fecha]);
$resumen = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener total entregas, productos y total $
$totalEntregas = 0;
$totalMonto = 0;
$totalProductos = 0;
foreach ($resumen as $r) {
    $totalEntregas += $r['total_entregas'];
    $totalMonto += $r['monto_total'];
}

// Para total productos
$stmtProd = $conn->prepare("
    SELECT SUM(cantidad) AS total_productos
    FROM cotizaciones_productos cp
    JOIN cotizaciones c ON cp.id_cotizacion = c.id_cotizacion
    {$sql_where}
");
$stmtProd->execute([':estatus' => $filtro_estatus, ':fecha' => $filtro_fecha]);
$totalProductos = $stmtProd->fetchColumn();


// Total de regalos
$stmtRegalos = $conn->prepare("
    SELECT SUM(cantidad) AS total_regalos
    FROM cotizaciones_regalos cr
    JOIN cotizaciones c ON cr.id_cotizacion = c.id_cotizacion
    {$sql_where}
");
$stmtRegalos->execute([':estatus' => $filtro_estatus, ':fecha' => $filtro_fecha]);
$totalRegalos = $stmtRegalos->fetchColumn();

?>

<style>
    .card-header {
        font-size: 18px;
    }
    .collapse p, .collapse li {
        font-size: 15px;
    }
</style>
<div class="mb-4">
    <a href="dashboard.php" class="btn btn-outline-secondary">
        ← Regresar al Dashboard
    </a>
</div>

<div class="container my-5">
    <h2 class="text-center text-success mb-4">📦 Reporte de Logística - <?= $fechaFormateada ?> (<?= ucfirst($filtro_estatus) ?>)</h2>

    <div class="card shadow-sm mb-4">
        <div class="card-body bg-light">
            <form method="GET" action="" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="fecha" class="form-label fw-bold">Fecha de Entrega</label>
                    <input type="date" class="form-control" id="fecha" name="fecha" value="<?= htmlspecialchars($filtro_fecha) ?>">
                </div>
                <div class="col-md-5">
                    <label for="estatus" class="form-label fw-bold">Estatus del Pedido</label>
                    <select class="form-select" id="estatus" name="estatus">
                        <?php foreach ($posibles_estatus as $estatus_opcion): ?>
                            <option value="<?= $estatus_opcion ?>" <?= ($filtro_estatus == $estatus_opcion) ? 'selected' : '' ?>>
                                <?= ucfirst($estatus_opcion) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success w-100">Filtrar</button>
                </div>
            </form>
        </div>
    </div>


    <div class="row text-center mb-4">
        <div class="col-md-3">
            <div class="p-4 bg-light rounded shadow-sm">
                <h5 class="text-muted">Total de entregas</h5>
                <h3 class="text-success fw-bold"><?= $totalEntregas ?? 0 ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-4 bg-light rounded shadow-sm">
                <h5 class="text-muted">Total productos</h5>
                <h3 class="text-success fw-bold"><?= number_format($totalProductos ?? 0) ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-4 bg-light rounded shadow-sm">
                <h5 class="text-muted">Total regalos</h5>
                <h3 class="text-success fw-bold"><?= number_format($totalRegalos ?? 0) ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="p-4 bg-light rounded shadow-sm">
                <h5 class="text-muted">Total $</h5>
                <h3 class="text-success fw-bold">$<?= number_format($totalMonto ?? 0, 2) ?></h3>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header bg-dark text-white fw-bold">
            Totales por Producto (Asignados)
        </div>
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                <tr>
                    <th>Producto</th>
                    <th class="text-end">Cantidad Total</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $stmtTotalProd = $conn->prepare("
                    SELECT r.nombre_producto, SUM(cp.cantidad) AS total
                    FROM cotizaciones_productos cp
                    JOIN cotizaciones c ON cp.id_cotizacion = c.id_cotizacion
                    JOIN recetas r ON cp.id_producto = r.id_receta
                    {$sql_where}
                    GROUP BY r.nombre_producto
                    ORDER BY total DESC
                ");
                $stmtTotalProd->execute([':estatus' => $filtro_estatus, ':fecha' => $filtro_fecha]);
                $productosTotales = $stmtTotalProd->fetchAll(PDO::FETCH_ASSOC);

                foreach ($productosTotales as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['nombre_producto']) ?></td>
                        <td class="text-end fw-bold"><?= number_format($p['total']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white fw-bold">
            Totales por Regalo (Asignados)
        </div>
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                <tr>
                    <th>Regalo</th>
                    <th class="text-end">Cantidad Total</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $stmtRegalos = $conn->prepare("
                    SELECT rg.nombre, SUM(cr.cantidad) AS total
                    FROM cotizaciones_regalos cr
                    JOIN cotizaciones c ON cr.id_cotizacion = c.id_cotizacion
                    JOIN regalos rg ON cr.id_regalo = rg.id_regalo
                    {$sql_where}
                    GROUP BY rg.nombre
                    ORDER BY total DESC
                ");
                $stmtRegalos->execute([':estatus' => $filtro_estatus, ':fecha' => $filtro_fecha]);
                $regalosTotales = $stmtRegalos->fetchAll(PDO::FETCH_ASSOC);

                foreach ($regalosTotales as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['nombre']) ?></td>
                        <td class="text-end fw-bold"><?= number_format($r['total']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header bg-success text-white fw-bold">
            Entregas por Repartidor (Resumen)
        </div>
        <ul class="list-group list-group-flush">
            <?php foreach ($resumen as $i => $r): ?>
                <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <table class="table w-100 align-middle mb-0" border="0">
                            <tr>
                                <td class="fw-bold text-uppercase">
                                    <?= htmlspecialchars($r['repartidor']) ?> <?= $r['total_entregas'] ?> pedidos - <b>$<?= number_format($r['monto_total']) ?></b>
                                </td>
                                <td align="right">
                                    <button class="btn btn-sm btn-outline-success" type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#detalle<?= $i ?>"
                                            aria-expanded="false"
                                            aria-controls="detalle<?= $i ?>">
                                        Ver detalle
                                    </button>
                                    <button class="btn btn-sm btn-danger" type="button"
                                            onclick="cerrarCollapse('detalle<?= $i ?>')">
                                        Cerrar detalle
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="collapse mt-3" id="detalle<?= $i ?>">
                        <div class="p-3 bg-white border-start border-3 border-success mt-2">
                            <?php
                            $stmtDetalle = $conn->prepare("
                                SELECT c.id_cotizacion, c.total, c.fecha_entrega, cl.nombre AS cliente, d.alcaldia_municipio
                                FROM asignaciones_entrega ae
                                JOIN cotizaciones c ON ae.id_cotizacion = c.id_cotizacion
                                JOIN clientes cl ON c.id_cliente = cl.id_cliente
                                JOIN direcciones d ON c.id_direccion = d.id_direccion
                                WHERE ae.id_usuario = :id_usuario AND c.estatus = :estatus AND c.fecha_entrega = :fecha
                            ");
                            $stmtDetalle->execute([
                                ':id_usuario' => $r['id_usuario'],
                                ':estatus' => $filtro_estatus,
                                ':fecha' => $filtro_fecha
                            ]);
                            $detalles = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($detalles as $d): ?>
                                <p><b>Cliente:</b> <?= htmlspecialchars($d['cliente']) ?> | <b>Alcaldía:</b> <?= htmlspecialchars($d['alcaldia_municipio']) ?></p>
                                <p><b>Folio:</b> <?= $d['id_cotizacion'] ?> | <b>Monto:</b> $<?= number_format($d['total']) ?></p>
                                <p><b>Fecha entrega:</b> <?= date('d/m/Y', strtotime($d['fecha_entrega'])) ?></p>

                                <p><b>Productos:</b></p>
                                <ul class="mb-3">
                                    <?php
                                    $stmtProdDet = $conn->prepare("
                                        SELECT r.nombre_producto, cp.cantidad
                                        FROM cotizaciones_productos cp
                                        JOIN recetas r ON cp.id_producto = r.id_receta
                                        WHERE cp.id_cotizacion = ?
                                    ");
                                    $stmtProdDet->execute([$d['id_cotizacion']]);
                                    $productos = $stmtProdDet->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($productos as $prod): ?>
                                        <li><?= htmlspecialchars($prod['nombre_producto']) ?>: <?= $prod['cantidad'] ?></li>
                                    <?php endforeach; ?>
                                </ul>

                                <p><b>Regalos:</b></p>
                                <ul class="mb-3">
                                    <?php
                                    $stmtRegaloDet = $conn->prepare("
                                        SELECT rg.nombre AS nombre_regalo, cr.cantidad
                                        FROM cotizaciones_regalos cr
                                        JOIN regalos rg ON cr.id_regalo = rg.id_regalo
                                        WHERE cr.id_cotizacion = ?
                                    ");
                                    $stmtRegaloDet->execute([$d['id_cotizacion']]);
                                    $regalos = $stmtRegaloDet->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($regalos as $reg): ?>
                                        <li><?= htmlspecialchars($reg['nombre_regalo']) ?>: <?= $reg['cantidad'] ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <hr>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<script>
    function cerrarCollapse(id) {
        const target = document.getElementById(id);
        const collapse = bootstrap.Collapse.getOrCreateInstance(target);
        collapse.hide();
    }
</script>