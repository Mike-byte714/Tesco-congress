<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../views/login.php");
    exit;
}

require_once '../config/database.php';
require_once '../models/User.php';
require_once '../models/Project.php';
require_once '../models/Speaker.php';

$db = (new Database())->getConnection();
$userModel = new User($db);
$projectModel = new Project($db);
$speakerModel = new Speaker($db);

$action = $_GET['action'] ?? '';

// Gestión de usuarios
if ($action === 'delete_user' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $db->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
    $stmt->execute([$id]);
    header("Location: ../views/admin/usuarios.php?msg=eliminado");
    exit;
}

// Cambiar estado de proyecto
if ($action === 'update_project_status' && isset($_GET['id']) && isset($_GET['status'])) {
    $id = (int)$_GET['id'];
    $status = $_GET['status'];
    $projectModel->updateStatus($id, $status);
    header("Location: ../views/admin/proyectos.php?msg=actualizado");
    exit;
}

// Agregar/editar ponente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save_speaker') {
    $name = $_POST['name'];
    $institution = $_POST['institution'];
    $country = $_POST['country'];
    $bio = $_POST['bio'];
    $conference_title = $_POST['conference_title'];
    $photo = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/images/speakers/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $photo = $uploadDir . time() . '_' . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
    }
    $stmt = $db->prepare("INSERT INTO speakers (name, institution, country, bio, photo, conference_title) VALUES (?,?,?,?,?,?)");
    $stmt->execute([$name, $institution, $country, $bio, $photo, $conference_title]);
    header("Location: ../views/admin/ponentes.php?msg=agregado");
    exit;
}

// etc. (eliminar ponente, actualizar convocatoria)
?>