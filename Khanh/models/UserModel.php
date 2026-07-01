<?php
class UserModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Hàm kiểm tra đăng nhập với bảng database mới
    public function login($username, $password) {
        $u = $this->conn->real_escape_string($username);
        $p = $this->conn->real_escape_string($password);
        
        // Cập nhật: Dùng bảng tai_khoan và cột ten_dang_nhap, mat_khau
        $sql = "SELECT * FROM tai_khoan WHERE ten_dang_nhap = '$u' AND mat_khau = '$p'";
        $result = $this->conn->query($sql);

        // Nếu query bị lỗi (ví dụ sai tên bảng), $result sẽ là false
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc(); // Trả về mảng thông tin user
        }
        return false; // Sai tài khoản/mật khẩu hoặc lỗi query
    }
    // Hàm Đổi mật khẩu
    public function changePassword($user_id, $old_password, $new_password) {
        $id = (int)$user_id;
        $old = $this->conn->real_escape_string($old_password);
        $new = $this->conn->real_escape_string($new_password);
        
        // 1. Kiểm tra mật khẩu cũ có đúng không
        $check = $this->conn->query("SELECT id FROM tai_khoan WHERE id = $id AND mat_khau = '$old'");
        if ($check && $check->num_rows > 0) {
            // 2. Nếu đúng thì cập nhật mật khẩu mới
            $this->conn->query("UPDATE tai_khoan SET mat_khau = '$new' WHERE id = $id");
            return true;
        }
        return false; // Mật khẩu cũ bị sai
    }
    
}
?>