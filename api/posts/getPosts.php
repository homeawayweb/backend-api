<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
$database = new Database(); $db = $database->connect();
if ($db) {
    $query = "SELECT id, slug, is_featured, title, excerpt, author_name, published_at, category, image_url FROM posts WHERE status = 'published' ORDER BY is_featured DESC, published_at DESC";
    $stmt = $db->prepare($query); $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($posts === false) { $posts = []; }
    foreach ($posts as $key => $post) {
        $posts[$key]['is_featured'] = (bool)$post['is_featured'];
        $posts[$key]['date'] = date('F j, Y', strtotime($post['published_at']));
    }
    echo json_encode($posts);
}
?>