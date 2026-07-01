<?php 
$current_page = isset($_GET['controller']) ? $_GET['controller'] : 'home'; 
$current_action = isset($_GET['action']) ? $_GET['action'] : '';
?>

<nav class="sidebar">
    <div class="brand">
        <img src="public/images/logo_utt.jpg" alt="Logo">
        <span>UTT KTX</span>
    </div>

    <ul class="menu-list">
    <?php $role = $_SESSION['vai_tro'] ?? ''; ?>


        <?php if ($role === 'admin' ): ?>
        <li class="menu-item">
            <a href="index.php?controller=home" class="menu-link <?php echo ($current_page == 'home') ? 'active' : ''; ?>">
                <img src="public/images/icon_home.png" alt="Trang chủ">
                <span>Trang chủ</span>
            </a>
        </li>
        <?php endif; ?>

    <?php if ($role === 'admin' || $role === 'nhan_vien'): ?>
        <li class="menu-item">
            <a href="index.php?controller=phong" class="menu-link <?php echo ($current_page == 'phong') ? 'active' : ''; ?>">
                <img src="public/images/icon_room.png" alt="Quản lý phòng">
                <span>Quản lý phòng</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="index.php?controller=sinhvien" class="menu-link <?php echo ($current_page == 'sinhvien') ? 'active' : ''; ?>">
                <img src="public/images/icon_student.png" alt="Quản lý sinh viên">
                <span>Quản lý sinh viên</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="index.php?controller=hoadon&action=index" class="menu-link <?php echo ($current_page == 'hoadon' && $current_action == 'index') ? 'active' : ''; ?>">
                <img src="public/images/icon_bill.png" alt="Hoá đơn">
                <span>Hoá đơn & Điện nước</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="index.php?controller=hopdong" class="menu-link <?php echo ($current_page == 'hopdong') ? 'active' : ''; ?>">
                <img src="public/images/icon_trangchu/icon_money.png" alt="Hợp đồng">
                <span>Hợp đồng</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="index.php?controller=suco" class="menu-link <?php echo ($current_page == 'suco') ? 'active' : ''; ?>">
                <img src="public/images/icon_trangchu/icon_tools.png" alt="Quản lý sự cố">
                <span>Quản lý sự cố</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="index.php?controller=thanhtoan" class="menu-link <?php echo ($current_page == 'thanhtoan') ? 'active' : ''; ?>">
                <img src="public/images/icon_bill.png" alt="Thanh toán">
                <span>Quản lý Thanh toán</span>
            </a>
        </li>

        <?php if ($role === 'admin'): ?>
        <li class="menu-item">
            <a href="index.php?controller=dichvu" class="menu-link <?php echo ($current_page == 'dichvu') ? 'active' : ''; ?>">
                <img src="public/images/icon_service.png" alt="Dịch vụ KTX">
                <span>Dịch vụ KTX</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="index.php?controller=taikhoan" class="menu-link <?php echo ($current_page == 'taikhoan') ? 'active' : ''; ?>">
                <img src="public/images/icon_trangchu/icon_group.png" alt="Quản lý Tài khoản">
                <span>Quản lý Tài khoản</span>
            </a>
        </li>
        <?php endif; ?>
    <?php elseif ($role === 'sinh_vien'): ?>
        <li class="menu-item">
            <a href="index.php?controller=home" class="menu-link <?php echo ($current_page == 'home') ? 'active' : ''; ?>">
                <img src="public/images/icon_student.png" alt="Thông tin của tôi">
                <span>Thông tin của tôi</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="index.php?controller=hoadon&action=cua_toi" class="menu-link <?php echo ($current_page == 'hoadon' && $current_action == 'cua_toi') ? 'active' : ''; ?>">
                <img src="public/images/icon_bill.png" alt="Hóa đơn phòng tôi">
                <span>Hóa đơn phòng tôi</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="index.php?controller=hoadon&action=thanh_toan" class="menu-link <?php echo ($current_page == 'hoadon' && $current_action == 'thanh_toan') ? 'active' : ''; ?>">
                <img src="public/images/icon_bill.png" alt="Thanh toán">
                <span>Thanh toán hóa đơn</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="index.php?controller=suco" class="menu-link <?php echo ($current_page == 'suco') ? 'active' : ''; ?>">
                <img src="public/images/icon_trangchu/icon_tools.png" alt="Báo cáo sự cố">
                <span>Báo cáo sự cố</span>
            </a>
        </li>
    <?php endif; ?>
</ul>

<div class="sidebar-footer">&copy; 2026 UTT System</div>
</nav>
