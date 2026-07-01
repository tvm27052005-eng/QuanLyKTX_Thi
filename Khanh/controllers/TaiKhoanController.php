<?php
require_once 'app/models/TaiKhoanModel.php';

class TaiKhoanController {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        // Có thể không cần giữ model ở đây nữa
    }

    public function index() {
        if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'admin') {
            header("Location: index.php?controller=home");
            exit();
        }

        require_once 'app/views/tai_khoan/index.php';
    }
}
?>