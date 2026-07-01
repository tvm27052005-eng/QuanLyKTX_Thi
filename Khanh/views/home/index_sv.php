<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cổng thông tin Sinh viên - UTT KTX</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <?php
require_once 'app/config/database.php';
require_once 'app/models/SinhVienModel.php';

$db = (new Database())->getConnection();
$svModel = new SinhVienModel($db);

// Lấy dữ liệu mới nhất từ DB
$sv = $svModel->getByMaSV($_SESSION['ma_sv']);
?>

    <?php include 'app/views/layout/sidebar.php'; ?>

    <div class="main-content">
        <?php include 'app/views/layout/header.php'; ?>

        <div class="page-content">
            <div class="content-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
                <h1 style="font-size:22px; color:#333;">Thông tin của tôi</h1>
                <div class="breadcrumb" style="color:#666; font-size:14px;">
                    <a href="index.php?controller=home" style="color:#1565C0; text-decoration:none;">Trang chủ</a> / Thông tin lưu trú
                </div>
            </div>

            <div class="panel" style="background:white; padding:25px; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.05); margin-bottom:25px; border-left:5px solid #1565C0;">
                <h2 style="color:#1a222b; margin-bottom:20px; font-size:20px;">Xin chào, <?php echo $_SESSION['fullname'] ?? 'Sinh viên'; ?>!</h2>

                <div style="background:#f4f6f9; padding:20px; border-radius:8px;">
                    <h3 style="font-size:16px; margin-bottom:15px; color:#333; border-bottom:1px solid #ddd; padding-bottom:10px;">Trạng thái lưu trú:</h3>
                    <p style="margin-bottom:10px;">
                        <strong>Mã sinh viên:</strong>
                        <span style="color:#555;"><?php echo $_SESSION['ma_sv'] ?? 'Chưa cập nhật'; ?></span>
                    </p>
                         <p style="margin-bottom:10px;">
                        <strong>Họ và tên:</strong>
                        <span style="color:#555;"><?php echo $_SESSION['fullname'] ?? 'Chưa cập nhật'; ?></span>
                    </p>
                        <p style="margin-bottom:10px;">
                        <strong>Lớp:</strong>
                        <span style="color:#555;"><?php echo $sv['lop'] ?? 'Chưa cập nhật'; ?></span>
                    </p>
                         <p style="margin-bottom:10px;">
                        <strong>Số điện thoại:</strong>
                        <span style="color:#555;"><?php echo $sv['sdt'] ?? 'Chưa cập nhật'; ?></span>
                    </p>
                    <p>
                      <strong>Phòng hiện tại:</strong>
                  <?php
                  // Kiểm tra xem biến $sv (lấy từ SinhVienModel) có chứa tên phòng không
                    if (!empty($sv['ma_phong'])) {
                    echo "<span style='background:#e8f5e9; color:#2e7d32; padding:4px 10px; border-radius:4px; font-weight:bold;'>Phòng: " . $sv['ma_phong'] . "</span>";
                    } else {
                    echo "<span style='background:#ffebee; color:#c62828; padding:4px 10px; border-radius:4px; font-weight:bold;'>Chưa được Ban quản lý xếp phòng</span>";
                    }
    ?>
</p>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:repeat(2, minmax(220px, 1fr)); gap:20px;">
                <a href="index.php?controller=hoadon&action=cua_toi" style="text-decoration:none;">
                    <div class="panel" style="padding:25px; border-top:4px solid #ffc107; transition:0.2s;">
                        <h3 style="color:#333; font-size:18px; margin-bottom:8px;">
                            <i class="fas fa-file-invoice-dollar" style="color:#ffc107; margin-right:10px;"></i>Hóa đơn điện nước
                        </h3>
                        <p style="color:#666; font-size:14px;">Xem và theo dõi hóa đơn phòng của bạn</p>
                    </div>
                </a>

                <a href="index.php?controller=suco" style="text-decoration:none;">
                    <div class="panel" style="padding:25px; border-top:4px solid #dc3545; transition:0.2s;">
                        <h3 style="color:#333; font-size:18px; margin-bottom:8px;">
                            <i class="fas fa-tools" style="color:#dc3545; margin-right:10px;"></i>Báo cáo sự cố
                        </h3>
                        <p style="color:#666; font-size:14px;">Gửi yêu cầu sửa chữa trang thiết bị</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</body>
</html>