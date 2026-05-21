<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../app/config/database.php';
require_once '../app/models/HomeModel.php';

$db = (new Database())->getConnection();
$model = new HomeModel($db);

$action = $_GET['action'] ?? 'get_stats';

if ($action === 'get_stats') {
    $stats = $model->getDashboardStats();
    echo json_encode(['success' => true, 'data' => $stats]);
} else {
    echo json_encode(['success' => false, 'message' => 'Action không hợp lệ']);
}
?>