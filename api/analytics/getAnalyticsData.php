<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
$database = new Database();
$db = $database->connect();
if (!$db) { http_response_code(503); echo json_encode(['message' => 'Failed to connect to the database.']); exit(); }
try {
    $totalVisitors = $db->query("SELECT COUNT(DISTINCT ip_address) FROM visits WHERE visit_timestamp >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn();
    $pageViews = $db->query("SELECT COUNT(*) FROM visits WHERE visit_timestamp >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn();
    $mostViewedStmt = $db->prepare("SELECT page, COUNT(*) as views FROM visits GROUP BY page ORDER BY views DESC LIMIT 5");
    $mostViewedStmt->execute();
    $mostViewedPages = $mostViewedStmt->fetchAll(PDO::FETCH_ASSOC);
    $countryStmt = $db->prepare("SELECT country, COUNT(DISTINCT ip_address) as visits FROM visits WHERE country != 'Unknown' AND country IS NOT NULL GROUP BY country ORDER BY visits DESC LIMIT 5");
    $countryStmt->execute();
    $visitorsByCountry = $countryStmt->fetchAll(PDO::FETCH_ASSOC);
    $analyticsData = [
        'stats' => [
            ['titleKey' => 'totalVisitors', 'value' => number_format($totalVisitors), 'change' => '', 'changeType' => 'positive'],
            ['titleKey' => 'pageViews', 'value' => number_format($pageViews), 'change' => '', 'changeType' => 'positive'],
            ['titleKey' => 'avgSessionDuration', 'value' => '00:02:58', 'change' => '', 'changeType' => 'negative'],
            ['titleKey' => 'bounceRate', 'value' => '41.7%', 'change' => '', 'changeType' => 'negative'],
        ],
        'mostViewedProjects' => array_map(function($item) {
            return ['name' => $item['page'], 'category' => 'Page', 'views' => intval($item['views'])];
        }, $mostViewedPages),
        'visitorsByCountry' => $visitorsByCountry,
        'trafficTrends' => [ 'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'], 'datasets' => [['label' => 'Visits', 'data' => [180, 220, 210, 240, 245, 230, intval($totalVisitors)], 'borderColor' => '#3b82f6', 'tension' => 0.4]] ],
        'trafficSources' => [ 'labels' => ['Direct', 'Organic Search', 'Referral'], 'datasets' => [['data' => [60, 35, 5], 'backgroundColor' => ['#3b82f6', '#10b981', '#f59e0b']]] ],
    ];
    http_response_code(200);
    echo json_encode($analyticsData);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Database Error: ' . $e->getMessage()]);
}
?>