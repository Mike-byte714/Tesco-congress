<?php
session_start();
require_once '../config/database.php';
$db = (new Database())->getConnection();

if(isset($_POST['completar_perfil'])) {
    $email = $_POST['user_email'];
    $institution = $_POST['institution'];
    $country = $_POST['country'];
    $conference_title = $_POST['conference_title'];
    $bio = $_POST['bio'];
    
    // Verificar si ya existe
    $stmt = $db->prepare("SELECT * FROM speakers WHERE email = ?");
    $stmt->execute([$email]);
    
    if($stmt->fetch()) {
        // Actualizar
        $update = $db->prepare("UPDATE speakers SET institution=?, country=?, conference_title=?, bio=? WHERE email=?");
        $update->execute([$institution, $country, $conference_title, $bio, $email]);
    } else {
        // Insertar
        $insert = $db->prepare("INSERT INTO speakers (name, institution, country, conference_title, bio, email) VALUES (?, ?, ?, ?, ?, ?)");
        $insert->execute([$_SESSION['fullname'], $institution, $country, $conference_title, $bio, $email]);
    }
    
    header("Location: ../views/dashboard_ponente.php?msg=actualizado");
    exit;
}
?>