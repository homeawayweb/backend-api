<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
$db = (new Database())->connect();
if ($db) {
    $stmt = $db->query('SELECT * FROM experiences ORDER BY display_order ASC, id DESC');
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
?>