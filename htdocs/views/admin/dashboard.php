<?php 
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}
include '../partials/header.php'; 

require_once '../../config/database.php';
$db = (new Database())->getConnection();

$totalUsuarios = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalProyectos = $db->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$totalPonentes = $db->query("SELECT COUNT(*) FROM speakers")->fetchColumn();
$proyectosPendientes = $db->query("SELECT COUNT(*) FROM projects WHERE status='recibido' OR status='en_revision'")->fetchColumn();
?>

<style>
    .admin-wrapper { background: #f8fafc; min-height: calc(100vh - 200px); padding: 2rem 0; }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem; }
    .stat-card { background: white; border-radius: 20px; padding: 1.5rem; text-align: center; box-shadow: 0 8px 20px rgba(0,0,0,0.05); }
    .stat-number { font-size: 2.2rem; font-weight: 800; color: #003366; }
    .stat-label { color: #2e7d32; font-weight: 500; }
    .actions-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.2rem; margin-top: 1rem; }
    .action-card { background: white; border-radius: 16px; padding: 1rem; text-align: center; text-decoration: none; border: 1px solid #e2e8f0; display: block; }
    .action-card i { font-size: 1.5rem; color: #003366; }
    .action-card span { font-weight: 600; color: #1e293b; display: block; margin-top: 8px; }
    .action-card:hover { border-color: #2e7d32; background: #f8fafc; }
    .welcome-card { background: linear-gradient(135deg, #003366, #001a33); border-radius: 20px; padding: 1.5rem; margin-bottom: 2rem; color: white; display: flex; justify-content: space-between; flex-wrap: wrap; }
    @media (max-width: 768px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } .actions-grid { grid-template-columns: 1fr; } .welcome-card { flex-direction: column; text-align: center; gap: 1rem; } }
</style>

<div class="admin-wrapper">
    <div class="container">
        <div class="welcome-card">
            <div><h3><i class="fas fa-crown"></i> Bienvenido, <?php echo $_SESSION['fullname']; ?></h3><p>Panel de Administración del Congreso TESCo 2027</p></div>
            <div><i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y'); ?></div>
        </div>

        <div class="stats-grid">
            <div class="stat-card"><div class="stat-number"><?php echo $totalUsuarios; ?></div><div class="stat-label">Usuarios</div></div>
            <div class="stat-card"><div class="stat-number"><?php echo $totalProyectos; ?></div><div class="stat-label">Proyectos</div></div>
            <div class="stat-card"><div class="stat-number"><?php echo $proyectosPendientes; ?></div><div class="stat-label">Pendientes</div></div>
            <div class="stat-card"><div class="stat-number"><?php echo $totalPonentes; ?></div><div class="stat-label">Ponentes</div></div>
        </div>

        <h4 class="mb-3" style="color: #003366;"><i class="fas fa-tachometer-alt"></i> Gestión del Congreso</h4>
        <div class="actions-grid">
            <a href="usuarios.php" class="action-card"><i class="fas fa-users"></i><span>Gestión de Usuarios</span></a>
            <a href="proyectos.php" class="action-card"><i class="fas fa-file-alt"></i><span>Gestión de Proyectos</span></a>
            <a href="ponentes.php" class="action-card"><i class="fas fa-microphone-alt"></i><span>Ponentes</span></a>
            <a href="convocatorias.php" class="action-card"><i class="fas fa-calendar-alt"></i><span>Convocatorias</span></a>
            <a href="certificados.php" class="action-card"><i class="fas fa-certificate"></i><span>Generar Certificados</span></a>
            <a href="estadisticas.php" class="action-card"><i class="fas fa-chart-line"></i><span>Estadísticas</span></a>
        </div>

        <div class="text-center mt-4"><a href="../index.php" style="color: #2e7d32;"><i class="fas fa-globe"></i> Ver sitio público</a></div>
    </div>
</div>

<?php include '../partials/footer.php'; ?>