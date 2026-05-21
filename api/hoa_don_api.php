<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");

require_once '../app/config/database.php';
require_once '../app/models/HoaDonModel.php';

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
if ($role !== 'sinh_vien') {
    jsonResponse(false, 'Chỉ sinh viên mới được thanh toán hóa đơn phòng tại đây!', null, 403);
}

$phong_id = (int)($_SESSION['phong_id_cua_sv'] ?? 0);
if ($phong_id <= 0) {
    jsonResponse(false, 'Không tìm thấy phòng của sinh viên!', null, 400);
}

$db = (new Database())->getConnection();
$model = new HoaDonModel($db);
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_my_bill':
        $bill = $model->getOutstandingBillByRoom($phong_id);
        if (!$bill) {
            jsonResponse(false, 'Không có hóa đơn chưa thanh toán cho phòng này.', []);
        }
        jsonResponse(true, 'Lấy hóa đơn thành công', $bill);
        break;

    case 'pay':
        $input = json_decode(file_get_contents("php://input"), true);
        $id = isset($input['id']) ? (int)$input['id'] : 0;
        if ($id <= 0) {
            jsonResponse(false, 'ID hóa đơn không hợp lệ', null, 400);
        }

        $bill = $model->getById($id);
        if (!$bill || (int)$bill['phong_id'] !== $phong_id) {
            jsonResponse(false, 'Hóa đơn không tồn tại hoặc không thuộc phòng của bạn.', null, 403);
        }
        if ((int)$bill['trang_thai'] === 1) {
            jsonResponse(false, 'Hóa đơn đã được thanh toán trước đó.', null, 400);
        }

        // Tạo yêu cầu thanh toán thay vì trực tiếp thanh toán
        require_once '../app/models/ThanhToanModel.php';
        $ttModel = new ThanhToanModel($db);
        $ma_sv = $_SESSION['ma_sv'] ?? '';
        
        if ($ttModel->createPaymentRequest($id, $phong_id, $ma_sv, (int)$bill['tong_tien'])) {
            jsonResponse(true, 'Yêu cầu thanh toán đã được gửi. Admin sẽ xác nhận trong thời gian sớm nhất.', ['id' => $id]);
        } else {
            jsonResponse(false, 'Gửi yêu cầu thanh toán không thành công. Vui lòng thử lại.', null, 500);
        }
        break;
case 'get_payment_status':
        // Lấy trạng thái yêu cầu thanh toán gần nhất của sinh viên
        $ma_sv = $_SESSION['ma_sv'] ?? '';
        if (empty($ma_sv)) {
            jsonResponse(false, 'Không tìm thấy mã sinh viên.', null, 400);
        }
        
        $sql = "SELECT yct.* FROM yeu_cau_thanh_toan yct 
                WHERE yct.ma_sinh_vien = '$ma_sv' AND yct.phong_id = $phong_id
                ORDER BY yct.created_at DESC LIMIT 1";
        $result = $db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $status = $result->fetch_assoc();
            jsonResponse(true, '', $status);
        } else {
            jsonResponse(false, 'Không có yêu cầu thanh toán nào.', null);
        }
        break;


    default:
        jsonResponse(false, 'Action API không hợp lệ!', null, 400);
}
?>
