<?php
class Project {
    private $conn;
    public function __construct($db) { $this->conn = $db; }
    public function create($userId, $title, $abstract, $authors, $institution, $country, $area, $filePath) {
        $sql = "INSERT INTO projects (user_id, title, abstract, authors, institution, country, area, file_path) VALUES (?,?,?,?,?,?,?,?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$userId, $title, $abstract, $authors, $institution, $country, $area, $filePath]);
    }
    public function getAll() {
        $sql = "SELECT * FROM projects";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function updateStatus($id, $status) {
        $sql = "UPDATE projects SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$status, $id]);
    }
}
?>