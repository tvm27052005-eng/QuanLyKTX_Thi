<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'app/config/database.php';

$controller = isset($_GET['controller']) ? $_GET['controller'] : 'home'; 
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// ==========================================
// 1. BẢO VỆ ROUTE: KIỂM TRA ĐĂNG NHẬP
// ==========================================
if (!isset($_SESSION['user_id']) && $controller != 'xac_thuc') {
    header("Location: index.php?controller=xac_thuc&action=login");
    exit();
}

// ==========================================
// 2. BỘ LỌC QUYỀN (MIDDLEWARE) - BƯỚC 4
// ==========================================
if (isset($_SESSION['vai_tro']) && $controller != 'xac_thuc') {
    $role = $_SESSION['vai_tro'];
    
    // Danh sách các trang yêu cầu quyền đặc biệt
    $admin_only_controllers = ['dichvu']; 
    $staff_controllers = ['phong', 'sinhvien', 'hopdong']; 

    // Chặn 1: Chỉ Admin mới được vào trang Quản lý Dịch vụ
    if (in_array($controller, $admin_only_controllers) && $role !== 'admin') {
        die("<div style='text-align:center; margin-top:100px; font-family:sans-serif;'>
                <h1 style='color:#c62828;'>🚫 Truy cập bị từ chối</h1>
                <p>Bạn không có quyền truy cập trang này. (Chỉ dành cho Quản trị viên)</p>
                <a href='index.php?controller=home' style='color:#1565C0; text-decoration:none; font-weight:bold;'>&larr; Quay lại Trang chủ</a>
             </div>");
    }

    // Chặn 2: Sinh viên tuyệt đối không được vào các trang Quản lý (Phòng, Sinh viên, Hợp đồng)
    if (in_array($controller, $staff_controllers) && $role === 'sinh_vien') {
        die("<div style='text-align:center; margin-top:100px; font-family:sans-serif;'>
                <h1 style='color:#c62828;'>🚫 Truy cập bị từ chối</h1>
                <p>Khu vực này chỉ dành cho Ban quản lý Ký túc xá.</p>
                <a href='index.php?controller=home' style='color:#1565C0; text-decoration:none; font-weight:bold;'>&larr; Quay lại Trang chủ</a>
             </div>");
    }
}

// ==========================================
// 3. ĐỊNH TUYẾN (ROUTING)
// ==========================================
switch ($controller) {
    case 'taikhoan':
        require_once 'app/controllers/TaiKhoanController.php';
        $controllerObj = new TaiKhoanController();
        break;
    case 'xac_thuc':
        require_once 'app/controllers/XacThucController.php';
        $controllerObj = new XacThucController();
        break;
        
    case 'hoadon':
        require_once 'app/controllers/HoaDonController.php';
        $controllerObj = new HoaDonController();
        break;
        
    case 'home':
        require_once 'app/controllers/HomeController.php';
        $controllerObj = new HomeController();
        break;
        
    case 'sinhvien':
        require_once 'app/controllers/SinhVienController.php';
        $controllerObj = new SinhVienController();
        break;
        
    case 'hopdong':
        require_once 'app/controllers/HopDongController.php';
        $controllerObj = new HopDongController();
        break;
        
    case 'dichvu':
         require_once 'app/controllers/DichVuController.php';
         $controllerObj = new DichVuController();
         // Đã xóa dòng $controllerObj->index(); bị thừa ở đây
         break;
         
    case 'phong':
        require_once 'app/controllers/PhongController.php';
        $controllerObj = new PhongController();
        break;
        
    case 'suco':
        require_once 'app/controllers/SuCoController.php';
        $controllerObj = new SuCoController();
        break;
    case 'thanhtoan':
        require_once 'app/controllers/ThanhToanController.php';
        $controllerObj = new ThanhToanController();
        break;


    default:
        die("<h2 style='text-align:center; margin-top:50px;'>404 - Không tìm thấy trang!</h2>");
}

// Gọi action (phương thức)
if (method_exists($controllerObj, $action)) {
    $controllerObj->$action();
} else {
    die("<h2 style='text-align:center; margin-top:50px;'>Lỗi: Chức năng (Action) không tồn tại!</h2>");
}
?>