<?php 
// Lấy tên controller hiện tại để làm sáng (active) menu
$current_page = isset($_GET['controller']) ? $_GET['controller'] : 'home'; 
?>
<nav class="sidebar">
    <div class="brand">
        <img src="public/images/logo_utt.jpg" alt="Logo"> 
        <span>UTT KTX</span>
    </div>
    <ul class="menu-list">
        <li class="menu-item">
            <a href="index.php?controller=home" class="menu-link <?php echo ($current_page == 'home') ? 'active' : '' ?>">
                <img src="public/images/icon_home.png" alt="Icon">
                <span>Trang chủ</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="index.php?controller=phong" class="menu-link <?php echo ($current_page == 'phong') ? 'active' : '' ?>">
                <img src="public/images/icon_room.png" alt="Icon">
                <span>Quản lý phòng</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="index.php?controller=sinhvien" class="menu-link <?php echo ($current_page == 'sinhvien') ? 'active' : '' ?>">
                <img src="public/images/icon_student.png" alt="Icon">
                <span>Quản lý sinh viên</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="index.php?controller=hoadon&action=index" class="menu-link <?php echo ($current_page == 'hoadon') ? 'active' : '' ?>">
                <img src="public/images/icon_bill.png" alt="Icon">
                <span>Hoá đơn & Điện nước</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="index.php?controller=dichvu" class="menu-link <?php echo ($current_page == 'dichvu') ? 'active' : '' ?>">
                <img src="public/images/icon_service.png" alt="Icon">
                <span>Dịch vụ</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="index.php?controller=hopdong" class="menu-link <?php echo ($current_page == 'hopdong') ? 'active' : '' ?>">
                <img src="public/images/icon_contract.png" alt="Icon">
                <span>Hợp đồng</span>
            </a>
        </li>
    </ul>
    <div class="sidebar-footer">&copy; 2026 UTT System</div>
</nav>