<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
$slug = isset($_GET['slug']) ? $_GET['slug'] : die();
$database = new Database(); $db = $database->connect();
if ($db) {
    $query = "SELECT * FROM posts WHERE slug = :slug AND status = 'published' LIMIT 1";
    $stmt = $db->prepare($query); $stmt->bindParam(':slug', $slug); $stmt->execute();
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$post) { http_response_code(404); echo json_encode(['message' => 'Post not found.']); exit(); }
    $post['is_featured'] = (bool)$post['is_featured'];
    $post['tags'] = json_decode($post['tags']) ?? [];
    $post['date'] = date('F j, Y', strtotime($post['published_at']));
    $post['author'] = ['name' => $post['author_name'], 'avatar' => '/src/assets/profile-picture.png', 'title' => 'Lead Web Developer', 'bio' => 'A creative expert.'];
    echo json_encode($post);
}
?>