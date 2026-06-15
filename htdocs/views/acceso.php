<?php
session_start();
require_once '../config/database.php';
require_once '../models/User.php';

$database = new Database();
$db = $database->getConnection();
$userModel = new User($db);

$error_message = '';
$success_message = '';

// LOGIN CON CORREO O NOMBRE DE USUARIO
if (isset($_POST['login_submit'])) {
    $login = trim($_POST['login']);
    $password = $_POST['password'];
    
    $stmt = $db->prepare("SELECT * FROM users WHERE email = :login OR username = :login");
    $stmt->execute(['login' => $login]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['username'] = $user['username'];
        
        if ($user['role'] == 'admin') {
            header("Location: admin/dashboard.php");
        } elseif ($user['role'] == 'speaker') {
            header("Location: dashboard_ponente.php");
        } else {
            header("Location: dashboard_usuario.php");
        }
        exit;
    } else {
        $error_message = "❌ Correo/Usuario o contraseña incorrectos.";
    }
}

// REGISTRO CON VERIFICACIÓN DE CORREO DUPLICADO
if (isset($_POST['register_submit'])) {
    $username = trim($_POST['username']);
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $institution = $_POST['institution'] ?? '';
    $country = $_POST['country'] ?? '';
    $role = $_POST['role'];

    $checkEmail = $db->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmail->execute([$email]);
    if ($checkEmail->rowCount() > 0) {
        $error_message = "❌ Este correo electrónico ya está registrado. <a href='recuperar.php' class='alert-link'>¿Olvidaste tu contraseña?</a>";
    }
    elseif ($username) {
        $checkUser = $db->prepare("SELECT id FROM users WHERE username = ?");
        $checkUser->execute([$username]);
        if ($checkUser->rowCount() > 0) {
            $error_message = "❌ Este nombre de usuario ya está en uso. Elige otro.";
        }
    }
    
    if (empty($error_message)) {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $db->prepare("INSERT INTO users (username, fullname, email, password, institution, country, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$username, $fullname, $email, $hashed, $institution, $country, $role])) {
            $success_message = "✅ ¡Registro exitoso! Ahora puedes iniciar sesión.";
        } else {
            $error_message = "❌ Error al registrar. Intenta nuevamente.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>TESCo Congress - Acceso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', system-ui, sans-serif; background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem; }
        .auth-container { max-width: 1100px; width: 100%; background: white; border-radius: 32px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); overflow: hidden; display: flex; flex-wrap: wrap; }
        .info-panel { flex: 1; min-width: 280px; background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%); padding: 2rem; color: white; display: flex; flex-direction: column; justify-content: center; position: relative; overflow: hidden; }
        .info-panel::before { content: ''; position: absolute; top: -50%; right: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%); animation: pulseGlow 8s ease-in-out infinite; }
        @keyframes pulseGlow { 0%,100% { transform: translate(0,0); opacity: 0.5; } 50% { transform: translate(-20px,-20px); opacity: 1; } }
        .info-content { position: relative; z-index: 2; text-align: center; }
        .info-content h1 { font-size: 2rem; font-weight: 800; margin-bottom: 1rem; }
        .info-content p { font-size: 0.9rem; line-height: 1.5; margin-bottom: 1.5rem; }
        .info-badge { background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); padding: 0.5rem 1rem; border-radius: 50px; display: inline-block; font-weight: 500; font-size: 0.8rem; margin-bottom: 1.5rem; }
        .switch-btn { background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.4); padding: 10px 20px; border-radius: 50px; color: white; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-size: 0.9rem; }
        .switch-btn:hover { background: white; color: #0f766e; transform: translateY(-2px); }
        .forms-panel { flex: 1; min-width: 280px; background: white; padding: 2rem; position: relative; }
        .forms-container { width: 100%; overflow: hidden; }
        .form-wrapper { display: flex; width: 200%; transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
        .form-slide { width: 50%; padding: 0 0.5rem; }
        .auth-form h2 { font-size: 1.5rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem; }
        .auth-form .subtitle { color: #64748b; margin-bottom: 1.5rem; font-size: 0.85rem; }
        .input-group { margin-bottom: 1rem; position: relative; }
        .input-group i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.9rem; }
        .input-group input, .input-group select { width: 100%; padding: 12px 12px 12px 42px; border: 1.5px solid #e2e8f0; border-radius: 14px; font-size: 0.9rem; transition: all 0.2s; background: #f8fafc; }
        .input-group input:focus, .input-group select:focus { border-color: #14b8a6; outline: none; background: white; box-shadow: 0 0 0 3px rgba(20,184,166,0.1); }
        .btn-primary { background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%); border: none; padding: 12px; border-radius: 40px; font-weight: 600; font-size: 0.95rem; color: white; width: 100%; cursor: pointer; transition: all 0.3s ease; margin-top: 0.5rem; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(15,118,110,0.3); }
        .forgot-link { text-align: center; margin-top: 1rem; }
        .forgot-link a { color: #64748b; text-decoration: none; font-size: 0.75rem; }
        .forgot-link a:hover { color: #14b8a6; }
        .divider { display: flex; align-items: center; text-align: center; margin: 1rem 0; }
        .divider::before, .divider::after { content: ''; flex: 1; border-bottom: 1px solid #e2e8f0; }
        .divider span { padding: 0 10px; color: #64748b; font-size: 0.75rem; }
        .g_id_signin { display: flex; justify-content: center; margin-top: 1rem; }
        .alert-custom { background: #fef2f2; border-left: 4px solid #dc2626; border-radius: 12px; padding: 10px; margin-bottom: 1rem; font-size: 0.8rem; color: #b91c1c; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .alert-custom a { color: #dc2626; font-weight: 600; }
        .alert-success-custom { background: #e8f5e9; border-left-color: #2e7d32; color: #1b5e20; }
        @media (max-width: 768px) { body { padding: 0.5rem; } .auth-container { flex-direction: column; border-radius: 24px; } .info-panel { padding: 1.5rem; text-align: center; } .forms-panel { padding: 1.5rem; } .input-group input, .input-group select { font-size: 16px; padding: 10px 10px 10px 38px; } }
    </style>
</head>
<body>

<div class="auth-container">
    <div class="info-panel">
        <div class="info-content" id="infoContent">
            <div class="info-badge"><i class="fas fa-calendar-alt"></i> 15-17 Nov 2027</div>
            <h1><i class="fas fa-graduation-cap"></i><br>TESCo Congress</h1>
            <p>Innovación, tecnología y educación para el futuro. Únete a expertos de todo el mundo.</p>
            <button class="switch-btn" id="switchToRegisterBtn"><i class="fas fa-user-plus"></i> ¿Nuevo? Regístrate</button>
        </div>
    </div>

    <div class="forms-panel">
        <div class="forms-container">
            <div class="form-wrapper" id="formWrapper">
                
                <!-- LOGIN -->
                <div class="form-slide" id="loginSlide">
                    <div class="auth-form">
                        <h2>Bienvenido de vuelta</h2>
                        <p class="subtitle">Inicia sesión con tu correo o usuario</p>
                        
                        <?php if ($error_message && !isset($_POST['register_submit'])): ?>
                            <div class="alert-custom"><i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?></div>
                        <?php endif; ?>
                        <?php if ($success_message): ?>
                            <div class="alert-custom alert-success-custom"><i class="fas fa-check-circle"></i> <?php echo $success_message; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="input-group"><i class="fas fa-envelope"></i><input type="text" name="login" placeholder="Correo o nombre de usuario" required></div>
                            <div class="input-group"><i class="fas fa-lock"></i><input type="password" name="password" placeholder="Contraseña" required></div>
                            <button type="submit" name="login_submit" class="btn-primary"><i class="fas fa-arrow-right-to-bracket"></i> Iniciar Sesión</button>
                            <div class="forgot-link"><a href="recuperar.php"><i class="fas fa-question-circle"></i> ¿Olvidaste tu contraseña o usuario?</a></div>
                        </form>

                        <div class="divider"><span>O continúa con</span></div>
                        <div id="g_id_onload" data-client_id="358668615160-icec4contl9ssmmqtrdmctg7c7ntucb4.apps.googleusercontent.com" data-callback="handleGoogleLogin"></div>
                        <div class="g_id_signin" data-type="standard" data-shape="rectangular" data-theme="outline" data-text="sign_in_with" data-size="large"></div>
                    </div>
                </div>

                <!-- REGISTRO -->
                <div class="form-slide" id="registerSlide">
                    <div class="auth-form">
                        <h2>Crear cuenta</h2>
                        <p class="subtitle">Únete a la comunidad TESCo</p>
                        
                        <?php if ($error_message && isset($_POST['register_submit'])): ?>
                            <div class="alert-custom"><i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="input-group"><i class="fas fa-user-tag"></i><input type="text" name="username" placeholder="Nombre de usuario" required></div>
                            <div class="input-group"><i class="fas fa-user"></i><input type="text" name="fullname" placeholder="Nombre completo" required></div>
                            <div class="input-group"><i class="fas fa-envelope"></i><input type="email" name="email" placeholder="Correo electrónico" required></div>
                            <div class="input-group"><i class="fas fa-lock"></i><input type="password" name="password" placeholder="Contraseña" required></div>
                            <div class="input-group"><i class="fas fa-building"></i><input type="text" name="institution" placeholder="Institución"></div>
                            <div class="input-group"><i class="fas fa-globe"></i><select name="country"><option value="">Selecciona tu país</option><option value="México">🇲🇽 México</option><option value="Estados Unidos">🇺🇸 Estados Unidos</option><option value="Canadá">🇨🇦 Canadá</option><option value="España">🇪🇸 España</option><option value="Colombia">🇨🇴 Colombia</option><option value="Argentina">🇦🇷 Argentina</option><option value="Chile">🇨🇱 Chile</option><option value="Perú">🇵🇪 Perú</option><option value="China">🇨🇳 China</option><option value="Japón">🇯🇵 Japón</option><option value="Otro">🌍 Otro</option></select></div>
                            <div class="input-group"><i class="fas fa-user-tag"></i><select name="role"><option value="assistant">👥 Asistente</option><option value="speaker">🎤 Ponente</option></select></div>
                            <button type="submit" name="register_submit" class="btn-primary"><i class="fas fa-user-plus"></i> Registrarse</button>
                        </form>

                        <div class="divider"><span>O regístrate con</span></div>
                        <div id="g_id_onload_register" data-client_id="358668615160-icec4contl9ssmmqtrdmctg7c7ntucb4.apps.googleusercontent.com" data-callback="handleGoogleRegister"></div>
                        <div class="g_id_signin" data-type="standard" data-shape="rectangular" data-theme="outline" data-text="sign_up_with" data-size="large"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const formWrapper = document.getElementById('formWrapper');
    const switchToRegisterBtn = document.getElementById('switchToRegisterBtn');
    const infoContent = document.getElementById('infoContent');

    function switchToLogin() {
        formWrapper.style.transform = 'translateX(0%)';
        infoContent.innerHTML = `<div class="info-badge"><i class="fas fa-calendar-alt"></i> 15-17 Nov 2027</div><h1><i class="fas fa-graduation-cap"></i><br>TESCo Congress</h1><p>Innovación, tecnología y educación para el futuro. Únete a expertos de todo el mundo.</p><button class="switch-btn" id="switchToRegisterBtn"><i class="fas fa-user-plus"></i> ¿Nuevo? Regístrate</button>`;
        document.getElementById('switchToRegisterBtn')?.addEventListener('click', switchToRegister);
    }

    function switchToRegister() {
        formWrapper.style.transform = 'translateX(-50%)';
        infoContent.innerHTML = `<div class="info-badge"><i class="fas fa-rocket"></i> Únete hoy</div><h1><i class="fas fa-user-plus"></i><br>Comienza ahora</h1><p>Regístrate para ser parte del congreso más importante. Accede a conferencias, talleres y networking.</p><button class="switch-btn" id="switchToLoginBtn"><i class="fas fa-sign-in-alt"></i> ¿Ya tienes cuenta? Inicia sesión</button>`;
        document.getElementById('switchToLoginBtn')?.addEventListener('click', switchToLogin);
    }

    if (switchToRegisterBtn) switchToRegisterBtn.addEventListener('click', switchToRegister);
    <?php if (isset($_POST['register_submit']) && $error_message): ?> switchToRegister(); <?php endif; ?>

    function handleGoogleLogin(response) {
        fetch('/controllers/GoogleAuthController.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'credential=' + encodeURIComponent(response.credential)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) window.location.href = data.redirect;
            else alert('Error: ' + (data.message || 'No se pudo iniciar sesión'));
        });
    }

    function handleGoogleRegister(response) {
        fetch('/controllers/GoogleAuthController.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'credential=' + encodeURIComponent(response.credential) + '&register=true'
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) window.location.href = data.redirect;
            else alert('Error: ' + (data.message || 'No se pudo registrar'));
        });
    }
</script>
</body>
</html>