<?php
session_start();
require_once '../config/database.php';

$db = (new Database())->getConnection();

$credential = $_POST['credential'] ?? null;
$isRegister = isset($_POST['register']);

if (!$credential) {
    echo json_encode(['success' => false, 'message' => 'No se recibió credencial']);
    exit;
}

// Verificar token con Google
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/tokeninfo?id_token=' . $credential);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$user_data = json_decode($response, true);

if (!isset($user_data['email'])) {
    echo json_encode(['success' => false, 'message' => 'Token inválido']);
    exit;
}

$email = $user_data['email'];
$name = $user_data['name'] ?? $user_data['given_name'] ?? 'Usuario';
$google_id = $user_data['sub'];

// Verificar que el Client ID coincida
$expected_client_id = '358668615160-icec4contl9ssmmqtrdmctg7c7ntucb4.apps.googleusercontent.com';
if ($user_data['aud'] !== $expected_client_id && $user_data['azp'] !== $expected_client_id) {
    echo json_encode(['success' => false, 'message' => 'Client ID no coincide']);
    exit;
}

// Buscar usuario
$stmt = $db->prepare("SELECT * FROM users WHERE email = ? OR google_id = ?");
$stmt->execute([$email, $google_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['fullname'] = $user['fullname'];
    $_SESSION['username'] = $user['username'] ?? explode('@', $email)[0];
    
    $redirect = ($user['role'] == 'admin') ? '../views/admin/dashboard.php' : '../views/index.php';
    echo json_encode(['success' => true, 'redirect' => $redirect]);
} 
elseif ($isRegister) {
    $username = explode('@', $email)[0];
    $base_username = $username;
    $counter = 1;
    while (true) {
        $check = $db->prepare("SELECT id FROM users WHERE username = ?");
        $check->execute([$username]);
        if ($check->rowCount() == 0) break;
        $username = $base_username . $counter;
        $counter++;
    }
    
    $fullname = $name;
    $role = 'assistant';
    
    $stmt = $db->prepare("INSERT INTO users (username, fullname, email, role, google_id) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$username, $fullname, $email, $role, $google_id])) {
        $new_id = $db->lastInsertId();
        $_SESSION['user_id'] = $new_id;
        $_SESSION['role'] = $role;
        $_SESSION['fullname'] = $fullname;
        $_SESSION['username'] = $username;
        echo json_encode(['success' => true, 'redirect' => '../views/index.php']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al registrar usuario']);
    }
} 
else {
    echo json_encode(['success' => false, 'message' => 'Usuario no registrado. Regístrate primero.']);
}
?>