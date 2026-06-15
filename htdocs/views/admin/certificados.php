<?php 
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}
include '../partials/header.php'; 

require_once '../../config/database.php';
$db = (new Database())->getConnection();
$usuarios = $db->query("SELECT id, fullname, email, role FROM users ORDER BY id")->fetchAll();
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color: #003366;"><i class="fas fa-certificate"></i> Generar Certificados</h2>
        <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver al Dashboard</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Acción</th></tr>
            </thead>
            <tbody>
                <?php foreach($usuarios as $u): ?>
                <tr>
                    <td><?php echo $u['id']; ?></td>
                    <td><?php echo htmlspecialchars($u['fullname']); ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td><?php echo $u['role']; ?></td>
                    <td>
                        <a href="../../controllers/CertificateController.php?user_id=<?php echo $u['id']; ?>&type=<?php echo $u['role']; ?>" class="btn btn-success btn-sm" target="_blank">📜 Generar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../partials/footer.php'; ?>