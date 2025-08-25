<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { exit(); }
$database = new Database(); $db = $database->connect();
if ($db) {
    $data = json_decode(file_get_contents("php://input"));
    if (!$data || !isset($data->id) || empty($data->title) || empty($data->content)) { http_response_code(400); echo json_encode(['message' => 'ID, title and content are required.']); exit(); }
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data->title))) . '-' . time();
    $published_at = ($data->status === 'Published' && empty($data->date)) ? date('Y-m-d H:i:s') : ($data->date ?? null);
    $query = 'UPDATE posts SET title = :title, slug = :slug, content = :content, image_url = :image_url, category = :category, author_name = :author, status = :status, published_at = :published_at, tags = :tags WHERE id = :id';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $data->id, PDO::PARAM_INT); $stmt->bindParam(':title', $data->title); $stmt->bindParam(':slug', $slug); $stmt->bindParam(':content', $data->content); $stmt->bindParam(':image_url', $data->image_url); $stmt->bindParam(':category', $data->category); $stmt->bindParam(':author', $data->author); $stmt->bindParam(':status', $data->status); $stmt->bindParam(':published_at', $published_at);
    $tags = json_encode($data->tags ?? []); $stmt->bindParam(':tags', $tags);
    if ($stmt->execute()) { http_response_code(200); echo json_encode(['message' => 'Post updated successfully.']); }
}
?>