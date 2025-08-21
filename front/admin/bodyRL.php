<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<?php
include '../../back/db/connection.php';

// Fecha actual
$fechaHoy = date('d/m/Y');

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
    WHERE c.estatus = 'en curso'
    GROUP BY ae.id_usuario
    ORDER BY u.nombre
");
$stmt->execute();
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
    WHERE c.estatus = 'en curso'
");
$stmtProd->execute();
$totalProductos = $stmtProd->fetchColumn();


// Total de regalos
$stmtRegalos = $conn->prepare("
    SELECT SUM(cantidad) AS total_regalos
    FROM cotizaciones_regalos cr
    JOIN cotizaciones c ON cr.id_cotizacion = c.id_cotizacion
    WHERE c.estatus = 'en curso'
");
$stmtRegalos->execute();
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
    <h2 class="text-center text-success mb-4">📦 Reporte de Logística - <?= $fechaHoy ?></h2>

    <!-- RESUMEN GENERAL -->
    <div class="row text-center mb-4">
        <div class="col-md-3">
            <div class="p-4 bg-light rounded shadow-sm">
                <h5 class="text-muted">Total de entregas</h5>
                <h3 class="text-success fw-bold"><?= $totalEntregas ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-4 bg-light rounded shadow-sm">
                <h5 class="text-muted">Total productos</h5>
                <h3 class="text-success fw-bold"><?= number_format($totalProductos) ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-4 bg-light rounded shadow-sm">
                <h5 class="text-muted">Total regalos</h5>
                <h3 class="text-success fw-bold"><?= number_format($totalRegalos) ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="p-4 bg-light rounded shadow-sm">
                <h5 class="text-muted">Total $</h5>
                <h3 class="text-success fw-bold">$<?= number_format($totalMonto, 2) ?></h3>
            </div>
        </div>
    </div>





    <!-- TOTAL DE PRODUCTOS ENTREGADOS -->
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
                    WHERE c.estatus = 'en curso'
                    GROUP BY r.nombre_producto
                    ORDER BY total DESC
                ");
                $stmtTotalProd->execute();
                $productosTotales = $stmtTotalProd->fetchAll(PDO::FETCH_ASSOC);

                foreach ($productosTotales as $p): ?>
                    <tr>
                        <td><?= $p['nombre_producto'] ?></td>
                        <td class="text-end fw-bold"><?= number_format($p['total']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>



    <!-- TOTAL DE REGALOS ENTREGADOS -->
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
                    WHERE c.estatus = 'en curso'
                    GROUP BY rg.nombre
                    ORDER BY total DESC
                ");
                $stmtRegalos->execute();
                $regalosTotales = $stmtRegalos->fetchAll(PDO::FETCH_ASSOC);

                foreach ($regalosTotales as $r): ?>
                    <tr>
                        <td><?= $r['nombre'] ?></td>
                        <td class="text-end fw-bold"><?= number_format($r['total']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>




    <!-- RESUMEN POR REPARTIDOR -->
    <div class="card shadow mb-4">
        <div class="card-header bg-success text-white fw-bold">
            Entregas por Repartidor (Resumen)
        </div>
        <ul class="list-group list-group-flush">
            <?php foreach ($resumen as $i => $r): ?>
                <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <table class="table w-100 align-middle" border="1">
                            <tr>
                                <td class="fw-bold text-uppercase">
                                    <?= $r['repartidor'] ?> <?= $r['total_entregas'] ?> pedidos - <b>$<?= number_format($r['monto_total']) ?></b>
                                </td>
                                <td align="right"></td>
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
                            WHERE ae.id_usuario = ? AND c.estatus = 'en curso'
                        ");
                            $stmtDetalle->execute([$r['id_usuario']]);
                            $detalles = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($detalles as $d): ?>
                                <p><b>Cliente:</b> <?= $d['cliente'] ?> | <b>Alcaldía:</b> <?= $d['alcaldia_municipio'] ?></p>
                                <p><b>Folio:</b> <?= $d['id_cotizacion'] ?> | <b>Monto:</b> $<?= number_format($d['total']) ?></p>
                                <p><b>Fecha entrega:</b> <?= date('d/m/Y', strtotime($d['fecha_entrega'])) ?></p>

                                <p><b>Productos:</b></p>
                                <ul class="mb-3">
                                    <?php
                                    $stmtProd = $conn->prepare("
                                    SELECT r.nombre_producto, cp.cantidad
                                    FROM cotizaciones_productos cp
                                    JOIN recetas r ON cp.id_producto = r.id_receta
                                    WHERE cp.id_cotizacion = ?
                                ");
                                    $stmtProd->execute([$d['id_cotizacion']]);
                                    $productos = $stmtProd->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($productos as $prod): ?>
                                        <li><?= $prod['nombre_producto'] ?>: <?= $prod['cantidad'] ?></li>
                                    <?php endforeach; ?>
                                </ul>

                                <p><b>Regalos:</b></p>
                                <ul class="mb-3">
                                    <?php
                                    $stmtRegalo = $conn->prepare("
                                    SELECT rg.nombre AS nombre_regalo, cr.cantidad
                                    FROM cotizaciones_regalos cr
                                    JOIN regalos rg ON cr.id_regalo = rg.id_regalo
                                    WHERE cr.id_cotizacion = ?
                                ");
                                    $stmtRegalo->execute([$d['id_cotizacion']]);
                                    $regalos = $stmtRegalo->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($regalos as $reg): ?>
                                        <li><?= $reg['nombre_regalo'] ?>: <?= $reg['cantidad'] ?></li>
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
