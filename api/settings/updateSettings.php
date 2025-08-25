<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { exit(); }
$database = new Database(); $db = $database->connect();
if ($db) {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data) { http_response_code(400); echo json_encode(['message' => 'Invalid data.']); exit(); }
    $query = 'INSERT INTO settings (name, value) VALUES (:name, :value) ON DUPLICATE KEY UPDATE value = :value';
    $stmt = $db->prepare($query);
    try {
        foreach ($data as $name => $value) { $stmt->bindParam(':name', $name); $stmt->bindParam(':value', $value); $stmt->execute(); }
        http_response_code(200); echo json_encode(['message' => 'Settings updated.']);
    } catch (PDOException $e) { http_response_code(500); echo json_encode(['message' => 'Database Error: ' . $e->getMessage()]); }
}
?>