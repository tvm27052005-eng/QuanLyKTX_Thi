<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Tài khoản - UTT KTX</title>
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        .panel { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 20px; border: 1px solid #eaeaea; }
        .form-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { font-size: 14px; font-weight: 600; display: block; margin-bottom: 6px; color: #333; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; outline: none; font-size: 13px; }
        .form-group input:focus, .form-group select:focus { border-color: #1565C0; box-shadow: 0 0 0 3px rgba(21, 101, 192, 0.1); }
        
        .btn { padding: 10px 16px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; transition: all 0.3s; font-size: 13px; }
        .btn-primary { background: #1565C0; color: white; }
        .btn-primary:hover { background: #1043a5; }
        .btn-secondary { background: #757575; color: white; }
        .btn-secondary:hover { background: #616161; }
        .btn-danger { background: #e53935; color: white; }
        .btn-danger:hover { background: #d32f2f; }
        .btn-warning { background: #f57c00; color: white; }
        .btn-warning:hover { background: #e65100; }
        
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table th, table td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; font-size: 13px; }
        table th { background: #f8f9fa; color: #333; font-weight: 600; }
        table tr:hover { background: #f5f5f5; }
        
        .vai-tro-badge { padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .vai-tro-admin { background: #ffebee; color: #c62828; }
        .vai-tro-nhan-vien { background: #e8f5e9; color: #2e7d32; }
        .vai-tro-sinh-vien { background: #e3f2fd; color: #1565C0; }
        
        .form-edit-panel { display: none; margin-bottom: 20px; border-top: 3px solid #1565C0; }
        .form-edit-panel.show { display: block; }
        
        .search-box { display: flex; gap: 10px; margin-bottom: 15px; }
        .search-box input { flex: 1; max-width: 300px; }
        
        .action-buttons { display: flex; gap: 8px; flex-wrap: wrap; }
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 15px; }
        .alert-success { background: #e8f5e9; color: #2e7d32; border-left: 4px solid #4caf50; }
        .alert-error { background: #ffebee; color: #c62828; border-left: 4px solid #f44336; }
    </style>
</head>
<body>
    <?php include 'app/views/layout/sidebar.php'; ?>

    <main class="main-content">
        <?php include 'app/views/layout/header.php'; ?>

        <div class="page-content">
            <h2 style="margin-bottom: 5px; color: #333;">Quản lý Tài khoản</h2>
            <p style="color: #888; margin-bottom: 20px; font-size: 14px;">Quản lý tài khoản sinh viên & nhân viên</p>

            <div id="alertBox"></div>

            <!-- Form tạo/sửa tài khoản -->
            <div id="formPanel" class="panel form-edit-panel show">
                <h3 style="margin-bottom: 15px; color: #1565C0;">
                    <span id="formTitle">Tạo tài khoản mới</span>
                </h3>
                <form id="formTaiKhoan" onsubmit="return saveTaiKhoan(event);">
                    <input type="hidden" id="form_id">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Tên đăng nhập *</label>
                            <input type="text" id="form_ten_dang_nhap" required>
                        </div>
                        <div class="form-group">
                            <label>Mật khẩu <small>(để trống nếu không đổi)</small></label>
                            <input type="password" id="form_mat_khau" minlength="6">
                        </div>
                        <div class="form-group">
                            <label>Họ tên *</label>
                            <input type="text" id="form_ho_ten" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Vai trò *</label>
                            <select id="form_vai_tro" required onchange="toggleMaSVField()">
                                <option value="">-- Chọn vai trò --</option>
                                <option value="admin">Admin</option>
                                <option value="nhan_vien">Nhân viên</option>
                                <option value="sinh_vien">Sinh viên</option>
                            </select>
                        </div>
                        <div class="form-group" id="maSVField" style="display: none;">
                            <label>Mã sinh viên (liên kết) <small>Chỉ dành cho sinh viên</small></label>
                            <input type="text" id="form_ma_sv_lien_ket">
                        </div>
                    </div>

                    <div style="margin-top: 15px; display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-primary">Lưu</button>
                        <button type="button" class="btn btn-secondary" onclick="cancelForm()">Hủy bỏ</button>
                    </div>
                </form>
            </div>

            <!-- Danh sách tài khoản -->
            <div class="panel">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 10px;">
                    <h3 style="margin: 0;">Danh sách tài khoản</h3>
                    <div class="search-box">
                        <input type="text" id="searchKeyword" placeholder="Tìm theo tên, tài khoản hoặc mã SV...">
                        <button class="btn btn-primary btn-sm" onclick="searchTaiKhoan()"> Tìm</button>
                    </div>
                </div>

                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên đăng nhập</th>
                                <th>Họ tên</th>
                                <th>Vai trò</th>
                                <th>Mã SV liên kết</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr>
                                <td colspan="6" style="text-align: center; color: #999; padding: 30px;">
                                    Đang tải dữ liệu...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        const API_TAIKHOAN = 'api/tai_khoan_api.php';

        function toggleMaSVField() {
            const vaiTro = document.getElementById('form_vai_tro').value;
            const maSVField = document.getElementById('maSVField');
            maSVField.style.display = vaiTro === 'sinh_vien' ? 'block' : 'none';
        }

        function showAlert(message, type = 'success') {
            const alertBox = document.getElementById('alertBox');
            const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
            alertBox.innerHTML = `<div class="alert ${alertClass}">${message}</div>`;
            setTimeout(() => {
                alertBox.innerHTML = '';
            }, 5000);
        }

        function renderTable(list) {
            const body = document.getElementById('tableBody');

            if (!list || list.length === 0) {
                body.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align:center; padding:30px; color:#999;">
                            Chưa có tài khoản nào
                        </td>
                    </tr>
                `;
                return;
            }

            const roleText = {
                admin: ' Admin',
                nhan_vien: 'Nhân viên',
                sinh_vien: 'Sinh viên'
            };

            let html = '';
            list.forEach(tk => {
                const vtClass = `vai-tro-${String(tk.vai_tro).replace('_','-')}`;
                const vtText = roleText[tk.vai_tro] || tk.vai_tro;
                html += `
                    <tr>
                        <td>${tk.id}</td>
                        <td><strong>${tk.ten_dang_nhap}</strong></td>
                        <td>${tk.ho_ten}</td>
                        <td>
                            <span class="vai-tro-badge ${vtClass}">${vtText}</span>
                        </td>
                        <td>${tk.ma_sv_lien_ket || '-'}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-primary btn-sm" onclick="editTaiKhoanFromRow(this, ${tk.id})"> Sửa</button>
                                <button class="btn btn-warning btn-sm" onclick="resetPasswordFromRow(this, ${tk.id})"> Reset</button>
                                <button class="btn btn-danger btn-sm" onclick="deleteTaiKhoanFromRow(this, ${tk.id})"> Xóa</button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            body.innerHTML = html;
        }

        function loadTaiKhoan(keyword = '') {
            const url = `${API_TAIKHOAN}?action=get_all&keyword=${encodeURIComponent(keyword)}`;
            fetch(url)
                .then(res => res.json())
                .then(result => {
                    if (!result.success) {
                        showAlert(result.message || 'Lỗi tải danh sách tài khoản', 'error');
                        return;
                    }
                    renderTable(result.data || []);
                })
                .catch(err => {
                    showAlert('Lỗi tải tài khoản: ' + err.message, 'error');
                });
        }

        function saveTaiKhoan(event) {
            event.preventDefault();

            const formData = new FormData();
            formData.append('id', document.getElementById('form_id').value || '');
            formData.append('ten_dang_nhap', document.getElementById('form_ten_dang_nhap').value.trim());
            formData.append('mat_khau', document.getElementById('form_mat_khau').value);
            formData.append('ho_ten', document.getElementById('form_ho_ten').value.trim());
            formData.append('vai_tro', document.getElementById('form_vai_tro').value);
            formData.append('ma_sv_lien_ket', document.getElementById('form_ma_sv_lien_ket').value.trim());

            fetch(`${API_TAIKHOAN}?action=save`, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    cancelForm();
                    loadTaiKhoan();
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(err => {
                showAlert('Lỗi: ' + err.message, 'error');
            });

            return false;
        }

        function editTaiKhoanFromRow(btn, id) {
            const row = btn.closest('tr');
            const tdn = row.cells[1].textContent.trim();
            const ht = row.cells[2].textContent.trim();
            const vtBadge = row.cells[3].innerText || '';
            const maSV = row.cells[4].textContent.trim();

            document.getElementById('form_id').value = id;
            document.getElementById('form_ten_dang_nhap').value = tdn;
            document.getElementById('form_ho_ten').value = ht;
            document.getElementById('form_mat_khau').value = '';

            let vaiTro = 'nhan_vien';
            if (vtBadge.includes('Admin')) vaiTro = 'admin';
            if (vtBadge.includes('Sinh viên')) vaiTro = 'sinh_vien';
            document.getElementById('form_vai_tro').value = vaiTro;

            document.getElementById('form_ma_sv_lien_ket').value = (maSV && maSV !== '-') ? maSV : '';

            document.getElementById('formTitle').textContent = 'Sửa tài khoản: ' + tdn;
            document.getElementById('formPanel').classList.add('show');

            toggleMaSVField();
            document.getElementById('form_ten_dang_nhap').focus();
        }

        function cancelForm() {
            document.getElementById('formTaiKhoan').reset();
            document.getElementById('form_id').value = '';
            document.getElementById('formTitle').textContent = 'Tạo tài khoản mới';
            document.getElementById('formPanel').classList.remove('show');
            document.getElementById('maSVField').style.display = 'none';
        }

        function deleteTaiKhoanFromRow(btn, id) {
            const row = btn.closest('tr');
            const tenDangNhap = row.cells[1].textContent.trim();

            if (!confirm(`Bạn chắc chắn muốn xóa tài khoản "${tenDangNhap}"? Hành động này không thể hoàn tác!`)) {
                return;
            }

            const formData = new FormData();
            formData.append('id', id);

            fetch(`${API_TAIKHOAN}?action=delete`, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                showAlert(data.message, data.success ? 'success' : 'error');
                if (data.success) {
                    loadTaiKhoan();
                }
            })
            .catch(err => {
                showAlert('Lỗi: ' + err.message, 'error');
            });
        }

        function resetPasswordFromRow(btn, id) {
            const row = btn.closest('tr');
            const tenDangNhap = row.cells[1].textContent.trim();

            if (!confirm(`Reset mật khẩu của "${tenDangNhap}" về 123456?`)) {
                return;
            }

            const formData = new FormData();
            formData.append('id', id);

            fetch(`${API_TAIKHOAN}?action=reset_password`, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                showAlert(data.message, data.success ? 'success' : 'error');
            })
            .catch(err => {
                showAlert('Lỗi: ' + err.message, 'error');
            });
        }

        function searchTaiKhoan() {
            const keyword = document.getElementById('searchKeyword').value;
            loadTaiKhoan(keyword);
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadTaiKhoan();
            document.getElementById('searchKeyword').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    searchTaiKhoan();
                }
            });
        });
    </script>
</body>
</html>