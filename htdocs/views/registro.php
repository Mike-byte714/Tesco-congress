<?php include 'partials/header.php'; ?>

<!-- ESTILOS MODERNOS - FONDO BLANCO, TARJETA CON SOMBRA DESTACADA -->
<style>
    /* Fondo completamente BLANCO */
    body {
        background: #ffffff !important;
    }
    
    /* Contenedor principal */
    .registro-wrapper {
        min-height: 80vh;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }
    
    /* Tarjeta blanca con SOMBRA FUERTE para destacar */
    .registro-card {
        background: #ffffff;
        border-radius: 28px;
        box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.25),
                    0 8px 20px -8px rgba(0, 0, 0, 0.15);
        padding: 2.5rem;
        width: 100%;
        max-width: 500px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid rgba(0, 51, 102, 0.08);
    }
    .registro-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3),
                    0 10px 25px -10px rgba(0, 0, 0, 0.2);
    }
    
    /* Encabezado */
    .registro-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    .registro-header h2 {
        font-weight: 700;
        color: #003366;
        margin-bottom: 0.5rem;
        font-size: 1.8rem;
    }
    .registro-header p {
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
    .input-group-custom input,
    .input-group-custom select {
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
    .input-group-custom select {
        padding: 13px 16px 13px 45px;
        cursor: pointer;
        appearance: none;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="%23003366" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>');
        background-repeat: no-repeat;
        background-position: right 16px center;
    }
    .input-group-custom input:focus,
    .input-group-custom select:focus {
        border-color: #2e7d32;
        outline: none;
        box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
    }
    .input-group-custom input:focus + i,
    .input-group-custom select:focus + i {
        color: #2e7d32;
    }
    
    /* Botón verde */
    .btn-registro {
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
    .btn-registro:hover {
        background: #1b5e20;
        transform: translateY(-2px);
    }
    
    /* Enlace a login */
    .login-link {
        text-align: center;
        margin-top: 1.5rem;
        font-size: 0.85rem;
        color: #64748b;
    }
    .login-link a {
        color: #2e7d32;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.2s;
    }
    .login-link a:hover {
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
    
    @media (max-width: 576px) {
        .registro-card { padding: 1.5rem; margin: 1rem; }
        .registro-header h2 { font-size: 1.5rem; }
        .input-group-custom input,
        .input-group-custom select { padding: 11px 12px 11px 42px; }
    }
</style>

<div class="registro-wrapper">
    <div class="registro-card">
        <div class="registro-header">
            <h2>Crear cuenta</h2>
            <p>Únete a la comunidad TESCo 2027</p>
        </div>

        <?php if(isset($_GET['error'])): ?>
            <div class="alert-custom">
                <i class="fas fa-exclamation-circle"></i>
                <span>Error al registrar. Por favor, verifica tus datos.</span>
            </div>
        <?php endif; ?>

        <form action="../controllers/AuthController.php?action=register" method="POST">
            <div class="input-group-custom">
                <i class="fas fa-user"></i>
                <input type="text" name="fullname" placeholder="Nombre completo" required>
            </div>
            <div class="input-group-custom">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Correo electrónico" required>
            </div>
            <div class="input-group-custom">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Contraseña" required>
            </div>
            <div class="input-group-custom">
                <i class="fas fa-building"></i>
                <input type="text" name="institution" placeholder="Institución">
            </div>
            <div class="input-group-custom">
                <i class="fas fa-globe"></i>
                <select name="country" required>
                    <option value="">Selecciona tu país</option>
                    <option value="México">🇲🇽 México</option>
                    <option value="Estados Unidos">🇺🇸 Estados Unidos</option>
                    <option value="Canadá">🇨🇦 Canadá</option>
                    <option value="España">🇪🇸 España</option>
                    <option value="Colombia">🇨🇴 Colombia</option>
                    <option value="Argentina">🇦🇷 Argentina</option>
                    <option value="Chile">🇨🇱 Chile</option>
                    <option value="Perú">🇵🇪 Perú</option>
                    <option value="Venezuela">🇻🇪 Venezuela</option>
                    <option value="Costa Rica">🇨🇷 Costa Rica</option>
                    <option value="Otro">🌍 Otro</option>
                </select>
            </div>
            <div class="input-group-custom">
                <i class="fas fa-user-tag"></i>
                <select name="role" required>
                    <option value="assistant">👥 Asistente</option>
                    <option value="speaker">🎤 Ponente</option>
                </select>
            </div>
            <button type="submit" class="btn-registro">
                <i class="fas fa-user-plus"></i> Registrarse
            </button>
        </form>

        <div class="login-link">
            ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>