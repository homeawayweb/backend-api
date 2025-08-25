<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { exit(); }
$database = new Database(); $db = $database->connect();
if ($db) {
    $data = json_decode(file_get_contents("php://input"));
    if (empty($data->title) || empty($data->domain) || empty($data->description)) { http_response_code(400); echo json_encode(['message' => 'Incomplete data.']); exit(); }
    $query = 'INSERT INTO projects SET title = :title, domain = :domain, description = :description, overview = :overview, technologies = :technologies, image_url = :image_url, live_link = :live_link, source_link = :source_link';
    $stmt = $db->prepare($query);
    $overview = !empty($data->overview) ? json_encode($data->overview) : null; $technologies = !empty($data->technologies) ? json_encode($data->technologies) : null;
    $stmt->bindParam(':title', htmlspecialchars(strip_tags($data->title))); $stmt->bindParam(':domain', htmlspecialchars(strip_tags($data->domain))); $stmt->bindParam(':description', htmlspecialchars(strip_tags($data->description))); $stmt->bindParam(':overview', $overview); $stmt->bindParam(':technologies', $technologies); $stmt->bindParam(':image_url', htmlspecialchars(strip_tags($data->image_url))); $stmt->bindParam(':live_link', htmlspecialchars(strip_tags($data->live_link))); $stmt->bindParam(':source_link', htmlspecialchars(strip_tags($data->source_link)));
    if ($stmt->execute()) { http_response_code(201); echo json_encode(['message' => 'Project created.']); }
}
?>