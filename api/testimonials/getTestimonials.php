<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
$database = new Database(); $db = $database->connect();
if ($db) {
    $query = 'SELECT id, author_name, author_title, quote, avatar_url as avatar, rating, country, status, created_at as date FROM testimonials ORDER BY created_at DESC';
    $stmt = $db->prepare($query); $stmt->execute();
    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($testimonials as $key => $testimonial) {
        $testimonials[$key]['client'] = $testimonial['author_name'];
        $testimonials[$key]['text'] = $testimonial['quote'];
        $testimonials[$key]['date'] = date('Y-m-d', strtotime($testimonial['date']));
    }
    echo json_encode($testimonials);
}
?>