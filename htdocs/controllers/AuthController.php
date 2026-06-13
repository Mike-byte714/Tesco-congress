<?php
require_once '../config/database.php';
require_once '../models/User.php';
require_once '../config/email_config.php'; // Incluir configuración de correo

$database = new Database();
$db = $database->getConnection();
$userModel = new User($db);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_GET['action'] ?? '';
    
    if($action == 'register') {
        $hashed = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $result = $userModel->create(
            $_POST['email'], 
            $hashed, 
            $_POST['fullname'], 
            $_POST['institution'], 
            $_POST['country'], 
            $_POST['role']
        );
        
        if($result) {
            // 🔐 ENVIAR CORREO DE BIENVENIDA
            $asunto = "¡Bienvenido al Congreso TESCo 2027!";
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
                    .btn { background: #2e7d32; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Congreso Internacional TESCo 2027</h2>
                    </div>
                    <div class='content'>
                        <h3>¡Hola " . $_POST['fullname'] . "!</h3>
                        <p>Te damos la más cordial bienvenida al <strong>Congreso Internacional TESCo 2027</strong>.</p>
                        <p>Tu registro ha sido exitoso. Ahora eres parte de nuestra comunidad de innovación y tecnología.</p>
                        <p><strong>Tus datos de acceso:</strong></p>
                        <ul>
                            <li><strong>Correo:</strong> " . $_POST['email'] . "</li>
                            <li><strong>Tipo:</strong> " . ($_POST['role'] == 'assistant' ? 'Asistente' : 'Ponente') . "</li>
                        </ul>
                        <p>Puedes iniciar sesión en nuestro portal para:</p>
                        <ul>
                            <li>✓ Enviar proyectos de investigación</li>
                            <li>✓ Consultar el programa del evento</li>
                            <li>✓ Conocer a los ponentes internacionales</li>
                            <li>✓ Acceder a la biblioteca digital</li>
                        </ul>
                        <p style='text-align: center;'>
                            <a href='https://tesco-congress.infinityfree.io/views/login.php' class='btn' style='color: white;'>Iniciar Sesión</a>
                        </p>
                        <p>¡Te esperamos en el congreso!<br><strong>Comité Organizador TESCo 2027</strong></p>
                    </div>
                    <div class='footer'>
                        <p>Tecnológico de Estudios Superiores de Coacalco | Innovación y Tecnología para el Futuro</p>
                        <p>© 2027 Congreso Internacional TESCo - Todos los derechos reservados</p>
                    </div>
                </div>
            </body>
            </html>
            ";
            
            enviarCorreo($_POST['email'], $_POST['fullname'], $asunto, $cuerpo);
            
            header("Location: ../views/login.php?msg=registered");
        } else {
            header("Location: ../views/registro.php?error=1");
        }
    } 
    elseif($action == 'login') {
        $user = $userModel->findByEmail($_POST['email']);
        if($user && password_verify($_POST['password'], $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['email'] = $user['email'];
            
            if($user['role'] == 'admin') {
                header("Location: ../views/admin/dashboard.php");
            } elseif($user['role'] == 'reviewer') {
                header("Location: ../views/revisor/dashboard.php");
            } else {
                header("Location: ../views/dashboard_usuario.php");
            }
        } else {
            header("Location: ../views/login.php?error=1");
        }
    }
}
?>