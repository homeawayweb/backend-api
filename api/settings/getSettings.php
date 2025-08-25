<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
$database = new Database(); $db = $database->connect();
if ($db) {
    $query = 'SELECT name, value FROM settings';
    $stmt = $db->prepare($query); $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $settings = [];
    foreach ($results as $row) { $settings[$row['name']] = $row['value']; }
    echo json_encode($settings);
}
?>