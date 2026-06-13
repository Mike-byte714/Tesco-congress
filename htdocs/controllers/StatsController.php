<?php
header('Content-Type: application/json');
require_once '../config/database.php';
$db = (new Database())->getConnection();

$action = $_GET['action'] ?? '';
if ($action === 'dashboard') {
    $participants = $db->query("SELECT COUNT(*) FROM users WHERE role='assistant'")->fetchColumn();
    $projects = $db->query("SELECT COUNT(*) FROM projects")->fetchColumn();
    $countries = $db->query("SELECT COUNT(DISTINCT country) FROM users WHERE country IS NOT NULL")->fetchColumn();
    $institutions = $db->query("SELECT COUNT(DISTINCT institution) FROM users WHERE institution IS NOT NULL")->fetchColumn();
    echo json_encode(['participants' => $participants, 'projects' => $projects, 'countries' => $countries, 'institutions' => $institutions]);
} elseif ($action === 'participantsByCountry') {
    $query = "SELECT c.country_name, c.lat, c.lng, COUNT(u.id) as count 
              FROM users u 
              JOIN countries_coords c ON u.country = c.country_name 
              GROUP BY u.country";
    $stmt = $db->query($query);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
?>