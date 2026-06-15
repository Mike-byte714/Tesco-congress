<?php 
// ============================================================
// ESTADÍSTICAS DEL CONGRESO
// ============================================================
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: /views/login.php");
    exit;
}

require_once '../../config/database.php';
$db = (new Database())->getConnection();

$totalUsuarios = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalProyectos = $db->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$totalAceptados = $db->query("SELECT COUNT(*) FROM projects WHERE status='aceptado'")->fetchColumn();
$totalRechazados = $db->query("SELECT COUNT(*) FROM projects WHERE status='rechazado'")->fetchColumn();
$totalPonentes = $db->query("SELECT COUNT(*) FROM speakers")->fetchColumn();
$totalPaises = $db->query("SELECT COUNT(DISTINCT country) FROM users WHERE country IS NOT NULL AND country != ''")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas - TESCo Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f8fafc; }
        .admin-header { background: #0f766e; color: white; padding: 15px 0; margin-bottom: 30px; }
        .admin-header .container { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; }
        .admin-header a { color: white; text-decoration: none; margin-left: 20px; }
        
        .stat-card { background: white; border-radius: 20px; padding: 1.5rem; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 1rem; }
        .stat-number { font-size: 2.5rem; font-weight: 800; color: #0f766e; }
        
        footer { background: #0f172a; color: white; text-align: center; padding: 20px; margin-top: 50px; }
        
        @media (max-width: 768px) {
            .admin-header .container { flex-direction: column; gap: 10px; text-align: center; }
            .stat-number { font-size: 1.8rem; }
        }
    </style>
</head>
<body>

<header class="admin-header">
    <div class="container">
        <h2><i class="fas fa-chart-line"></i> TESCo - Estadísticas</h2>
        <div><a href="/views/index.php">Inicio</a><a href="dashboard.php">Dashboard</a><a href="/controllers/LogoutController.php">Salir</a></div>
    </div>
</header>

<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color:#0f766e;"><i class="fas fa-chart-line"></i> Estadísticas del Congreso</h2>
        <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3"><div class="stat-card"><div class="stat-number"><?php echo $totalUsuarios; ?></div><p>Usuarios Registrados</p></div></div>
        <div class="col-md-4 mb-3"><div class="stat-card"><div class="stat-number"><?php echo $totalProyectos; ?></div><p>Total Proyectos</p></div></div>
        <div class="col-md-4 mb-3"><div class="stat-card"><div class="stat-number"><?php echo $totalAceptados; ?></div><p>Proyectos Aceptados</p></div></div>
        <div class="col-md-4 mb-3"><div class="stat-card"><div class="stat-number"><?php echo $totalRechazados; ?></div><p>Proyectos Rechazados</p></div></div>
        <div class="col-md-4 mb-3"><div class="stat-card"><div class="stat-number"><?php echo $totalPonentes; ?></div><p>Ponentes</p></div></div>
        <div class="col-md-4 mb-3"><div class="stat-card"><div class="stat-number"><?php echo $totalPaises; ?></div><p>Países Participantes</p></div></div>
    </div>
</div>

<footer><p>&copy; 2027 TESCo Congress</p></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>