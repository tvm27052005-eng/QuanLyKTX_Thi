<?php
class HomeController {
    public function __construct() {
        // Sau này nếu cần lấy số liệu thống kê (tổng SV, tổng phòng...) 
        // thì mình sẽ gọi Database và Model ở đây
    }

    public function index() {
        // Gọi giao diện trang chủ ra hiển thị
        require_once 'app/views/home/index.php';
    }
}
?>