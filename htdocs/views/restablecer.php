<?php include 'partials/header.php'; 

$token = $_GET['token'] ?? '';
if(empty($token)) {
    header("Location: login.php");
    exit;
}
?>

<style>
    .restablecer-wrapper {
        min-height: 70vh;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }
    .restablecer-card {
        background: white;
        border-radius: 28px;
        box-shadow: 0 20px 40px -12px rgba(0,0,0,0.15);
        padding: 2rem;
        width: 100%;
        max-width: 450px;
    }
    .restablecer-header {
        text-align: center;
        margin-bottom: 1.5rem;
    }
    .restablecer-header h2 {
        color: #003366;
        font-weight: 700;
    }
    .btn-restablecer {
        background: #2e7d32;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 40px;
        width: 100%;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn-restablecer:hover {
        background: #1b5e20;
    }
</style>

<div class="restablecer-wrapper">
    <div class="restablecer-card">
        <div class="restablecer-header">
            <h2><i class="fas fa-lock"></i> Nueva contraseña</h2>
            <p>Ingresa tu nueva contraseña</p>
        </div>

        <?php if(isset($_GET['error'])): ?>
            <?php if($_GET['error'] == 'no_coinciden'): ?>
                <div class="alert alert-danger">❌ Las contraseñas no coinciden</div>
            <?php elseif($_GET['error'] == 'password_corta'): ?>
                <div class="alert alert-danger">❌ La contraseña debe tener al menos 6 caracteres</div>
            <?php elseif($_GET['error'] == 'token_invalido'): ?>
                <div class="alert alert-danger">❌ El enlace es inválido o ha expirado. Solicita uno nuevo.</div>
            <?php endif; ?>
        <?php endif; ?>

        <form action="../controllers/PasswordRecoveryController.php?action=restablecer" method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            
            <div class="mb-3">
                <label class="form-label">Nueva contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Confirmar contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                    <input type="password" name="confirm_password" class="form-control" required minlength="6">
                </div>
            </div>
            
            <button type="submit" class="btn-restablecer">
                <i class="fas fa-save"></i> Actualizar contraseña
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