<?php
session_start();

// Verificar que solo el admin pueda acceder
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Acceso denegado. Solo administradores.");
}

require_once '../config/database.php';

// Obtener parámetros
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$type = isset($_GET['type']) ? $_GET['type'] : 'assistant';

if($user_id == 0) {
    die("Error: No se especificó un usuario válido.");
}

$db = (new Database())->getConnection();

// Obtener datos del usuario
$stmt = $db->prepare("SELECT fullname, email, role FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$user) {
    die("Error: Usuario no encontrado.");
}

// Generar folio único
$folio = 'TESCo-' . strtoupper(uniqid());

// Crear directorio si no existe
$cert_dir = __DIR__ . '/../assets/uploads/constancias/';
if (!is_dir($cert_dir)) {
    mkdir($cert_dir, 0777, true);
}

// Tipo de participante
$tipo_texto = '';
switch($type) {
    case 'assistant': $tipo_texto = 'ASISTENTE'; break;
    case 'speaker': $tipo_texto = 'PONENTE'; break;
    case 'reviewer': $tipo_texto = 'REVISOR'; break;
    case 'organizer': $tipo_texto = 'ORGANIZADOR'; break;
    case 'admin': $tipo_texto = 'ORGANIZADOR'; break;
    default: $tipo_texto = 'PARTICIPANTE';
}

$nombre_completo = htmlspecialchars($user['fullname']);
$fecha = date('d/m/Y');

// Crear el contenido HTML de la constancia
$html_content = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Constancia TESCo 2027</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f0f0f0; }
        .certificate { width: 800px; margin: 50px auto; padding: 40px; border: 10px solid #003366; background: white; text-align: center; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .header { border-bottom: 2px solid #2e7d32; margin-bottom: 30px; padding-bottom: 20px; }
        .header h1 { color: #003366; margin: 0; font-size: 28px; }
        .header h3 { color: #2e7d32; margin: 10px 0 0; }
        .content { margin: 30px 0; }
        .content p { font-size: 18px; line-height: 1.8; }
        .nombre { font-size: 24px; font-weight: bold; color: #003366; margin: 20px 0; }
        .tipo { font-size: 20px; font-weight: bold; color: #2e7d32; margin: 10px 0; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #ccc; font-size: 12px; }
        .folio { font-size: 11px; color: #666; margin-top: 20px; }
        .sello { margin-top: 30px; }
        .btn-pdf { display: block; width: 200px; margin: 20px auto; padding: 10px; background: #2e7d32; color: white; text-align: center; text-decoration: none; border-radius: 5px; }
        .btn-pdf:hover { background: #1b5e20; }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="header">
            <h1>Congreso Internacional TESCo 2027</h1>
            <h3>Innovación y Tecnología para el Futuro</h3>
        </div>
        <div class="content">
            <p>El Tecnológico de Estudios Superiores de Coacalco</p>
            <p>otorga la presente constancia a:</p>
            <div class="nombre">' . $nombre_completo . '</div>
            <p>Por su valiosa participación como</p>
            <div class="tipo">' . $tipo_texto . '</div>
            <p>en el Congreso Internacional TESCo 2027, realizado del 15 al 17 de noviembre de 2027.</p>
        </div>
        <div class="sello">
            <p>_________________________</p>
            <p>Dra. María González<br>Directora General</p>
        </div>
        <div class="footer">
            <p>Tecnológico de Estudios Superiores de Coacalco</p>
            <p>' . $fecha . '</p>
        </div>
        <div class="folio">
            <p>Folio: ' . $folio . '</p>
        </div>
        <a href="javascript:window.print()" class="btn-pdf"><i class="fas fa-print"></i> Imprimir / Guardar como PDF</a>
    </div>
    <script>console.log("Constancia generada: ' . $folio . '");</script>
</body>
</html>';

// Mostrar directamente la constancia (sin redirección)
echo $html_content;

// Guardar en la base de datos (opcional, para registro)
try {
    $insert = $db->prepare("INSERT INTO certificates (user_id, type, unique_folio, qr_code) VALUES (?, ?, ?, ?)");
    $insert->execute([$user_id, $type, $folio, '']);
} catch(Exception $e) {
    // No hacer nada si falla, la constancia ya se mostró
}

exit;
?>