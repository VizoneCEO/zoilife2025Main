<div class="login-container"  >
    <div class="login-form" >

        <h2 class="text-center">Iniciar Sesión</h2>

        <!-- Mostrar mensaje de error -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger text-center" role="alert">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <form action="../../back/auth/process_login.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Ingresa tu correo" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Ingresa tu contraseña" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Ingresar</button>
            </div>
            <div class="text-center mt-3">
                <a href="recuperar_password.php" class="link">¿Olvidaste tu contraseña?</a>
            </div>
        </form>
    </div>
</div>