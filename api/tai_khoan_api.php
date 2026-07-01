<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");

require_once '../app/config/database.php';
require_once '../app/models/TaiKhoanModel.php';

function jsonResponse($success, $message = '', $data = null, $httpCode = 200) {
    http_response_code($httpCode);
    $payload = ['success' => $success];
    if ($message !== '') $payload['message'] = $message;
    if ($data !== null) $payload['data'] = $data;
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}




// Kiểm tra đăng nhập + quyền admin
if (!isset($_SESSION['user_id'])) {
    jsonResponse(false, 'Bạn chưa đăng nhập!', null, 401);
}

$role = $_SESSION['vai_tro'] ?? '';
if ($role !== 'admin') {

    jsonResponse(false, 'Chỉ admin được quản lý tài khoản!', null, 403);

   jsonResponse(false, 'Chỉ admin được quản lý tài khoản!', null, 403);

}

$db = (new Database())->getConnection();
$model = new TaiKhoanModel($db);

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
//anh
switch ($action) {
    case 'get_all':
        if ($method !== 'GET') {
            jsonResponse(false, 'Method not allowed', null, 405);
        }
        $keyword = $_GET['keyword'] ?? '';
        $data = $model->getAll($keyword);
        jsonResponse(true, 'Thành công', $data);
        break;

    case 'save':
        if ($method !== 'POST') {
            jsonResponse(false, 'Method not allowed', null, 405);
        }

        $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : 0;
        $ten_dang_nhap = trim($_POST['ten_dang_nhap'] ?? '');
        $ho_ten = trim($_POST['ho_ten'] ?? '');
        $vai_tro = trim($_POST['vai_tro'] ?? 'nhan_vien');
        $ma_sv_lien_ket = trim($_POST['ma_sv_lien_ket'] ?? '');
        $mat_khau = (string)($_POST['mat_khau'] ?? '');

        if ($ten_dang_nhap === '' || $ho_ten === '') {
            jsonResponse(false, 'Vui lòng nhập tên đăng nhập và họ tên');
        }
        if (!in_array($vai_tro, ['admin', 'nhan_vien', 'sinh_vien'], true)) {
            jsonResponse(false, 'Vai trò không hợp lệ');
        }
        if ($vai_tro === 'sinh_vien' && $ma_sv_lien_ket === '') {
            jsonResponse(false, 'Tài khoản sinh viên bắt buộc phải có mã sinh viên liên kết');
        }

        if ($model->checkTenDangNhap($ten_dang_nhap, $id)) {
            jsonResponse(false, 'Tên đăng nhập đã tồn tại');
        }

        if ($ma_sv_lien_ket !== '' && $model->checkMaSVLienKet($ma_sv_lien_ket, $id)) {
          jsonResponse(false, 'Mã sinh viên liên kết đã được sử dụng cho tài khoản khác');
        }

        $data = [
            'ten_dang_nhap' => $ten_dang_nhap,
            'ho_ten' => $ho_ten,
            'vai_tro' => $vai_tro,
            'ma_sv_lien_ket' => $ma_sv_lien_ket
        ];

        try {
            if ($id) {
                // Sửa
                $id = (int)$id;
                $existing = $model->getById($id);
                if (!$existing) {
                    jsonResponse(false, 'Tài khoản không tồn tại');
                }

                if ($existing['ten_dang_nhap'] !== $ten_dang_nhap && $model->checkTenDangNhap($ten_dang_nhap)) {
                    jsonResponse(false, 'Tên đăng nhập đã tồn tại');
                }

                if (!empty($mat_khau)) {
                    if (strlen($mat_khau) < 6) {
                        jsonResponse(false, 'Mật khẩu phải có ít nhất 6 ký tự');
                    }
                    if (!$model->changePassword($id, $mat_khau)) {
                        jsonResponse(false, 'Lỗi cập nhật mật khẩu');
                    }
                }

                if (!$model->update($id, $data)) {
                    jsonResponse(false, 'Lỗi cập nhật tài khoản');
                }

                jsonResponse(true, 'Cập nhật tài khoản thành công');
            } else {
                // Thêm mới
                if (empty($mat_khau)) {
                    $mat_khau = '123456';
                }
                if (strlen($mat_khau) < 6) {
                    jsonResponse(false, 'Mật khẩu phải có ít nhất 6 ký tự');
                }
                if ($model->checkTenDangNhap($ten_dang_nhap)) {
                    jsonResponse(false, 'Tên đăng nhập đã tồn tại');
                }
                $data['mat_khau'] = $mat_khau;

                if (!$model->insert($data)) {
                    jsonResponse(false, 'Lỗi tạo tài khoản');
                }
                jsonResponse(true, 'Tạo tài khoản thành công');
            }
        } catch (Exception $e) {
            jsonResponse(false, 'Lỗi: ' . $e->getMessage());
        }
        break;

case 'delete':

    // Chỉ chấp nhận phương thức POST
    if ($method !== 'POST') {
        jsonResponse(false, 'Method không hợp lệ', null, 405);
    }

    // Không truyền id
    if (!isset($_POST['id'])) {
        jsonResponse(false, 'ID không được để trống');
    }

    $id = trim($_POST['id']);

    // ID rỗng
    if ($id === '') {
        jsonResponse(false, 'ID không được để trống');
    }

    // ID phải là số
    if (!is_numeric($id)) {
        jsonResponse(false, 'ID không hợp lệ');
    }

    $id = (int)$id;

    // ID phải lớn hơn 0
    if ($id <= 0) {
        jsonResponse(false, 'ID không hợp lệ');
    }

    // Kiểm tra tài khoản có tồn tại không
    $taiKhoan = $model->getById($id);

    if (!$taiKhoan) {
        jsonResponse(false, 'Tài khoản không tồn tại');
    }

    try {

        if ($model->delete($id)) {

            jsonResponse(true, 'Xóa tài khoản thành công');

        } else {

            jsonResponse(false, 'Lỗi xóa tài khoản');

        }

    } catch (Exception $e) {

        jsonResponse(false, 'Lỗi: ' . $e->getMessage());

    }

    break;

    case 'reset_password':
        if ($method !== 'POST') {
            jsonResponse(false, 'Method not allowed', null, 405);
        }
        $id = $_POST['id'] ?? null;
        if (!$id) {
            jsonResponse(false, 'ID không hợp lệ');
        }
        try {
            if ($model->resetPassword($id)) {
                jsonResponse(true, 'Reset mật khẩu về 123456 thành công');
            } else {
                jsonResponse(false, 'Lỗi reset mật khẩu');
            }
        } catch (Exception $e) {
            jsonResponse(false, 'Lỗi: ' . $e->getMessage());
        }
        break;

    default:
        jsonResponse(false, 'Action API không hợp lệ!', null, 400);
}