<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'app/config/database.php';

$controller = isset($_GET['controller']) ? $_GET['controller'] : 'home'; 
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// ==========================================
// BẢO VỆ ROUTE: KIỂM TRA ĐĂNG NHẬP
// Nếu chưa có session VÀ không phải đang ở controller auth thì đá về trang login
// ==========================================
if (!isset($_SESSION['user_id']) && $controller != 'auth') {
    header("Location: index.php?controller=auth&action=login");
    exit();
}


// Định tuyến
switch ($controller) {
    case 'auth':
        require_once 'app/controllers/AuthController.php';
        $controllerObj = new AuthController();
        break;
        
    case 'hoadon':
        require_once 'app/controllers/HoaDonController.php';
        $controllerObj = new HoaDonController();
        break;
        
   case 'home':
        // GỌI HOME CONTROLLER CHUẨN
        require_once 'app/controllers/HomeController.php';
        $controllerObj = new HomeController();
        break;
        
    default:
        die("Không tìm thấy trang!");
}

// Gọi action (phương thức)
if (method_exists($controllerObj, $action)) {
    $controllerObj->$action();
} else {
    die("Action không tồn tại!");
}
?>