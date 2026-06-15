<?php
require_once '../config/database.php';
require_once '../models/Project.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'submit') {
    $userId = $_SESSION['user_id'] ?? 0;
    if (!$userId) {
        header("Location: ../views/login.php?error=debes_iniciar_sesion");
        exit;
    }

    $title = $_POST['title'] ?? '';
    $abstract = $_POST['abstract'] ?? '';
    $authors = $_POST['authors'] ?? '';
    $institution = $_POST['institution'] ?? '';
    $country = $_POST['country'] ?? '';
    $area = $_POST['area'] ?? '';

    // Validar archivo PDF
    if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
        header("Location: ../views/enviar_proyecto.php?error=archivo_no_subido");
        exit;
    }
    $file = $_FILES['pdf_file'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'pdf') {
        header("Location: ../views/enviar_proyecto.php?error=solo_pdf");
        exit;
    }
    $allowedSize = 5 * 1024 * 1024; // 5 MB
    if ($file['size'] > $allowedSize) {
        header("Location: ../views/enviar_proyecto.php?error=archivo_grande");
        exit;
    }

    $uploadDir = '../assets/uploads/proyectos/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $newFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
    $destino = $uploadDir . $newFileName;

    if (move_uploaded_file($file['tmp_name'], $destino)) {
        $db = (new Database())->getConnection();
        $projectModel = new Project($db);
        $result = $projectModel->create($userId, $title, $abstract, $authors, $institution, $country, $area, $destino);
        if ($result) {
            header("Location: ../views/dashboard_usuario.php?msg=proyecto_enviado");
        } else {
            header("Location: ../views/enviar_proyecto.php?error=db_error");
        }
    } else {
        header("Location: ../views/enviar_proyecto.php?error=no_mover");
    }
    exit;
}
?>