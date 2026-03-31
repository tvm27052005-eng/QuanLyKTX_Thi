<header class="header">
    <div class="header-title">Hệ thống quản lý Ký túc xá</div>
    <div class="user-info">
        <span class="user-name">Xin chào, <?php echo isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Admin'; ?></span>
        <a href="index.php?controller=auth&action=logout" class="btn-logout">
            <img src="public/images/icon_logout.png" alt="Logout"> Thoát
        </a>
    </div>
</header>