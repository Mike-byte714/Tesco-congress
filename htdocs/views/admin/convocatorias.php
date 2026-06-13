<?php 
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}
include '../partials/header.php'; 

require_once '../../config/database.php';
$db = (new Database())->getConnection();
$convocatorias = $db->query("SELECT * FROM calls ORDER BY id DESC")->fetchAll();
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color: #003366;"><i class="fas fa-calendar-alt"></i> Convocatorias</h2>
        <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver al Dashboard</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr><th>ID</th><th>Título</th><th>Descripción</th><th>Inicio</th><th>Fin</th><th>Activa</th></tr>
            </thead>
            <tbody>
                <?php foreach($convocatorias as $c): ?>
                <tr>
                    <td><?php echo $c['id']; ?></td>
                    <td><?php echo htmlspecialchars($c['title']); ?></td>
                    <td><?php echo htmlspecialchars($c['description']); ?></td>
                    <td><?php echo $c['start_date']; ?></td>
                    <td><?php echo $c['end_date']; ?></td>
                    <td><?php echo $c['is_active'] ? '✅ Sí' : '❌ No'; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../partials/footer.php'; ?>