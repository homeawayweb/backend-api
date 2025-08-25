<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
$database = new Database(); $db = $database->connect();
if ($db) {
    $query = 'SELECT id, title, slug, content, image_url, category, tags, author_name as author, status, published_at as date FROM posts ORDER BY created_at DESC';
    $stmt = $db->prepare($query); $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($posts === false) { $posts = []; }
    foreach ($posts as $key => $post) {
        $posts[$key]['date'] = $post['date'] ? date('Y-m-d', strtotime($post['date'])) : null;
        $posts[$key]['tags'] = json_decode($post['tags']) ?? [];
    }
    echo json_encode($posts);
}
?>