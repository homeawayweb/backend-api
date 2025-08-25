<?php
// File Path: portfolio-api/api/messages/updateMessageStatus.php
require_once '../../config/cors.php';
require_once '../../config/database.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { exit(); }

$database = new Database();
$db = $database->connect();

if ($db) {
    $data = json_decode(file_get_contents("php://input"));
    if (!$data || !isset($data->id) || !isset($data->is_read)) {
        http_response_code(400); echo json_encode(['message' => 'Incomplete data.']); exit();
    }
    
    $query = 'UPDATE messages SET is_read = :is_read WHERE id = :id';
    $stmt = $db->prepare($query);

    $id = intval($data->id);
    $is_read = (bool)$data->is_read ? 1 : 0; // Convert boolean to 1 or 0 for database

    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':is_read', $is_read, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(['message' => 'Message status updated.']);
    } else {
        http_response_code(503);
        echo json_encode(['message' => 'Failed to update message status.']);
    }
}
?>