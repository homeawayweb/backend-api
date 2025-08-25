<?php
// File Path: portfolio-api/api/analytics/getTrafficTrends.php (FINAL)

// --- Universal CORS and Error Reporting ---
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit(); }

require_once '../../config/database.php';

$database = new Database();
$db = $database->connect();

if ($db) {
    try {
        $query = "SELECT DATE_FORMAT(visit_timestamp, '%Y-%m') AS month, COUNT(DISTINCT ip_address) AS visitors FROM visits WHERE visit_timestamp >= DATE_SUB(NOW(), INTERVAL 7 MONTH) GROUP BY month ORDER BY month ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $labels = []; $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $labels[] = date('M', strtotime($month));
            $visitor_count = 0;
            foreach ($results as $row) {
                if ($row['month'] == $month) { $visitor_count = (int)$row['visitors']; break; }
            }
            $data[] = $visitor_count;
        }
        
        $trafficTrends = ['labels' => $labels, 'datasets' => [['label' => 'Unique Visitors', 'data' => $data, 'borderColor' => '#3b82f6', 'backgroundColor' => 'rgba(59, 130, 246, 0.5)', 'tension' => 0.4, 'fill' => true]]];
        http_response_code(200);
        echo json_encode($trafficTrends);
    } catch (PDOException $e) { http_response_code(500); echo json_encode(['message' => 'DB Error in getTrafficTrends: ' . $e->getMessage()]); }
} else { http_response_code(503); echo json_encode(['message' => 'DB Connection Failed.']); }
?>