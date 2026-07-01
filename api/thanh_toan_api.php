<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");
//huy2
require_once '../app/config/database.php';
require_once '../app/models/ThanhToanModel.php';

function jsonResponse($success, $message = '', $data = null, $code = 200) {
    http_response_code($code);
    $payload = ['success' => $success];
    if ($message !== '') $payload['message'] = $message;
    if ($data !== null) $payload['data'] = $data;
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    jsonResponse(false, 'Bạn chưa đăng nhập!', null, 401);
}

$role = $_SESSION['vai_tro'] ?? '';
if (!in_array($role, ['admin', 'nhan_vien'])) {
    jsonResponse(false, 'Chỉ admin và nhân viên mới được xem yêu cầu thanh toán!', null, 403);
}

$db = (new Database())->getConnection();
$model = new ThanhToanModel($db);
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_all':
        $keyword = $_GET['keyword'] ?? '';
        $data = $model->getAll($keyword);
        jsonResponse(true, 'Thành công', $data);
        break;

    case 'approve':
        $input = json_decode(file_get_contents("php://input"), true);
        $id = isset($input['id']) ? (int)$input['id'] : 0;
        if ($id <= 0) {
            jsonResponse(false, 'ID không hợp lệ', null, 400);
        }

        if ($model->approve($id)) {
            jsonResponse(true, 'Đã xác nhận thanh toán thành công!', ['id' => $id]);
        } else {
            jsonResponse(false, 'Xác nhận không thành công. Vui lòng thử lại.', null, 500);
        }
        break;

    case 'reject':
        $input = json_decode(file_get_contents("php://input"), true);
        $id = isset($input['id']) ? (int)$input['id'] : 0;
        if ($id <= 0) {
            jsonResponse(false, 'ID không hợp lệ', null, 400);
        }

        if ($model->reject($id)) {
            jsonResponse(true, 'Đã từ chối yêu cầu thanh toán.', ['id' => $id]);
        } else {
            jsonResponse(false, 'Từ chối không thành công. Vui lòng thử lại.', null, 500);
        }
        break;

    case 'delete':
        $input = json_decode(file_get_contents("php://input"), true);
        $id = isset($input['id']) ? (int)$input['id'] : 0;
        if ($id <= 0) {
            jsonResponse(false, 'ID không hợp lệ', null, 400);
        }

        if ($model->delete($id)) {
            jsonResponse(true, 'Đã xóa yêu cầu.', ['id' => $id]);
        } else {
            jsonResponse(false, 'Xóa không thành công.', null, 500);
        }
        break;

    default:
        jsonResponse(false, 'Action API không hợp lệ!', null, 400);
}
?>
