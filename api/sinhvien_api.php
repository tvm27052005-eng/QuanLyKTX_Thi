<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../app/config/database.php';
require_once '../app/models/SinhVienModel.php';

$db = (new Database())->getConnection();
$svModel = new SinhVienModel($db);
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

function response($success, $message, $data = []) {
    echo json_encode(["success" => $success, "message" => $message, "data" => $data], JSON_UNESCAPED_UNICODE);
    exit;
}

switch ($method) {
    case 'GET':
        if ($action === 'get_rooms') {
            $gioi_tinh = $_GET['gioi_tinh'] ?? '';
            $data = $svModel->getAvailableRooms($gioi_tinh);
            response(true, "Lấy phòng thành công", $data);
        } else {
            $keyword = $_GET['keyword'] ?? '';
            $data = $svModel->getAll($keyword);
            response(true, "Thành công", $data);
        }
        break;

    case 'PUT':
        $input = json_decode(file_get_contents("php://input"), true);
        $ma_sv = trim($input['ma_sv'] ?? '');
        if (empty($ma_sv)) response(false, "Thiếu mã sinh viên!");

        if ($svModel->updateSync($ma_sv, $input)) {
            response(true, "Cập nhật thông tin và xếp phòng thành công!");
        } else {
            response(false, "Lỗi khi cập nhật!");
        }
        break;

    case 'DELETE':
        $input = json_decode(file_get_contents("php://input"), true);
        $ma_sv = trim($input['ma_sv'] ?? '');
        
        if (empty($ma_sv)) response(false, "Thiếu mã sinh viên!");

        // KIỂM TRA LOGIC: KHÔNG ĐƯỢC XÓA KHI HỢP ĐỒNG CÒN HIỆU LỰC
        $checkSql = "SELECT trang_thai_hop_dong FROM sinh_vien WHERE ma_sv = '$ma_sv'";
        $checkRs = $db->query($checkSql);
        
        if ($checkRs && $checkRs->num_rows > 0) {
            $trang_thai = $checkRs->fetch_assoc()['trang_thai_hop_dong'];
            if ($trang_thai === 'Hieu_luc') {
                response(false, "LỖI BẢO MẬT: Sinh viên này đang có Hợp đồng hiệu lực. Bạn phải sang phần 'Hợp đồng' để hủy hợp đồng trước mới được phép xóa!");
            }
        }

        // Nếu hợp đồng đã hết hiệu lực thì cho phép xóa hoàn toàn
        if ($svModel->deleteFull($ma_sv)) {
            response(true, "Đã xóa vĩnh viễn hồ sơ sinh viên!");
        } else {
            response(false, "Lỗi khi xóa hệ thống!");
        }
        break;
}
?>