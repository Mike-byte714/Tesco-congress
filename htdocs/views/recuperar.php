<?php include 'partials/header.php'; ?>

<style>
    .recuperar-wrapper {
        min-height: 70vh;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }
    .recuperar-card {
        background: white;
        border-radius: 28px;
        box-shadow: 0 20px 40px -12px rgba(0,0,0,0.15);
        padding: 2rem;
        width: 100%;
        max-width: 450px;
    }
    .recuperar-header {
        text-align: center;
        margin-bottom: 1.5rem;
    }
    .recuperar-header h2 {
        color: #003366;
        font-weight: 700;
    }
    .recuperar-header p {
        color: #2e7d32;
        font-size: 0.9rem;
    }
    .btn-recuperar {
        background: #2e7d32;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 40px;
        width: 100%;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn-recuperar:hover {
        background: #1b5e20;
        transform: translateY(-2px);
    }
    .alert-info {
        background: #e3f2fd;
        border-left: 4px solid #2196f3;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
    }
</style>

<div class="recuperar-wrapper">
    <div class="recuperar-card">
        <div class="recuperar-header">
            <h2><i class="fas fa-key"></i> ¿Olvidaste tu contraseña?</h2>
            <p>Ingresa tu correo y te enviaremos un enlace para restablecerla</p>
        </div>

        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> Error al enviar el correo. Intenta nuevamente.
            </div>
        <?php endif; ?>

        <div class="alert-info">
            <i class="fas fa-info-circle"></i> Te enviaremos un enlace a tu correo. Revisa también la carpeta de spam.
        </div>

        <form action="../controllers/PasswordRecoveryController.php?action=solicitar" method="POST">
            <div class="mb-3">
                <label class="form-label">Correo electrónico</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="tu@correo.com" required>
                </div>
            </div>
            <button type="submit" class="btn-recuperar">
                <i class="fas fa-paper-plane"></i> Enviar enlace de recuperación
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="login.php" class="text-decoration-none">
                <i class="fas fa-arrow-left"></i> Volver al inicio de sesión
            </a>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>