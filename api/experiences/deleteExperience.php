<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { exit(); }
$db = (new Database())->connect();
if ($db) {
    $data = json_decode(file_get_contents("php://input"));
    if (!$data || !isset($data->id)) { http_response_code(400); exit(); }
    $query = 'DELETE FROM experiences WHERE id = :id';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $data->id);
    if ($stmt->execute()) { http_response_code(200); echo json_encode(['message' => 'Experience deleted.']); }
}
?>