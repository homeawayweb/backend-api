<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
$id = isset($_GET['id']) ? $_GET['id'] : die();
$database = new Database(); $db = $database->connect();
if ($db) {
    $query = 'SELECT * FROM projects WHERE id = :id';
    $stmt = $db->prepare($query); $stmt->bindParam(':id', $id); $stmt->execute();
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$project) { http_response_code(404); echo json_encode(['message' => 'Project not found.']); exit(); }
    $gallery_query = 'SELECT id, image_url, caption FROM project_images WHERE project_id = :id';
    $gallery_stmt = $db->prepare($gallery_query); $gallery_stmt->bindParam(':id', $id); $gallery_stmt->execute();
    $project['overview'] = json_decode($project['overview']) ?? []; $project['technologies'] = json_decode($project['technologies']) ?? []; $project['gallery'] = $gallery_stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
    echo json_encode($project);
}
?>