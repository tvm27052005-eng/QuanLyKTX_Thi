<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../app/config/database.php';
$db = (new Database())->getConnection();
$action = $_GET['action'] ?? '';

switch ($action) {

    // Lấy danh sách phòng (có tìm kiếm)
    case 'get_all':
        $kw = $db->real_escape_string($_GET['keyword'] ?? '');
        $where = "1";
        if ($kw) $where .= " AND (ma_phong LIKE '%$kw%' OR loai_phong LIKE '%$kw%' OR trang_thai LIKE '%$kw%')";
        $result = $db->query("SELECT * FROM phong WHERE $where ORDER BY id DESC");
        $data = [];
        while ($row = $result->fetch_assoc()) $data[] = $row;
        echo json_encode($data);
        break;

    // Lấy 1 phòng theo ID (để sửa)
    case 'get_one':
        $id = (int)($_GET['id'] ?? 0);
        $result = $db->query("SELECT * FROM phong WHERE id=$id");
        echo json_encode($result->fetch_assoc());
        break;

    // Thêm phòng
    case 'insert':
        $body = json_decode(file_get_contents('php://input'), true);
        $ma    = $db->real_escape_string($body['ma_phong']);
        $loai  = $db->real_escape_string($body['loai_phong']);
        $gia   = (int)$body['gia_thue'];
        $max   = (int)$body['so_nguoi_toi_da'];
        $tt    = $db->real_escape_string($body['trang_thai']);
        // Validate dữ liệu
        if ($gia < 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Giá thuê phải lớn hơn hoặc bằng 0!'
            ]);
            exit;
        }
        if ($max <= 0 || !is_numeric($body['so_nguoi_toi_da']) || floor($body['so_nguoi_toi_da']) != $body['so_nguoi_toi_da']) {
            echo json_encode([
                'success' => false,
                'message' => 'Số người tối đa phải là số nguyên lớn hơn 0!'
            ]);
            exit;
        }
        // Kiểm tra phòng VIP tối đa 4 người
        if (in_array($loai, ['nam_vip', 'nu_vip']) && $max > 4) {
            echo json_encode([
                'success' => false,
                'message' => 'Phòng VIP chỉ được tối đa 4 người!'
            ]);
            exit;
        }
        // Kiểm tra trùng mã phòng
        $check = $db->query("SELECT id FROM phong WHERE ma_phong='$ma'");
        if ($check->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Mã phòng đã tồn tại!']);
            break;
        }
        $ok = $db->query("INSERT INTO phong (ma_phong,loai_phong,gia_thue,so_nguoi_toi_da,trang_thai)
                          VALUES ('$ma','$loai',$gia,$max,'$tt')");
        echo json_encode(['success' => $ok, 'message' => $ok ? 'Thêm thành công!' : $db->error]);
        break;

    // Cập nhật phòng
    case 'update':
        $body = json_decode(file_get_contents('php://input'), true);
        $id   = (int)$body['id'];
        $ma   = $db->real_escape_string($body['ma_phong']);
        $loai = $db->real_escape_string($body['loai_phong']);
        $gia  = (int)$body['gia_thue'];
        $max  = (int)$body['so_nguoi_toi_da'];
        $tt   = $db->real_escape_string($body['trang_thai']);
        // Validate dữ liệu
        if ($gia < 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Giá thuê phải lớn hơn hoặc bằng 0!'
            ]);
            exit;
        }
        if ($max <= 0 || !is_numeric($body['so_nguoi_toi_da']) || floor($body['so_nguoi_toi_da']) != $body['so_nguoi_toi_da']) {
            echo json_encode([
                'success' => false,
                'message' => 'Số người tối đa phải là số nguyên lớn hơn 0!'
            ]);
            exit;
        }
        // Kiểm tra phòng VIP tối đa 4 người
        if (in_array($loai, ['nam_vip', 'nu_vip']) && $max > 4) {
            echo json_encode([
                'success' => false,
                'message' => 'Phòng VIP chỉ được tối đa 4 người!'
            ]);
            exit;
        }
        // Kiểm tra nếu đổi sang VIP mà số người hiện tại đang vượt quá 4
        if (in_array($loai, ['Nam VIP', 'Nữ VIP'])) {
            $check_hien_tai = $db->query("SELECT so_nguoi_hien_tai FROM phong WHERE id = $id");
            if ($check_hien_tai && $check_hien_tai->num_rows > 0) {
                $row_ht = $check_hien_tai->fetch_assoc();
                $so_hien_tai = (int)$row_ht['so_nguoi_hien_tai'];
                if ($so_hien_tai > 4) {
                    echo json_encode([
                        'success' => false,
                        'message' => "Không thể chuyển sang phòng VIP! Phòng đang có $so_hien_tai người, vượt giới hạn tối đa 4 người. Vui lòng chuyển bớt sinh viên trước."
                    ]);
                    exit;
                }
            }
        }
        // Kiểm tra trùng mã phòng (trừ chính nó)
        $check = $db->query("SELECT id FROM phong WHERE ma_phong='$ma' AND id<>$id");
        if ($check->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Mã phòng đã tồn tại!']);
            break;
        }
        //Kiểm tra trạng thái bảo trì: nếu đang chuyển sang bảo trì thì phải đảm bảo phòng không còn sinh viên nào ở trong
        if ($tt === 'bao_tri') {
            $check_sv = $db->query("SELECT so_nguoi_hien_tai FROM phong WHERE id = $id");
            if ($check_sv && $check_sv->num_rows > 0) {
                $row = $check_sv->fetch_assoc();
                if ((int)$row['so_nguoi_hien_tai'] > 0) {
                    echo json_encode(['success' => false, 'message' => 'Không thể đặt phòng này vào bảo trì vì vẫn còn sinh viên ở trong!']);
                    exit; 
                }
            }
        }
        $ok = $db->query("UPDATE phong SET ma_phong='$ma',loai_phong='$loai',
                          gia_thue=$gia,so_nguoi_toi_da=$max,trang_thai='$tt' WHERE id=$id");
        echo json_encode(['success' => $ok, 'message' => $ok ? 'Cập nhật thành công!' : $db->error]);
        break;

    // Xóa phòng
   case 'delete':
        $id = (int)($_GET['id'] ?? 0);
        
        // BƯỚC 1: Kiểm tra xem phòng có đang chứa sinh viên nào không
        // (Dựa vào cột so_nguoi_hien_tai trong bảng phong)
        $check_phong = $db->query("SELECT so_nguoi_hien_tai FROM phong WHERE id = $id");
        if ($check_phong && $check_phong->num_rows > 0) {
            $phong = $check_phong->fetch_assoc();
            if ($phong['so_nguoi_hien_tai'] > 0) {
                echo json_encode(['success' => false, 'message' => 'CẢNH BÁO: Phòng này đang có sinh viên ở, bạn không thể xóa!']);
                exit; // Dừng ngay lập tức
            }
        }

        // BƯỚC 2: Kiểm tra kỹ hơn xem còn hồ sơ sinh viên nào bị kẹt ID phòng này không
        $check_sv = $db->query("SELECT id FROM sinh_vien WHERE phong_id = $id");
        if ($check_sv && $check_sv->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'LỖI: Vẫn còn dữ liệu sinh viên gắn với phòng này. Vui lòng chuyển phòng cho họ trước khi xóa!']);
            exit; // Dừng ngay lập tức
        }

        // BƯỚC 3: Nếu qua được 2 vòng kiểm tra, tiến hành xóa
        $ok = $db->query("DELETE FROM phong WHERE id = $id");
        echo json_encode(['success' => $ok, 'message' => $ok ? 'Xóa phòng thành công!' : 'Lỗi hệ thống: ' . $db->error]);
        break;

    // Xuất Excel (trả về dữ liệu để JS xử lý với SheetJS)
    case 'export':
        $kw = $db->real_escape_string($_GET['keyword'] ?? '');
        $where = "1";
        if ($kw) $where .= " AND (ma_phong LIKE '%$kw%' OR loai_phong LIKE '%$kw%')";
        $result = $db->query("SELECT ma_phong,loai_phong,gia_thue,so_nguoi_toi_da,so_nguoi_hien_tai,trang_thai FROM phong WHERE $where ORDER BY id DESC");
        $data = [];
        while ($row = $result->fetch_assoc()) $data[] = $row;
        echo json_encode($data);
        break;

    default:
        echo json_encode(['error' => 'Action không tồn tại']);
}
?>