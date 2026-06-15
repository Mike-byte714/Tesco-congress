<?php 
// ============================================================
// PANEL DE ADMINISTRACIÓN - DASHBOARD PRINCIPAL
// ============================================================
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: /views/login.php");
    exit;
}

require_once '../../config/database.php';
$db = (new Database())->getConnection();

// Estadísticas para el dashboard
$totalUsuarios = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalProyectos = $db->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$totalPonentes = $db->query("SELECT COUNT(*) FROM speakers")->fetchColumn();
$proyectosPendientes = $db->query("SELECT COUNT(*) FROM projects WHERE status='recibido' OR status='en_revision'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TESCo Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* ============================================ */
        /* ESTILOS DEL DASHBOARD (DISEÑO ORIGINAL)     */
        /* ============================================ */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #f8fafc; font-family: 'Segoe UI', system-ui, sans-serif; }
        
        /* Header sencillo */
        .admin-header {
            background: #0f766e;
            color: white;
            padding: 15px 0;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .admin-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .admin-header h2 { margin: 0; font-size: 1.5rem; }
        .admin-header a { color: white; text-decoration: none; margin-left: 20px; }
        .admin-header a:hover { text-decoration: underline; }
        
        /* Tarjeta de bienvenida */
        .welcome-card {
            background: linear-gradient(135deg, #0f766e, #0a5c55);
            border-radius: 20px;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        /* Grid de estadísticas */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-number { font-size: 2.2rem; font-weight: 800; color: #0f766e; }
        .stat-label { color: #2e7d32; font-weight: 500; }
        
        /* Grid de botones de acción */
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.2rem;
            margin: 1.5rem 0;
        }
        .action-card {
            background: white;
            border-radius: 16px;
            padding: 1.2rem;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
            display: block;
        }
        .action-card i { font-size: 1.8rem; color: #0f766e; display: block; margin-bottom: 0.5rem; }
        .action-card span { font-weight: 600; color: #1e293b; }
        .action-card:hover { transform: translateY(-3px); border-color: #2e7d32; background: #f8fafc; }
        
        /* Footer */
        footer { background: #0f172a; color: white; text-align: center; padding: 20px; margin-top: 50px; }
        
        /* ========== RESPONSIVE ========== */
        @media (max-width: 992px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 1rem; }
            .actions-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .admin-header .container { flex-direction: column; gap: 10px; text-align: center; }
            .welcome-card { flex-direction: column; text-align: center; gap: 10px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .stat-number { font-size: 1.5rem; }
            .actions-grid { grid-template-columns: 1fr; gap: 10px; }
            .action-card { padding: 0.8rem; }
            .action-card i { font-size: 1.3rem; }
        }
        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- ============================================================ -->
<!-- HEADER SENCILLO DEL ADMIN -->
<!-- ============================================================ -->
<header class="admin-header">
    <div class="container">
        <h2><i class="fas fa-tachometer-alt"></i> TESCo Admin</h2>
        <div>
            <a href="/views/index.php"><i class="fas fa-home"></i> Inicio</a>
            <a href="/controllers/LogoutController.php"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </div>
    </div>
</header>

<!-- ============================================================ -->
<!-- CONTENIDO PRINCIPAL DEL DASHBOARD -->
<!-- ============================================================ -->
<div class="container py-3">
    
    <!-- Tarjeta de bienvenida -->
    <div class="welcome-card">
        <div>
            <h3><i class="fas fa-crown"></i> Bienvenido, <?php echo $_SESSION['fullname']; ?></h3>
            <p>Panel de Administración del Congreso TESCo</p>
        </div>
        <div>
            <i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y'); ?>
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?php echo $totalUsuarios; ?></div>
            <div class="stat-label">Usuarios Registrados</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $totalProyectos; ?></div>
            <div class="stat-label">Proyectos Recibidos</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $proyectosPendientes; ?></div>
            <div class="stat-label">Proyectos Pendientes</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $totalPonentes; ?></div>
            <div class="stat-label">Ponentes</div>
        </div>
    </div>

    <!-- Panel de administración con enlaces -->
    <h4 class="mb-3" style="color: #0f766e;"><i class="fas fa-tachometer-alt"></i> Gestión del Congreso</h4>
    <div class="actions-grid">
        <a href="usuarios.php" class="action-card"><i class="fas fa-users"></i><span>Gestión de Usuarios</span></a>
        <a href="proyectos.php" class="action-card"><i class="fas fa-file-alt"></i><span>Gestión de Proyectos</span></a>
        <a href="ponentes.php" class="action-card"><i class="fas fa-microphone-alt"></i><span>Ponentes</span></a>
        <a href="convocatorias.php" class="action-card"><i class="fas fa-calendar-alt"></i><span>Convocatorias</span></a>
        <a href="certificados.php" class="action-card"><i class="fas fa-certificate"></i><span>Generar Certificados</span></a>
        <a href="estadisticas.php" class="action-card"><i class="fas fa-chart-line"></i><span>Estadísticas</span></a>
    </div>

    <!-- Enlace al sitio público -->
    <div class="text-center mt-4">
        <a href="../index.php" style="color: #2e7d32;"><i class="fas fa-globe"></i> Ver sitio público del congreso</a>
    </div>
</div>

<!-- ============================================================ -->
<!-- FOOTER -->
<!-- ============================================================ -->
<footer>
    <p>&copy; 2027 TESCo Congress - Todos los derechos reservados</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>