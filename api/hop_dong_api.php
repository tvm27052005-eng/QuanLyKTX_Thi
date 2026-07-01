<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../app/config/database.php';
require_once '../app/models/HopDongModel.php';

$db = (new Database())->getConnection();
$model = new HopDongModel($db);

$action = $_GET['action'] ?? '';
//huy
switch ($action) {

    case 'get_all':
        $keyword = $_GET['keyword'] ?? '';
        echo json_encode(['success' => true, 'data' => $model->getAll($keyword)]);
        break;

    case 'insert':
        $input = json_decode(file_get_contents("php://input"), true);
        $ma_sv = trim($input['ma_sv'] ?? '');
        $ho_ten = trim($input['ho_ten'] ?? '');
        $lop = trim($input['lop'] ?? '');
        $ngay_sinh = trim($input['ngay_sinh'] ?? '');
        $gioi_tinh = trim($input['gioi_tinh'] ?? '');
        $dia_chi = trim($input['dia_chi'] ?? '');
        $sdt = trim($input['sdt'] ?? '');
        $ngay_vao = trim($input['ngay_vao'] ?? '');
        $ngay_het_han = trim($input['ngay_het_han'] ?? '');

        if (
            empty($ma_sv) || empty($ho_ten) || empty($lop) || empty($ngay_sinh) ||
            empty($gioi_tinh) || empty($dia_chi) || empty($sdt) || empty($ngay_vao) || empty($ngay_het_han)
        ) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin!']);
            exit;
        }

        if ($gioi_tinh !== 'Nam' && $gioi_tinh !== 'Nữ') {
            echo json_encode(['success' => false, 'message' => "Giới tính chỉ được phép là 'Nam' hoặc 'Nữ'!"]);
            exit;
        }

        $startDate = new DateTime($ngay_vao);
        $minEndDate = clone $startDate;
        $minEndDate->add(new DateInterval('P6M'));
        $endDate = new DateTime($ngay_het_han);

        if ($endDate < $minEndDate) {
            echo json_encode(['success' => false, 'message' => 'Ngày hết hạn phải tối thiểu 6 tháng kể từ ngày vào!']);
            exit;
        }

        if ($ngay_vao >= $ngay_het_han) {
            echo json_encode(['success' => false, 'message' => 'Ngày vào phải nhỏ hơn ngày hết hạn!']);
            exit;
        }

        if (!preg_match('/^\d+$/', $sdt)) {
            echo json_encode(['success' => false, 'message' => 'Số điện thoại chỉ được phép nhập số!']);
            exit;
        }

        $stmt = $db->prepare("SELECT COUNT(*) as cnt FROM sinh_vien WHERE ma_sv = ?");
        $stmt->bind_param("s", $ma_sv);
        $stmt->execute();
        $rs = $stmt->get_result()->fetch_assoc();
        if ($rs && $rs['cnt'] > 0) {
            echo json_encode(['success' => false, 'message' => 'Mã sinh viên đã tồn tại, vui lòng nhập mã khác!']);
            exit;
        }

        $res = $model->insert($ma_sv, $ho_ten, $lop, $ngay_sinh, $gioi_tinh, $dia_chi, $sdt, $ngay_vao, $ngay_het_han);
        echo json_encode(['success' => $res, 'message' => $res ? 'Thêm hợp đồng thành công!' : 'Lỗi Database!']);
        break;


    case 'update':
        $input = json_decode(file_get_contents("php://input"), true);
        $id = $input['id'] ?? 0;
        $ma_sv = trim($input['ma_sv'] ?? '');
        $ho_ten = trim($input['ho_ten'] ?? '');
        $lop = trim($input['lop'] ?? '');
        $ngay_sinh = trim($input['ngay_sinh'] ?? '');
        $gioi_tinh = trim($input['gioi_tinh'] ?? '');
        $dia_chi = trim($input['dia_chi'] ?? '');
        $sdt = trim($input['sdt'] ?? '');
        $ngay_vao = trim($input['ngay_vao'] ?? '');
        $ngay_het_han = trim($input['ngay_het_han'] ?? '');

        if (
            empty($ma_sv) || empty($ho_ten) || empty($lop) || empty($ngay_sinh) ||
            empty($gioi_tinh) || empty($dia_chi) || empty($sdt) || empty($ngay_vao) || empty($ngay_het_han)
        ) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin!']);
            exit;
        }

        if ($gioi_tinh !== 'Nam' && $gioi_tinh !== 'Nữ') {
            echo json_encode(['success' => false, 'message' => "Giới tính chỉ được phép là 'Nam' hoặc 'Nữ'!"]);
            exit;
        }

        $startDate = new DateTime($ngay_vao);
        $minEndDate = clone $startDate;
        $minEndDate->add(new DateInterval('P6M'));
        $endDate = new DateTime($ngay_het_han);
        if ($endDate < $minEndDate) {
            echo json_encode(['success' => false, 'message' => 'Ngày hết hạn phải tối thiểu 6 tháng kể từ ngày vào!']);
            exit;
        }

        if (!preg_match('/^\d+$/', $sdt)) {
            echo json_encode(['success' => false, 'message' => 'Số điện thoại chỉ được phép nhập số!']);
            exit;
        }

        $res = $model->update($id, $ma_sv, $ho_ten, $lop, $ngay_sinh, $gioi_tinh, $dia_chi, $sdt, $ngay_vao, $ngay_het_han);
        echo json_encode(['success' => $res, 'message' => $res ? 'Cập nhật thành công!' : 'Lỗi cập nhật!']);
        break;

    case 'delete':
        $input = json_decode(file_get_contents("php://input"), true);
        $res = $model->delete($input['id'] ?? 0);
        echo json_encode(['success' => $res, 'message' => $res ? 'Đã xóa hợp đồng!' : 'Lỗi khi xóa!']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action không hợp lệ!']);
}
?>