<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");

require_once '../app/config/database.php';
require_once '../app/models/SuCoModel.php';

function jsonResponse($success, $message = '', $data = null, $httpCode = 200) {
    http_response_code($httpCode);
    $payload = ['success' => $success];
    if ($message !== '') $payload['message'] = $message;
    if ($data !== null) $payload['data'] = $data;
    echo json_encode($payload);
    exit;
}

//if (!isset($_SESSION['user_id'])) {
 //   jsonResponse(false, 'Bạn chưa đăng nhập!', null, 401);
//}

//$role = $_SESSION['vai_tro'] ?? '';
//if (!in_array($role, ['admin', 'nhan_vien', 'sinh_vien'], true)) {
    //jsonResponse(false, 'Vai trò không hợp lệ!', null, 403);
//}

$role = 'admin'; // Cấp quyền admin ảo để test
$db = (new Database())->getConnection();
$model = new SuCoModel($db);

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_all':
        $keyword = $_GET['keyword'] ?? '';
        jsonResponse(true, '', $model->getAll($keyword));
        break;

    case 'insert':
        $input = json_decode(file_get_contents("php://input"), true);

        $phong_id = (int)($input['phong_id'] ?? 0);
        $nguoi_bao = trim($input['nguoi_bao'] ?? '');
        $noi_dung = trim($input['noi_dung'] ?? '');

        // Sinh viên: ép phòng + người báo theo session để tránh gửi sai phòng
        if ($role === 'sinh_vien') {
            $phong_id = (int)($_SESSION['phong_id_cua_sv'] ?? 0);
            $nguoi_bao = trim($_SESSION['fullname'] ?? '');
        }

        if (!$phong_id || $noi_dung === '') {
            jsonResponse(false, 'Vui lòng chọn phòng và mô tả hỏng hóc!');
        }

        $res = $model->insert($phong_id, $nguoi_bao, $noi_dung); // model đã mặc định cho_xu_ly
        jsonResponse($res, $res ? 'Ghi nhận sự cố thành công!' : 'Lỗi hệ thống khi lưu!');
        break;

    case 'update_status':
        if (!in_array($role, ['admin', 'nhan_vien'], true)) {
            jsonResponse(false, 'Bạn không có quyền cập nhật trạng thái!', null, 403);
        }

        $input = json_decode(file_get_contents("php://input"), true);
        $id = (int)($input['id'] ?? 0);
        $trang_thai = trim($input['trang_thai'] ?? '');

        $allowed = ['cho_xu_ly', 'dang_sua', 'da_xong'];
        if ($id <= 0 || !in_array($trang_thai, $allowed, true)) {
            jsonResponse(false, 'Dữ liệu cập nhật không hợp lệ!');
        }

        $res = $model->updateStatus($id, $trang_thai);
        jsonResponse($res, $res ? 'Đã cập nhật trạng thái!' : 'Lỗi cập nhật trạng thái!');
        break;

    case 'delete':
        if ($role !== 'admin') {
            jsonResponse(false, 'Chỉ admin được xóa sự cố!', null, 403);
        }

        $input = json_decode(file_get_contents("php://input"), true);
        $id = (int)($input['id'] ?? 0);
        if ($id <= 0) {
            jsonResponse(false, 'ID không hợp lệ!');
        }

        $res = $model->delete($id);
        jsonResponse($res, $res ? 'Đã xóa bản ghi!' : 'Lỗi khi xóa!');
        break;

    default:
        jsonResponse(false, 'Action API không hợp lệ!', null, 400);
}