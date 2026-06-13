<?php include '../partials/header.php';
if($_SESSION['role']!='reviewer') header("Location: ../login.php");
$projectId = $_GET['id'];
require_once '../../config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$projectId]);
$proyecto = $stmt->fetch();
?>
<h2>Evaluar Proyecto: <?= $proyecto['title'] ?></h2>
<form action="../../controllers/ReviewerController.php?action=save_review" method="POST">
    <input type="hidden" name="project_id" value="<?= $projectId ?>">
    <div class="mb-3"><label>Originalidad (1-10)</label><input type="number" name="score_originality" min="1" max="10" class="form-control" required></div>
    <div class="mb-3"><label>Metodología (1-10)</label><input type="number" name="score_methodology" min="1" max="10" class="form-control" required></div>
    <div class="mb-3"><label>Relevancia (1-10)</label><input type="number" name="score_relevance" min="1" max="10" class="form-control" required></div>
    <div class="mb-3"><label>Claridad (1-10)</label><input type="number" name="score_clarity" min="1" max="10" class="form-control" required></div>
    <div class="mb-3"><label>Comentarios</label><textarea name="comments" rows="4" class="form-control"></textarea></div>
    <button type="submit" class="btn btn-primary">Guardar Evaluación</button>
</form>
<?php include '../partials/footer.php'; ?>