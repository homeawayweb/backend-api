<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { exit(); }
$db = (new Database())->connect();
if ($db) {
    $data = json_decode(file_get_contents("php://input"));
    if (!$data || empty($data->role) || empty($data->company)) { http_response_code(400); exit(); }
    $query = 'INSERT INTO experiences SET role = :role, company = :company, period = :period, description = :description';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':role', $data->role);
    $stmt->bindParam(':company', $data->company);
    $stmt->bindParam(':period', $data->period);
    $stmt->bindParam(':description', $data->description);
    if ($stmt->execute()) { http_response_code(201); echo json_encode(['message' => 'Experience added.']); }
}
?>