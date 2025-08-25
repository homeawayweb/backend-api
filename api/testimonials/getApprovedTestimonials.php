<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
$database = new Database(); $db = $database->connect();
if ($db) {
    $query = "SELECT id, author_name as name, author_title as title, quote, avatar_url as avatar FROM testimonials WHERE status = 'Approved' ORDER BY created_at DESC";
    $stmt = $db->prepare($query); $stmt->execute();
    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($testimonials);
}
?>