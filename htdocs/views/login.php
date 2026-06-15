<?php include 'partials/header.php'; ?>

<!-- ESTILOS MODERNOS PARA LOGIN - FONDO CLARO, TARJETA CON SOMBRA -->
<style>
    /* Fondo gris muy claro para contraste */
    body {
        background: #f8fafc !important;
    }
    
    /* Contenedor principal */
    .login-wrapper {
        min-height: 80vh;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }
    
    /* Tarjeta blanca con sombra destacada */
    .login-card {
        background: #ffffff;
        border-radius: 28px;
        box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.25),
                    0 8px 20px -8px rgba(0, 0, 0, 0.15);
        padding: 2.5rem;
        width: 100%;
        max-width: 450px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid rgba(0, 51, 102, 0.08);
    }
    .login-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3),
                    0 10px 25px -10px rgba(0, 0, 0, 0.2);
    }
    
    /* Encabezado */
    .login-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    .login-header h2 {
        font-weight: 700;
        color: #003366;
        margin-bottom: 0.5rem;
        font-size: 1.8rem;
    }
    .login-header p {
        color: #2e7d32;
        font-size: 0.9rem;
        font-weight: 500;
    }
    
    /* Campos de entrada con íconos */
    .input-group-custom {
        position: relative;
        margin-bottom: 1.2rem;
    }
    .input-group-custom i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #003366;
        font-size: 1rem;
        transition: color 0.2s;
        z-index: 2;
        width: 18px;
        text-align: center;
    }
    .input-group-custom input {
        width: 100%;
        padding: 13px 16px 13px 45px;
        border: 1.5px solid #e2e8f0;
        border-radius: 16px;
        font-size: 0.95rem;
        transition: all 0.25s;
        background: #ffffff;
        color: #1e293b;
        font-family: inherit;
    }
    .input-group-custom input:focus {
        border-color: #2e7d32;
        outline: none;
        box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
    }
    .input-group-custom input:focus + i {
        color: #2e7d32;
    }
    
    /* Botón verde */
    .btn-login {
        background: #2e7d32;
        border: none;
        color: white;
        font-weight: 600;
        padding: 13px;
        border-radius: 40px;
        width: 100%;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.25s;
        margin-top: 0.5rem;
    }
    .btn-login:hover {
        background: #1b5e20;
        transform: translateY(-2px);
    }
    
    /* Enlace de recuperación */
    .forgot-link {
        text-align: center;
        margin-top: 1rem;
        font-size: 0.85rem;
    }
    .forgot-link a {
        color: #64748b;
        text-decoration: none;
        transition: color 0.2s;
    }
    .forgot-link a:hover {
        color: #2e7d32;
        text-decoration: underline;
    }
    
    /* Enlace a registro */
    .register-link {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid #e2e8f0;
        font-size: 0.85rem;
        color: #64748b;
    }
    .register-link a {
        color: #2e7d32;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.2s;
    }
    .register-link a:hover {
        color: #003366;
        text-decoration: underline;
    }
    
    /* Alerta de error */
    .alert-custom {
        background: #fef2f2;
        border-left: 4px solid #dc2626;
        border-radius: 14px;
        padding: 0.8rem 1rem;
        margin-bottom: 1.5rem;
        font-size: 0.85rem;
        color: #b91c1c;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .alert-custom i {
        font-size: 1rem;
        color: #dc2626;
    }
    
    /* Alerta de éxito */
    .alert-success-custom {
        background: #e8f5e9;
        border-left: 4px solid #2e7d32;
        border-radius: 14px;
        padding: 0.8rem 1rem;
        margin-bottom: 1.5rem;
        font-size: 0.85rem;
        color: #1b5e20;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .alert-success-custom i {
        font-size: 1rem;
        color: #2e7d32;
    }
    
    @media (max-width: 576px) {
        .login-card { padding: 1.5rem; margin: 1rem; }
        .login-header h2 { font-size: 1.5rem; }
        .input-group-custom input { padding: 11px 12px 11px 42px; }
    }
</style>

<div class="login-wrapper">
    <div class="login-card">
        <div class="login-header">
            <h2>Iniciar Sesión</h2>
            <p>Bienvenido de vuelta</p>
        </div>

        <!-- Mensajes de éxito -->
        <?php if(isset($_GET['msg'])): ?>
            <?php if($_GET['msg'] == 'revision_correo'): ?>
                <div class="alert-success-custom">
                    <i class="fas fa-envelope"></i>
                    <span><strong>¡Revisa tu correo!</strong> Te hemos enviado un enlace para restablecer tu contraseña. Recuerda revisar también la carpeta de spam.</span>
                </div>
            <?php elseif($_GET['msg'] == 'password_actualizada'): ?>
                <div class="alert-success-custom">
                    <i class="fas fa-check-circle"></i>
                    <span><strong>¡Contraseña actualizada!</strong> Ya puedes iniciar sesión con tu nueva contraseña.</span>
                </div>
            <?php elseif($_GET['msg'] == 'registered'): ?>
                <div class="alert-success-custom">
                    <i class="fas fa-user-check"></i>
                    <span><strong>¡Registro exitoso!</strong> Revisa tu correo para confirmar tu cuenta. Ya puedes iniciar sesión.</span>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Mensajes de error -->
        <?php if(isset($_GET['error'])): ?>
            <div class="alert-custom">
                <i class="fas fa-exclamation-circle"></i>
                <span>Credenciales incorrectas. Por favor, verifica tu correo y contraseña.</span>
            </div>
        <?php endif; ?>

        <form action="../controllers/AuthController.php?action=login" method="POST">
            <div class="input-group-custom">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Correo electrónico" required>
            </div>
            <div class="input-group-custom">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Contraseña" required>
            </div>
            <button type="submit" class="btn-login">
                <i class="fas fa-arrow-right-to-bracket"></i> Ingresar
            </button>
        </form>

        <div class="forgot-link">
            <a href="recuperar.php"><i class="fas fa-question-circle"></i> ¿Olvidaste tu contraseña?</a>
        </div>

        <div class="register-link">
            ¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>