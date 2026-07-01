<?php
session_start();

// Import kết nối DB và Model
require_once '../app/config/database.php'; // Điều chỉnh lại đường dẫn cho khớp với cấu trúc thư mục của bạn
require_once '../app/models/UserModel.php'; 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate dữ liệu đầu vào
    if (empty($username) || empty($password)) {
        echo json_encode([
            'success' => false, 
            'message' => 'Vui lòng nhập đầy đủ tài khoản và mật khẩu!'
        ]);
        exit;
    }

    try {
        $db = (new Database())->getConnection();
        $userModel = new UserModel($db);
        
        $user = $userModel->login($username, $password);

        if ($user) {
            // Đăng nhập thành công, lưu thông tin vào SESSION
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['vai_tro'] = $user['vai_tro'];
            
            // Giả định tên các cột trong DB của bạn (tuỳ chỉnh lại nếu khác)
            $_SESSION['fullname'] = $user['ho_ten'] ?? $user['ten_dang_nhap']; 
            
            if ($user['vai_tro'] === 'sinh_vien') {
                $_SESSION['ma_sv'] = $user['ten_dang_nhap']; // Hoặc $user['ma_sv'] tùy DB
            }

            echo json_encode([
                'success' => true, 
                'redirect' => 'index.php?controller=home' // Trả về đường dẫn để JS tự động chuyển trang
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Tên đăng nhập hoặc mật khẩu không chính xác!'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'message' => 'Lỗi hệ thống: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Phương thức không được hỗ trợ!'
    ]);
}
?>