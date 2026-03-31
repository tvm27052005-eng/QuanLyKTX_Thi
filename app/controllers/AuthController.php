<?php
require_once 'app/models/UserModel.php';

class AuthController {
    private $model;
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->model = new UserModel($this->db);
    }

    // Màn hình đăng nhập
    public function login() {
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=home");
            exit();
        }

        $error = "";
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $user = $this->model->login($username, $password);

            if ($user) {
                // Lưu session
                $_SESSION['user_id'] = $user['id'];
                
                // Cập nhật: Lấy giá trị từ cột 'ho_ten' trong CSDL mới
                $_SESSION['fullname'] = $user['ho_ten']; 
                
                header("Location: index.php?controller=home");
                exit();
            } else {
                $error = "Tên đăng nhập hoặc mật khẩu không đúng!";
            }
        }

        require_once 'app/views/auth/login.php';
    }

    // Xử lý đăng xuất
    public function logout() {
        session_unset();
        session_destroy();
        header("Location: index.php?controller=auth&action=login");
        exit();
    }
}
?>