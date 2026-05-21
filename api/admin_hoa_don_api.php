<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../app/config/database.php';
require_once '../app/models/HoaDonModel.php';

$db = (new Database())->getConnection();
$model = new HoaDonModel($db);
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'get_all_for_export':
        $listHoaDon = $model->getAll();
        echo json_encode(['success' => true, 'data' => $listHoaDon]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action không tồn tại']);
}
?>