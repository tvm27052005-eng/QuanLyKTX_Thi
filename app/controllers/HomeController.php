<?php
class HomeController {
    public function index() {
        $role = $_SESSION['vai_tro'] ?? '';

        if ($role === 'sinh_vien') {
            require_once 'app/views/home/index_sv.php';
        } else {
            require_once 'app/views/home/index.php';
        }
    }
}
?>