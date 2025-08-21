<?php
include '../../back/db/connection.php';

try {
    // Obtener los clientes activos
    $query = "SELECT id_cliente, nombre, apellido_paterno, apellido_materno, telefono1 FROM clientes WHERE estatus = 'activo' ORDER BY nombre ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener clientes: " . $e->getMessage());
}
?>

<style>
    .contact-card {
        border: 1px solid #4CAF50;
        border-radius: 8px;
        transition: background 0.2s ease-in-out;
        padding: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        background: white;
    }

    .contact-card:hover {
        background: #f0f0f0;
    }

    .contact-name {
        font-size: 18px;
        font-weight: bold;
    }

    .contact-phone {
        font-size: 14px;
        color: #666;
    }

    .floating-button {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: #4CAF50;
        color: white;
        border: none;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        font-size: 24px;
        text-align: center;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        transition: background 0.3s, transform 0.2s;
        z-index: 1000;
    }

    .floating-button:hover {
        background-color: #388E3C;
        transform: scale(1.1);
    }

</style>

<div class="container mt-4">
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Regresar al Dashboard</a>

    <h1 class="text-center">Gestión de Clientes</h1>

    <input type="text" id="search" class="form-control mb-3" placeholder="Buscar cliente...">

    <div class="list-group">
        <?php foreach ($clientes as $cliente): ?>
            <div class="contact-card mb-2" data-id="<?= $cliente['id_cliente'] ?>">
                <div class="flex-grow-1">
                    <p class="contact-name"><?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido_paterno']) ?></p>
                    <p class="contact-phone"><?= htmlspecialchars($cliente['telefono1']) ?></p>
                </div>
                <div class="d-flex gap-2">
                    <a href="nueva_cotizacion.php?id=<?= $cliente['id_cliente'] ?>" class="btn btn-primary btn-sm">Cotizar</a>
                    <a href="direcciones.php?id=<?= $cliente['id_cliente'] ?>" class="btn btn-warning btn-sm">Direcciones</a>
                    <a href="editar_cliente.php?id=<?= $cliente['id_cliente'] ?>" class="btn btn-success btn-sm">Editar</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<button class="floating-button" onclick="window.location.href='nuevo_cliente.php'">
    +
</button>

<script>
    document.getElementById('search').addEventListener('input', function () {
        let searchValue = this.value.toLowerCase().trim();
        let contacts = document.querySelectorAll('.contact-card');

        contacts.forEach(contact => {
            let name = contact.querySelector('.contact-name').textContent.toLowerCase();
            let phone = contact.querySelector('.contact-phone').textContent.toLowerCase();

            if (name.includes(searchValue) || phone.includes(searchValue)) {
                contact.style.display = 'flex'; // Mantener el diseño flexible
            } else {
                contact.style.display = 'none';
            }
        });
    });
</script>
