<div class="content-header" style="margin-bottom: 20px; display: flex; justify-content: flex-end;">
    <a href="index.php?controller=home" style="background: #f8f9fa; color: #1565C0; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-weight: 600; border: 1px solid #1565C0; display: flex; align-items: center; gap: 8px; transition: 0.3s;">
        <i class="fas fa-home"></i> Quay lại trang chủ
    </a>
</div>

<div class="panel" style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); max-width: 500px; margin: 0 auto;">
    
    <?php if(!empty($error)): ?>
        <div style="background: #ffebee; color: #c62828; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-weight: bold;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if(!empty($success)): ?>
        <div style="background: #e8f5e9; color: #2e7d32; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-weight: bold;">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?controller=xac_thuc&action=changePassword">
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: 600; margin-bottom: 5px;">Mật khẩu hiện tại (*)</label>
            <input type="password" name="old_password" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: 600; margin-bottom: 5px;">Mật khẩu mới (*)</label>
            <input type="password" name="new_password" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
        </div>

        <div style="margin-bottom: 25px;">
            <label style="display: block; font-weight: 600; margin-bottom: 5px;">Xác nhận mật khẩu mới (*)</label>
            <input type="password" name="confirm_password" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
        </div>

        <button type="submit" style="background: #1565C0; color: white; border: none; padding: 12px 20px; border-radius: 6px; font-weight: bold; cursor: pointer; width: 100%;">
            Cập nhật mật khẩu
        </button>
    </form>
</div>