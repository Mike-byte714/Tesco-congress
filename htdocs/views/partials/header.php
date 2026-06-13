<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$lang = $_SESSION['lang'] ?? 'es';
$translations = [];
$lang_file = "../assets/lang/{$lang}.php";
if (file_exists($lang_file)) {
    $translations = include $lang_file;
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?php echo htmlspecialchars($translations['site_title'] ?? 'Congreso TESCo 2027'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body>

<header class="bg-primary text-white py-2 sticky-top">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="logo">
                <h2 class="fs-5 fs-md-4 fs-lg-3 mb-0">TESCo Congress 2027</h2>
            </div>

            <button class="btn btn-outline-light d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars"></i>
            </button>

            <div class="collapse d-md-block" id="navbarNav">
                <ul class="nav flex-column flex-md-row gap-2 gap-md-3 mt-3 mt-md-0">
                    <li class="nav-item"><a class="nav-link text-white" href="index.php"><?php echo htmlspecialchars($translations['nav_home'] ?? 'Inicio'); ?></a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="programa.php"><?php echo htmlspecialchars($translations['nav_program'] ?? 'Programa'); ?></a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="biblioteca.php">Biblioteca</a></li>
                    
                    <?php if(!isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link text-white" href="registro.php"><?php echo htmlspecialchars($translations['nav_register'] ?? 'Registro'); ?></a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="login.php"><?php echo htmlspecialchars($translations['nav_login'] ?? 'Iniciar Sesión'); ?></a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link text-white" href="dashboard_usuario.php"><?php echo htmlspecialchars($translations['dashboard'] ?? 'Mi Cuenta'); ?></a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="LogoutController.php">Cerrar Sesión</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="d-flex gap-2 align-items-center mt-2 mt-md-0">
                <div class="language">
                    <select id="langSwitcher" class="form-select form-select-sm bg-light text-dark" style="width: auto;">
                        <option value="es" <?php echo $lang=='es' ? 'selected' : ''; ?>>ES</option>
                        <option value="en" <?php echo $lang=='en' ? 'selected' : ''; ?>>EN</option>
                    </select>
                </div>
                <button id="darkModeToggle" class="btn btn-outline-light rounded-circle" style="width: 36px; height: 36px;">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
        </div>
    </div>
</header>

<main class="container my-4">