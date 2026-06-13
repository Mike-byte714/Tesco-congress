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
$totalAceptados = $db->query("SELECT COUNT(*) FROM projects WHERE status='aceptado'")->fetchColumn();
$totalRechazados = $db->query("SELECT COUNT(*) FROM projects WHERE status='rechazado'")->fetchColumn();
$totalPonentes = $db->query("SELECT COUNT(*) FROM speakers")->fetchColumn();
$paises = $db->query("SELECT COUNT(DISTINCT country) FROM users WHERE country IS NOT NULL")->fetchColumn();
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color: #003366;"><i class="fas fa-chart-line"></i> Estadísticas</h2>
        <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver al Dashboard</a>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3"><div class="card text-center p-3"><h3><?php echo $totalUsuarios; ?></h3><p>Usuarios</p></div></div>
        <div class="col-md-4 mb-3"><div class="card text-center p-3"><h3><?php echo $totalProyectos; ?></h3><p>Proyectos</p></div></div>
        <div class="col-md-4 mb-3"><div class="card text-center p-3"><h3><?php echo $totalAceptados; ?></h3><p>Aceptados</p></div></div>
        <div class="col-md-4 mb-3"><div class="card text-center p-3"><h3><?php echo $totalRechazados; ?></h3><p>Rechazados</p></div></div>
        <div class="col-md-4 mb-3"><div class="card text-center p-3"><h3><?php echo $totalPonentes; ?></h3><p>Ponentes</p></div></div>
        <div class="col-md-4 mb-3"><div class="card text-center p-3"><h3><?php echo $paises; ?></h3><p>Países</p></div></div>
    </div>
</div>

<?php include '../partials/footer.php'; ?>