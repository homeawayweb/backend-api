<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
$database = new Database();
$db = $database->connect();
if ($db) {
    $query = 'SELECT name FROM icons ORDER BY name ASC';
    $stmt = $db->prepare($query);
    $stmt->execute();
    $icons = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    echo json_encode($icons);
}
?>