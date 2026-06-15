<?php include '../partials/header.php'; 
if($_SESSION['role']!='reviewer') header("Location: ../login.php");
require_once '../../config/database.php';
$db = (new Database())->getConnection();
$reviewerId = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT p.id, p.title, p.status, r.comments FROM projects p JOIN project_reviewers pr ON p.id=pr.project_id LEFT JOIN reviews r ON p.id=r.project_id AND r.reviewer_id=? WHERE pr.reviewer_id=?");
$stmt->execute([$reviewerId, $reviewerId]);
$proyectos = $stmt->fetchAll();
?>
<h2>Proyectos Asignados para Revisión</h2>
<table class="table">
    <tr><th>Título</th><th>Estado</th><th>Acciones</th></tr>
    <?php foreach($proyectos as $p): ?>
    <tr>
        <td><?= $p['title'] ?></td>
        <td><?= $p['status'] ?></td>
        <td><a href="evaluar.php?id=<?= $p['id'] ?>" class="btn btn-primary">Evaluar</a></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php include '../partials/footer.php'; ?>