<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
http_response_code(204);
$data = json_decode(file_get_contents("php://input"));
$page = $data->page ?? '/';
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$country = 'Unknown';
try {
    $geo_data = @json_decode(@file_get_contents("http://ip-api.com/json/{$ip_address}"));
    if ($geo_data && $geo_data->status == 'success') {
        $country = $geo_data->country;
    }
} catch (Exception $e) { /* Ignore errors */ }
$database = new Database();
$db = $database->connect();
if ($db) {
    $query = 'INSERT INTO visits SET page = :page, ip_address = :ip_address, user_agent = :user_agent, country = :country';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':page', $page);
    $stmt->bindParam(':ip_address', $ip_address);
    $stmt->bindParam(':user_agent', $user_agent);
    $stmt->bindParam(':country', $country);
    $stmt->execute();
}
?>