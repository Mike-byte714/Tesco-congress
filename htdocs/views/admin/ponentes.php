<?php 
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}
include '../partials/header.php'; 

require_once '../../config/database.php';
$db = (new Database())->getConnection();
$ponentes = $db->query("SELECT * FROM speakers ORDER BY id")->fetchAll();
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color: #003366;"><i class="fas fa-microphone-alt"></i> Ponentes Internacionales</h2>
        <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver al Dashboard</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr><th>ID</th><th>Nombre</th><th>Institución</th><th>País</th><th>Conferencia</th></tr>
            </thead>
            <tbody>
                <?php foreach($ponentes as $p): ?>
                <tr>
                    <td><?php echo $p['id']; ?></td>
                    <td><?php echo htmlspecialchars($p['name']); ?></td>
                    <td><?php echo htmlspecialchars($p['institution']); ?></td>
                    <td><?php echo htmlspecialchars($p['country']); ?></td>
                    <td><?php echo htmlspecialchars($p['conference_title']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../partials/footer.php'; ?>