<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../front/auth/login.php');
    exit();
}
?>
<style>
    /* No requiere demasiado CSS gracias a Bootstrap */

    /* Opcional: Ajustes adicionales */
    .headerC img {
        object-fit: cover;
    }

    .headerC a.btn-danger {
        font-size: 14px;
        padding: 8px 20px;
        border-radius: 5px;
    }


</style>
<header class="headerC bg-success text-white p-3 d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
        <!-- Ícono de usuario -->
        <img src="../../front/multimedia/user.png" alt="Perfil" class="rounded-circle me-3" style="width: 40px; height: 40px; border: 2px solid white;">

        <!-- Información del usuario -->
        <div>
            <span class="fw-bold d-block"><?php echo htmlspecialchars($_SESSION['user_name']) ?></span>
            <span class="small"><?php echo htmlspecialchars(ucfirst($_SESSION['user_role'])); ?></span>
        </div>
    </div>

    <!-- Botón de cerrar sesión -->
    <a href="../auth/logout.php" class="btn btn-danger">Cerrar Sesión</a>
</header>
