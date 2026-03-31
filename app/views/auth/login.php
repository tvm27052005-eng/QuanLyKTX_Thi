<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống KTX - UTT</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { height: 100vh; display: flex; }
        
        /* Cột bên trái: Hình ảnh */
        .left-section {
            width: 50%;
            /* Lưu ý đường dẫn ảnh public/images/ */
            background: url("public/images/bg_utt.jpg") no-repeat center center/cover;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 40px;
            color: white;
        }
        .left-section::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(10, 25, 60, 0.3);
            z-index: 1;
        }
        .content-overlay { position: relative; z-index: 2; }
        
        .logo-area { display: flex; align-items: center; gap: 10px; margin-bottom: 20px;}
        .logo-area img { height: 50px; }
        .logo-area span { font-size: 18px; font-weight: 500; }

        .hero-text { margin-top: auto; margin-bottom: 100px; }
        .hero-text h1 { font-size: 48px; font-weight: 700; line-height: 1.2; margin-bottom: 20px; }
        .hero-text p { font-size: 16px; opacity: 0.9; line-height: 1.5; max-width: 80%; }
        
        .footer-copy { font-size: 12px; opacity: 0.7; margin-top: auto; margin-bottom: 20px;}

        /* Cột bên phải: Form đăng nhập */
        .right-section {
            width: 50%;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }
        .login-box { width: 100%; max-width: 400px; }
        .login-box h2 { font-size: 32px; color: #1a1a1a; margin-bottom: 10px; }
        .login-box .subtitle { color: #666; margin-bottom: 40px; font-size: 14px; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #333; font-size: 14px; }
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            outline: none;
            transition: 0.3s;
        }
        .form-group input:focus { border-color: #1565C0; }

        .options { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; font-size: 14px; }
        .options a { text-decoration: none; color: #E65100; font-weight: 600; }
        
        .btn-login {
            width: 100%; padding: 14px; background-color: #1565C0; color: white; border: none;
            border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer; transition: 0.3s;
        }
        .btn-login:hover { background-color: #0D47A1; }
        
        .error-msg { color: red; margin-bottom: 15px; font-size: 14px; background: #ffebee; padding: 10px; border-radius: 5px;}
    </style>
</head>
<body>

    <div class="left-section">
        <div class="content-overlay logo-area">
            <img src="public/images/logo_utt.jpg" alt="Logo UTT">
            <span>UTT - Dormitory</span>
        </div>
        
        <div class="content-overlay hero-text">
            <h1>Quản lý hiệu quả,<br>An toàn tối đa.</h1>
            <p>Hệ thống quản lý ký túc xá tập trung dành cho trường Đại học Công nghệ Giao thông Vận tải. Nâng cao chất lượng đời sống sinh viên.</p>
        </div>

        <div class="content-overlay footer-copy">
            &copy; 2026 University of Transport Technology
        </div>
    </div>

    <div class="right-section">
        <div class="login-box">
            <h2>Đăng nhập hệ thống</h2>
            <p class="subtitle">Dành cho Cán bộ Quản lý & Nhân viên</p>

            <?php if(!empty($error)) echo "<div class='error-msg'>$error</div>"; ?>

            <form method="POST" action="index.php?controller=auth&action=login">
                <div class="form-group">
                    <label>Tên đăng nhập</label>
                    <input type="text" name="username" placeholder="Nhập mã quản lý" required>
                </div>

                <div class="form-group">
                    <label>Mật khẩu</label>
                    <input type="password" name="password" placeholder="Nhập mật khẩu" required>
                </div>

                <div class="options">
                    <label><input type="checkbox"> Ghi nhớ đăng nhập</label>
                    <a href="index.php?controller=auth&action=forgot">Quên mật khẩu?</a>
                </div>

                <button type="submit" class="btn-login">Đăng nhập</button>
            </form>
        </div>
    </div>

</body>
</html>