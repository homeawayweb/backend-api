<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { exit(); }
$database = new Database(); $db = $database->connect();
if ($db) {
    $data = json_decode(file_get_contents("php://input"));
    if (!$data || !isset($data->id)) { http_response_code(400); echo json_encode(['message' => 'Post ID is required.']); exit(); }
    $query = 'DELETE FROM posts WHERE id = :id';
    $stmt = $db->prepare($query); $stmt->bindParam(':id', $data->id, PDO::PARAM_INT);
    if ($stmt->execute()) { http_response_code(200); echo json_encode(['message' => 'Post deleted successfully.']); }
}
?>