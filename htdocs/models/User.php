<?php
class User {
    private $conn;
    public function __construct($db) { $this->conn = $db; }
    public function create($email, $password, $fullname, $institution, $country, $role) {
        $sql = "INSERT INTO users (email, password, fullname, institution, country, role) VALUES (?,?,?,?,?,?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$email, $password, $fullname, $institution, $country, $role]);
    }
    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getAll() {
        $sql = "SELECT * FROM users";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Más métodos: update, delete, etc.
}
?>