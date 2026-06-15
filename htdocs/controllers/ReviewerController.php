<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'reviewer') {
    header("Location: ../views/login.php");
    exit;
}

require_once '../config/database.php';
$db = (new Database())->getConnection();
$reviewerId = $_SESSION['user_id'];

// Obtener proyectos asignados
$query = "SELECT p.*, r.comments, r.score_originality, r.score_methodology, r.score_relevance, r.score_clarity 
          FROM projects p 
          JOIN project_reviewers pr ON p.id = pr.project_id 
          LEFT JOIN reviews r ON p.id = r.project_id AND r.reviewer_id = ?
          WHERE pr.reviewer_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$reviewerId, $reviewerId]);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Guardar evaluación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'save_review') {
    $project_id = $_POST['project_id'];
    $score_originality = $_POST['score_originality'];
    $score_methodology = $_POST['score_methodology'];
    $score_relevance = $_POST['score_relevance'];
    $score_clarity = $_POST['score_clarity'];
    $comments = $_POST['comments'];
    
    $check = $db->prepare("SELECT id FROM reviews WHERE project_id = ? AND reviewer_id = ?");
    $check->execute([$project_id, $reviewerId]);
    if ($check->rowCount() > 0) {
        $update = $db->prepare("UPDATE reviews SET score_originality=?, score_methodology=?, score_relevance=?, score_clarity=?, comments=? WHERE project_id=? AND reviewer_id=?");
        $update->execute([$score_originality, $score_methodology, $score_relevance, $score_clarity, $comments, $project_id, $reviewerId]);
    } else {
        $insert = $db->prepare("INSERT INTO reviews (project_id, reviewer_id, score_originality, score_methodology, score_relevance, score_clarity, comments) VALUES (?,?,?,?,?,?,?)");
        $insert->execute([$project_id, $reviewerId, $score_originality, $score_methodology, $score_relevance, $score_clarity, $comments]);
    }
    // Actualizar estado del proyecto a "en_revision" si no lo estaba
    $db->prepare("UPDATE projects SET status = 'en_revision' WHERE id = ?")->execute([$project_id]);
    header("Location: ../views/revisor/dashboard.php?msg=evaluacion_guardada");
    exit;
}
?>