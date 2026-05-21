<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../app/config/database.php';
require_once '../app/models/DichVuModel.php';

$db = (new Database())->getConnection();
$model = new DichVuModel($db);
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_all':
        echo json_encode(['success' => true, 'data' => $model->getAll()]);
        break;

    case 'save':
        $input = json_decode(file_get_contents("php://input"), true);

        $id = isset($input['id']) && $input['id'] !== '' ? (int)$input['id'] : 0;
        $tenRaw = trim($input['ten_dich_vu'] ?? '');
        $gia = (int)($input['don_gia'] ?? 0);

        if ($tenRaw === '' || $gia <= 0) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập tên dịch vụ và đơn giá hợp lệ!']);
            exit;
        }

        if ($model->existsByName($tenRaw, $id)) {
            echo json_encode(['success' => false, 'message' => 'Tên dịch vụ đã tồn tại, không thể thêm trùng!']);
            exit;
        }

        $ten = $db->real_escape_string($tenRaw);
        $res = $id > 0 ? $model->update($id, $ten, $gia) : $model->insert($ten, $gia);

        echo json_encode(['success' => $res, 'message' => $res ? 'Lưu dịch vụ thành công!' : 'Lỗi!']);
        break;

    case 'delete':
        $input = json_decode(file_get_contents("php://input"), true);
        $res = $model->delete((int)($input['id'] ?? 0));
        echo json_encode(['success' => $res, 'message' => $res ? 'Xóa dịch vụ thành công!' : 'Lỗi!']);
        break;

    case 'get_room_services':
        $phong_id = (int)($_GET['phong_id'] ?? 0);
        echo json_encode(['success' => true, 'data' => $model->getRoomServices($phong_id)]);
        break;

    case 'save_room_services':
        $input = json_decode(file_get_contents("php://input"), true);
        $phong_id = (int)($input['phong_id'] ?? 0);
        $dich_vu_ids = $input['dich_vu_ids'] ?? [];

        // Kiểm tra phòng có phải VIP không
        $phong_row = $db->query("SELECT loai_phong FROM phong WHERE id=$phong_id")->fetch_assoc();
        $loai_phong = $phong_row['loai_phong'] ?? '';
        $is_vip = in_array($loai_phong, ['Nam VIP', 'Nữ VIP']);

        // Nếu là phòng VIP → bắt buộc thêm dịch vụ điều hoà vào danh sách
        if ($is_vip) {
            $rs_dh = $db->query("SELECT id FROM dich_vu WHERE LOWER(ten_dich_vu) LIKE '%đi%u h%' LIMIT 1");
            if ($rs_dh && $rs_dh->num_rows > 0) {
                $dieu_hoa_id = (string)$rs_dh->fetch_assoc()['id'];
                if (!in_array($dieu_hoa_id, array_map('strval', $dich_vu_ids))) {
                    $dich_vu_ids[] = $dieu_hoa_id;
                }
            }
        }

        $res = $model->saveRoomServices($phong_id, $dich_vu_ids);
        echo json_encode(['success' => $res, 'message' => $res ? 'Đã gán dịch vụ cho phòng thành công!' : 'Lỗi!']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action invalid']);
}
?>