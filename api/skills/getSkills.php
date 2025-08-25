<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
$database = new Database();
$db = $database->connect();
if ($db) {
    $query = 'SELECT id, name, domain, proficiency, icon FROM skills ORDER BY domain ASC, proficiency DESC';
    $stmt = $db->prepare($query);
    $stmt->execute();
    $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($skills);
}
?>