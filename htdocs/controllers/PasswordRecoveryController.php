<?php
require_once '../config/database.php';
require_once '../config/email_config.php';

session_start();

$database = new Database();
$db = $database->getConnection();

$action = $_GET['action'] ?? '';

// 1. Solicitar recuperación (envía correo con enlace)
if ($action == 'solicitar' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    
    // Verificar si el email existe
    $stmt = $db->prepare("SELECT id, fullname FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Generar token único
        $token = bin2hex(random_bytes(50));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Guardar token en la base de datos
        $update = $db->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
        $update->execute([$token, $expires, $email]);
        
        // Crear enlace de recuperación
        $resetLink = "https://tesco-congress.infinityfree.io/views/restablecer.php?token=" . $token;
        
        // Enviar correo
        $asunto = "Recuperación de contraseña - Congreso TESCo 2027";
        $cuerpo = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #003366; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f4f6f9; }
                .footer { background: #001f3f; color: white; padding: 15px; text-align: center; font-size: 12px; }
                .btn { background: #2e7d32; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block; }
                .warning { color: #d32f2f; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Congreso Internacional TESCo 2027</h2>
                </div>
                <div class='content'>
                    <h3>¡Hola " . $user['fullname'] . "!</h3>
                    <p>Hemos recibido una solicitud para restablecer tu contraseña.</p>
                    <p>Si no fuiste tú, ignora este correo. Tu contraseña no cambiará.</p>
                    <p>Para crear una nueva contraseña, haz clic en el siguiente botón:</p>
                    <p style='text-align: center;'>
                        <a href='" . $resetLink . "' class='btn' style='color: white;'>Restablecer Contraseña</a>
                    </p>
                    <p>O copia y pega este enlace en tu navegador:</p>
                    <p><small>" . $resetLink . "</small></p>
                    <p class='warning'>Este enlace expirará en 1 hora.</p>
                    <br>
                    <p>Saludos,<br><strong>Comité Organizador TESCo 2027</strong></p>
                </div>
                <div class='footer'>
                    <p>Tecnológico de Estudios Superiores de Coacalco | Innovación y Tecnología para el Futuro</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $enviado = enviarCorreo($email, $user['fullname'], $asunto, $cuerpo);
        
        if ($enviado) {
            header("Location: ../views/login.php?msg=revision_correo");
        } else {
            header("Location: ../views/recuperar.php?error=correo_no_enviado");
        }
    } else {
        // No mostramos si el email existe o no por seguridad
        header("Location: ../views/login.php?msg=revision_correo");
    }
    exit;
}

// 2. Restablecer contraseña (validar token y actualizar)
if ($action == 'restablecer' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        header("Location: ../views/restablecer.php?token=" . $token . "&error=no_coinciden");
        exit;
    }
    
    if (strlen($password) < 6) {
        header("Location: ../views/restablecer.php?token=" . $token . "&error=password_corta");
        exit;
    }
    
    // Verificar token válido y no expirado
    $stmt = $db->prepare("SELECT id, email, fullname FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Actualizar contraseña
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $update = $db->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
        $update->execute([$hashed, $user['id']]);
        
        // Enviar correo de confirmación
        $asunto = "Tu contraseña ha sido actualizada - Congreso TESCo 2027";
        $cuerpo = "
        <html>
        <body>
            <h2>Contraseña actualizada</h2>
            <p>Hola " . $user['fullname'] . ",</p>
            <p>Tu contraseña ha sido cambiada exitosamente.</p>
            <p>Si no realizaste este cambio, contacta al soporte inmediatamente.</p>
            <a href='https://tesco-congress.infinityfree.io/views/login.php'>Iniciar Sesión</a>
        </body>
        </html>
        ";
        enviarCorreo($user['email'], $user['fullname'], $asunto, $cuerpo);
        
        header("Location: ../views/login.php?msg=password_actualizada");
    } else {
        header("Location: ../views/restablecer.php?error=token_invalido");
    }
    exit;
}
?>