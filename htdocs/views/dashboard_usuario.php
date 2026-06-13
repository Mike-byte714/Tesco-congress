<?php include 'partials/header.php'; 
if(!isset($_SESSION['user_id'])) header("Location: login.php");
$userId = $_SESSION['user_id'];
?>
<div class="row">
    <div class="col-md-8">
        <h3>Mis Proyectos</h3>
        <?php
        require_once '../config/database.php';
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("SELECT * FROM projects WHERE user_id = ? ORDER BY submitted_at DESC");
        $stmt->execute([$userId]);
        $proyectos = $stmt->fetchAll();
        if(count($proyectos)==0) echo "<p>Aún no has enviado proyectos.</p>";
        foreach($proyectos as $p): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5><?= $p['title'] ?></h5>
                    <p>Estado: <strong><?= $p['status'] ?></strong></p>
                    <a href="<?= $p['file_path'] ?>" target="_blank" class="btn btn-sm btn-secondary">Ver PDF</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5>Bienvenido, <?= $_SESSION['fullname'] ?></h5>
                <a href="enviar_proyecto.php" class="btn btn-success w-100">Enviar nuevo proyecto</a>
                <a href="../controllers/LogoutController.php" class="btn btn-danger w-100 mt-2">Cerrar sesión</a>
            </div>
        </div>
    </div>
</div>
<?php include 'partials/footer.php'; ?>