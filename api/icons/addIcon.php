<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { exit(); }
$database = new Database();
$db = $database->connect();
if ($db) {
    $data = json_decode(file_get_contents("php://input"));
    if (!$data || empty($data->name)) { http_response_code(400); echo json_encode(['message' => 'Icon name is required.']); exit(); }
    $query = 'INSERT INTO icons SET name = :name';
    try {
        $stmt = $db->prepare($query);
        $name = htmlspecialchars(strip_tags($data->name));
        $stmt->bindParam(':name', $name);
        if ($stmt->execute()) { http_response_code(201); echo json_encode(['message' => 'Icon added successfully.']); }
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) { http_response_code(409); echo json_encode(['message' => 'This icon name already exists.']); }
        else { http_response_code(500); echo json_encode(['message' => 'Database Error: ' . $e->getMessage()]); }
    }
}
?>