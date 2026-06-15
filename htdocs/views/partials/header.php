<?php
// ============================================================
// HEADER PRINCIPAL - VERSIÓN CON MENÚ SEGÚN ROL
// ============================================================
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
    <title>TESCo Congress</title>
    <!-- Favicon (ícono de pestaña) -->
	<link rel="icon" type="image/jpeg" href="/assets/images/favicon.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Leaflet para el mapa -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <style>
        /* ============================================ */
        /* ESTILOS DEL HEADER                          */
        /* ============================================ */
        :root {
            --tesco-primary: #0f766e;
            --tesco-primary-dark: #0a5c55;
            --tesco-primary-light: #14b8a6;
        }

        #worldMap {
            height: 450px;
            width: 100%;
            border-radius: 20px;
            overflow: hidden;
            z-index: 1;
        }

        .main-header {
            background: var(--tesco-primary);
            color: white;
            padding: 12px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .main-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Logo con texto */
        .logo-link {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .logo-img {
            height: 45px;
            width: auto;
            transition: transform 0.3s ease;
        }
        .logo-text {
            color: white;
            font-size: 1.3rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .logo-link:hover .logo-img { transform: scale(1.05); }
        .logo-link:hover .logo-text { color: #ccfbf1; }
        
        /* Navegación */
        .nav-links {
            display: flex;
            gap: 20px;
            list-style: none;
            margin: 0;
            padding: 0;
            align-items: center;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
        }
        .nav-links a:hover { color: #ccfbf1; }
        
        /* Selector de idioma */
        .lang-select {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 50px;
            padding: 6px 12px;
            color: white;
            font-weight: 500;
            cursor: pointer;
            transition: 0.3s;
        }
        .lang-select:hover { background: rgba(255,255,255,0.3); }
        .lang-select option { background: var(--tesco-primary); color: white; }
        
        /* Botón modo oscuro */
        .dark-mode-btn {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.3s;
            color: white;
        }
        .dark-mode-btn:hover { background: rgba(255,255,255,0.3); transform: scale(1.05); }
        
        /* Menú hamburguesa */
        .menu-btn {
            display: none;
            background: rgba(255,255,255,0.2);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
        }
        
        /* Modo oscuro */
        body.dark-mode {
            background: #0f172a !important;
            color: #e2e8f0 !important;
        }
        body.dark-mode .main-header { background: #0a5c55 !important; }
        body.dark-mode .card,
        body.dark-mode .form-card,
        body.dark-mode .table-panel { background: #1e293b !important; color: #e2e8f0 !important; }
        body.dark-mode footer { background: #020617 !important; }
        
        /* Responsive */
        @media (max-width: 768px) {
            .main-header .container { flex-direction: column; gap: 12px; }
            .logo-img { height: 35px; }
            .logo-text { font-size: 1rem; }
            .logo-link { gap: 8px; }
            .menu-btn { display: block !important; margin: 0 auto; }
            .nav-links {
                display: none !important;
                width: 100%;
                flex-direction: column;
                text-align: center;
                gap: 12px;
                padding: 15px 0;
                background: #0f766e;
                border-radius: 16px;
                margin-top: 10px;
            }
            .nav-links.show { display: flex !important; }
            .nav-links li { width: 100%; }
            .nav-links a { display: block; padding: 8px; }
            #worldMap { height: 280px !important; }
        }
    </style>
</head>
<body>

<header class="main-header">
    <div class="container">
        <!-- Logo + texto -->
        <div class="logo">
            <a href="/views/index.php" class="logo-link">
                <img src="/assets/images/logo.jpg" alt="TESCo Congress" class="logo-img">
                <span class="logo-text">TESCo Congress</span>
            </a>
        </div>
        
        <!-- Botón menú hamburguesa -->
        <button class="menu-btn" onclick="toggleMenu()">
            <i class="fas fa-bars"></i>
        </button>
        
        <ul class="nav-links" id="navLinks">
            <!-- ============================================ -->
            <!-- ENLACES COMUNES PARA TODOS                   -->
            <!-- ============================================ -->
            <li><a href="/views/index.php"><?php echo $translations['nav_home']; ?></a></li>
            <li><a href="/views/programa.php"><?php echo $translations['nav_program']; ?></a></li>
            <li><a href="/views/biblioteca.php"><?php echo $translations['nav_biblioteca']; ?></a></li>
            
            <!-- ============================================ -->
            <!-- MENÚ SEGÚN ROL DEL USUARIO                   -->
            <!-- ============================================ -->
            
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <!-- 👑 ADMINISTRADOR -->
                <li><a href="/views/admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="/views/admin/ponentes.php"><i class="fas fa-microphone-alt"></i> Ponentes</a></li>
                <li><a href="/views/admin/usuarios.php"><i class="fas fa-users"></i> Usuarios</a></li>
                <li><a href="/views/admin/proyectos.php"><i class="fas fa-file-alt"></i> Proyectos</a></li>
                <li><a href="/controllers/LogoutController.php"><i class="fas fa-sign-out-alt"></i> Salir</a></li>
                
            <?php elseif(isset($_SESSION['role']) && $_SESSION['role'] == 'speaker'): ?>
                <!-- 🎤 PONENTE -->
                <li><a href="/views/dashboard_ponente.php"><i class="fas fa-microphone-alt"></i> Mi Panel</a></li>
                <li><a href="/views/enviar_proyecto.php"><i class="fas fa-paper-plane"></i> Enviar Proyecto</a></li>
                <li><a href="/controllers/LogoutController.php"><i class="fas fa-sign-out-alt"></i> Salir</a></li>
                
            <?php elseif(isset($_SESSION['user_id'])): ?>
                <!-- 👤 ASISTENTE (usuario normal) -->
                <li><a href="/views/dashboard_usuario.php"><i class="fas fa-user"></i> Mi Cuenta</a></li>
                <li><a href="/views/enviar_proyecto.php"><i class="fas fa-paper-plane"></i> Enviar Proyecto</a></li>
                <li><a href="/controllers/LogoutController.php"><i class="fas fa-sign-out-alt"></i> Salir</a></li>
                
            <?php else: ?>
                <!-- 🌍 VISITANTE (no logueado) -->
                <li><a href="/views/acceso.php"><i class="fas fa-sign-in-alt"></i> Ingresar</a></li>
                <li><a href="/views/acceso.php"><i class="fas fa-user-plus"></i> Registrarse</a></li>
            <?php endif; ?>
            
            <!-- ============================================ -->
            <!-- SELECTOR DE IDIOMA Y MODO OSCURO             -->
            <!-- ============================================ -->
            <li>
                <select id="langSwitcher" class="lang-select">
                    <option value="es" <?php echo $lang=='es' ? 'selected' : ''; ?>>🇪🇸 ES</option>
                    <option value="en" <?php echo $lang=='en' ? 'selected' : ''; ?>>🇬🇧 EN</option>
                    <option value="zh" <?php echo $lang=='zh' ? 'selected' : ''; ?>>🇨🇳 中文</option>
                </select>
            </li>
            <li>
                <button id="darkModeToggle" class="dark-mode-btn">
                    <i class="fas fa-moon"></i>
                </button>
            </li>
        </ul>
    </div>
</header>

<main class="container my-4">

<script>
// ============================================================
// FUNCIONES DEL HEADER
// ============================================================

// Menú hamburguesa
function toggleMenu() {
    const nav = document.getElementById('navLinks');
    if (nav) nav.classList.toggle('show');
}

// Modo oscuro
const darkModeToggle = document.getElementById('darkModeToggle');
if (darkModeToggle) {
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
        darkModeToggle.innerHTML = '<i class="fas fa-sun"></i>';
    }
    darkModeToggle.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
        if (document.body.classList.contains('dark-mode')) {
            localStorage.setItem('darkMode', 'enabled');
            darkModeToggle.innerHTML = '<i class="fas fa-sun"></i>';
        } else {
            localStorage.setItem('darkMode', 'disabled');
            darkModeToggle.innerHTML = '<i class="fas fa-moon"></i>';
        }
    });
}

// Selector de idioma
const langSwitcher = document.getElementById('langSwitcher');
if (langSwitcher) {
    langSwitcher.addEventListener('change', function() {
        window.location.href = '/controllers/LangController.php?lang=' + this.value;
    });
}
</script>