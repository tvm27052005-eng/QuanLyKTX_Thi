<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Sinh viên - UTT KTX</title>
    <link rel="stylesheet" href="public/css/style.css">
    
    <style>
        /* CSS bổ trợ để trang trí các thành phần đặc thù */
        .status-badge { padding: 5px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .status-active { background: #e8f5e9; color: #2e7d32; } /* Có hiệu lực */
        .status-expired { background: #ffebee; color: #c62828; } /* Không hiệu lực */
        
        .form-edit-panel { display: none; margin-bottom: 20px; border-top: 3px solid #1565C0; }
        .form-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
        .form-group label { font-size: 13px; font-weight: 600; display: block; margin-bottom: 5px; color: #555; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; outline: none; }
        
        .btn-sm { padding: 6px 12px; font-size: 12px; border: none; border-radius: 4px; cursor: pointer; color: white; margin-right: 5px; font-weight: bold;}
        .btn-edit { background: #ff9800; }
        .btn-delete { background: #e53935; }
        
        .panel { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 20px; border: 1px solid #eaeaea;}
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table th, table td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; }
        table th { background: #f8f9fa; color: #333; font-weight: 600; }
    </style>
</head>
<body>

    <?php include 'app/views/layout/sidebar.php'; ?>

    <main class="main-content">
        <?php include 'app/views/layout/header.php'; ?>

        <div class="page-content">
            <h2 style="margin-bottom: 20px; color: #333;">Quản lý Hồ sơ & Xếp Phòng Sinh viên</h2>

            <div id="editPanel" class="panel form-edit-panel">
                <h3 style="margin-bottom: 15px; color: #1565C0;">Sửa thông tin & Xếp phòng</h3>
                <div class="form-wrap">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Mã sinh viên (Cố định)</label>
                            <input type="text" id="edit_ma_sv" readonly style="background: #f5f5f5; color: #888;">
                        </div>
                        <div class="form-group">
                            <label>Họ tên sinh viên</label>
                            <input type="text" id="edit_ho_ten">
                        </div>
                        <div class="form-group">
                            <label>Lớp</label>
                            <input type="text" id="edit_lop">
                        </div>
                        
                        <div class="form-group">
                            <label>Ngày sinh</label>
                            <input type="date" id="edit_ngay_sinh">
                        </div>
                        <div class="form-group">
                            <label>Giới tính</label>
                            <input type="text" id="edit_gioi_tinh" readonly style="background: #f5f5f5; color: #888;">
                        </div>
                        <div class="form-group">
                            <label style="color: #1565C0;">Xếp Phòng</label>
                            <select id="edit_phong" style="border: 1px solid #1565C0; background: #e3f2fd; font-weight: bold; color: #1565C0;">
                                <option value="">-- Đang tải... --</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="text" id="edit_sdt">
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label>Địa chỉ quê quán</label>
                            <input type="text" id="edit_dia_chi">
                        </div>
                    </div>
                    <div style="margin-top: 15px; display: flex; gap: 10px;">
                        <button class="btn btn-primary" onclick="updateSinhVien()">Cập nhật & Xếp phòng</button>
                        <button class="btn btn-secondary" onclick="hideEditPanel()">Hủy bỏ</button>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3 style="margin: 0;">Danh sách sinh viên</h3>
                    <div class="search-box">
                        <input type="text" id="searchKeyword" placeholder="Tìm theo tên hoặc mã SV..." style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; width: 250px;">
                        <button class="btn btn-primary btn-sm" style="padding: 9px 15px;" onclick="searchSV()">Tìm kiếm</button>
                    </div>
                </div>

                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Mã SV</th>
                                <th>Họ tên</th>
                                <th>Lớp</th>
                                <th>Phòng</th>
                                <th>SĐT</th>
                                <th style="text-align: center;">Trạng thái HĐ</th>
                                <th style="text-align: center;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="svTableBody">
                            <tr><td colspan="7" style="text-align:center; padding:30px; color: #888;">Đang tải danh sách sinh viên...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        const apiSV = "api/sinhvien_api.php";

        // Hàm Render bảng để dùng chung cho Load và Search
        function renderTable(data) {
            let html = "";
            if (!data || data.length === 0) {
                html = `<tr><td colspan="7" style="text-align:center; padding:20px; color: #888;">Không tìm thấy dữ liệu sinh viên nào.</td></tr>`;
            } else {
                data.forEach(sv => {
                    const isHieuLuc = sv.trang_thai_hop_dong === 'Hieu_luc';
                    const statusClass = isHieuLuc ? 'status-active' : 'status-expired';
                    const statusText = isHieuLuc ? 'Có hiệu lực' : 'Không hiệu lực';
                    
                    // Highlight nếu đã có phòng
                    const roomDisplay = sv.ma_phong ? `<strong style="color:#1565C0;">${sv.ma_phong}</strong>` : `<i style="color:#e65100;">Chưa xếp</i>`;
                    
                    html += `
                        <tr>
                            <td><strong>${sv.ma_sv}</strong></td>
                            <td>${sv.ho_ten}</td>
                            <td>${sv.lop}</td>
                            <td>${roomDisplay}</td>
                            <td>${sv.sdt}</td>
                            <td style="text-align: center;">
                                <span class="status-badge ${statusClass}">${statusText}</span>
                            </td>
                            <td style="text-align: center;">
                                <button class="btn-sm btn-edit" onclick='showEditPanel(${JSON.stringify(sv)})'>Sửa / Xếp phòng</button>
                                <button class="btn-sm btn-delete" onclick="deleteSV('${sv.ma_sv}')">Xóa</button>
                            </td>
                        </tr>
                    `;
                });
            }
            document.getElementById("svTableBody").innerHTML = html;
        }

        async function loadSV() {
            try {
                const res = await fetch(apiSV);
                const result = await res.json();
                renderTable(result.data);
            } catch (e) { alert("Lỗi tải dữ liệu!"); }
        }

        // Hiện form sửa + Gọi API lấy danh sách phòng
        async function showEditPanel(sv) {
            document.getElementById("editPanel").style.display = "block";
            document.getElementById("edit_ma_sv").value = sv.ma_sv;
            document.getElementById("edit_ho_ten").value = sv.ho_ten;
            document.getElementById("edit_lop").value = sv.lop;
            document.getElementById("edit_ngay_sinh").value = sv.ngay_sinh;
            document.getElementById("edit_gioi_tinh").value = sv.gioi_tinh;
            document.getElementById("edit_sdt").value = sv.sdt;
            document.getElementById("edit_dia_chi").value = sv.dia_chi;
            
            // Tải danh sách phòng dựa theo giới tính
            const selectPhong = document.getElementById("edit_phong");
            selectPhong.innerHTML = '<option value="">-- Đang tải phòng... --</option>';
            
            try {
                const res = await fetch(`${apiSV}?action=get_rooms&gioi_tinh=${sv.gioi_tinh}`);
                const result = await res.json();
                
                let options = `<option value="">-- Rút khỏi phòng (Chưa xếp) --</option>`;
                let isCurrentRoomIncluded = false;
                
                if (result.success && result.data) {
                    result.data.forEach(room => {
                        const isSelected = room.id == sv.phong_id ? 'selected' : '';
                        if (room.id == sv.phong_id) isCurrentRoomIncluded = true;
                        options += `<option value="${room.id}" ${isSelected}>${room.ma_phong} (Trống ${room.so_nguoi_toi_da - room.so_nguoi_hien_tai} giường)</option>`;
                    });
                }
                
                // Trường hợp phòng đang ở đã "Full" nên không trả về từ API, vẫn phải hiện tên phòng đó
                if (sv.phong_id && !isCurrentRoomIncluded) {
                    options += `<option value="${sv.phong_id}" selected>${sv.ma_phong} (Phòng đang ở)</option>`;
                }
                
                selectPhong.innerHTML = options;
            } catch (e) {
                selectPhong.innerHTML = '<option value="">Lỗi tải phòng</option>';
            }

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function hideEditPanel() { document.getElementById("editPanel").style.display = "none"; }

        // Gửi dữ liệu cập nhật kèm theo phong_id
        async function updateSinhVien() {
            const data = {
                ma_sv: document.getElementById("edit_ma_sv").value,
                ho_ten: document.getElementById("edit_ho_ten").value,
                lop: document.getElementById("edit_lop").value,
                ngay_sinh: document.getElementById("edit_ngay_sinh").value,
                gioi_tinh: document.getElementById("edit_gioi_tinh").value,
                sdt: document.getElementById("edit_sdt").value,
                dia_chi: document.getElementById("edit_dia_chi").value,
                phong_id: document.getElementById("edit_phong").value // Đẩy phong_id lên API
            };

            try {
                const res = await fetch(apiSV, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await res.json();
                alert(result.message);
                if (result.success) { hideEditPanel(); loadSV(); }
            } catch (e) { alert("Lỗi cập nhật!"); }
        }

        async function deleteSV(ma_sv) {
            if (!confirm("CẢNH BÁO: Xóa sinh viên sẽ xóa toàn bộ hợp đồng liên quan. Bạn chắc chắn chứ?")) return;
            try {
                const res = await fetch(apiSV, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ma_sv: ma_sv })
                });
                const result = await res.json();
                alert(result.message);
                if (result.success) loadSV();
            } catch (e) { alert("Lỗi khi xóa!"); }
        }

        async function searchSV() {
            const kw = document.getElementById("searchKeyword").value.trim();
            try {
                const res = await fetch(apiSV + "?keyword=" + encodeURIComponent(kw));
                const result = await res.json();
                renderTable(result.data);
            } catch (e) { alert("Lỗi tìm kiếm!"); }
        }

        document.getElementById("searchKeyword").addEventListener("keypress", (e) => {
            if (e.key === "Enter") searchSV();
        });

        loadSV();
    </script>
</body>
</html>