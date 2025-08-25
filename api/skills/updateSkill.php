<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { exit(); }
$database = new Database();
$db = $database->connect();
if ($db) {
    $data = json_decode(file_get_contents("php://input"));
    if (!$data || !isset($data->id) || empty($data->name) || empty($data->domain) || !isset($data->proficiency)) {
        http_response_code(400); echo json_encode(['message' => 'Incomplete data.']); exit();
    }
    $query = 'UPDATE skills SET name = :name, domain = :domain, proficiency = :proficiency, icon = :icon WHERE id = :id';
    try {
        $stmt = $db->prepare($query);
        $id = intval($data->id);
        $name = htmlspecialchars(strip_tags($data->name));
        $domain = htmlspecialchars(strip_tags($data->domain));
        $proficiency = intval($data->proficiency);
        $icon = htmlspecialchars(strip_tags($data->icon));
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':domain', $domain);
        $stmt->bindParam(':proficiency', $proficiency, PDO::PARAM_INT);
        $stmt->bindParam(':icon', $icon);
        if ($stmt->execute()) {
            http_response_code(200); echo json_encode(['message' => 'Skill updated successfully.']);
        }
    } catch (PDOException $e) {
        http_response_code(500); echo json_encode(['message' => 'Database Error: ' . $e->getMessage()]);
    }
}
?>