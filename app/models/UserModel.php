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
}
?>