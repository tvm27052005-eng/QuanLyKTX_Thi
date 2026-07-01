<header class="header">
    <div class="header-title">
    <?php 
        if (isset($_SESSION['vai_tro']) && $_SESSION['vai_tro'] === 'sinh_vien') {
            echo "Cổng thông tin Sinh viên";
        } else {
            echo "Hệ thống quản lý Ký túc xá";
        }
    ?>
</div>

    <div class="user-info">
        <span class="user-name">Xin chào, <?php echo isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Admin'; ?></span>
        
        <a href="index.php?controller=xac_thuc&action=changePassword" class="btn-logout" style="background: #e3f2fd; color: #1565C0;">
            <i class="fas fa-key"></i> Đổi mật khẩu
        </a>

        <a href="index.php?controller=xac_thuc&action=logout" class="btn-logout">
            <img src="public/images/icon_logout.png" alt="Logout"> Thoát
        </a>
    </div>
</header>