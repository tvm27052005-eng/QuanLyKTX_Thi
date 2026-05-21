<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Sự cố - UTT KTX</title>
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        /* ===== BÊ Y NGUYÊN FORM CỦA TRANG QUẢN LÝ PHÒNG SANG ===== */
        .toolbar {
            display: flex; justify-content: space-between; align-items: center; gap: 10px;
            padding: 18px 24px; background: white;
            border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            margin-bottom: 20px; flex-wrap: wrap;
        }
        .search-box {
            display: flex; align-items: center;
            border: 1px solid #ddd; border-radius: 7px;
            overflow: hidden; min-width: 300px;
        }
        .search-box input {
            border: none; outline: none; padding: 9px 14px;
            font-size: 14px; width: 100%;
        }
        .search-box button {
            background: #1565C0; color: white; border: none;
            padding: 9px 14px; cursor: pointer; font-size: 14px; font-weight: bold;
        }
        
        .layout-grid { display: grid; grid-template-columns: 320px 1fr; gap: 20px; align-items: start; }
        .panel { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px; color: #333; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; outline: none; transition: 0.2s; }
        .form-control:focus { border-color: #1565C0; }
        
        .btn { padding: 10px 15px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; color: white; width: 100%; transition: 0.2s;}
        .btn-primary { background: #1565C0; } .btn-primary:hover { background: #0D47A1; }
        .btn-danger { background: #e53935; padding: 6px 12px; font-size: 12px; width: auto; border-radius: 4px; }
        
        table { width: 100%; border-collapse: collapse; }
        table th, table td { padding: 12px 15px; border-bottom: 1px solid #eee; text-align: left; font-size: 14px; }
        table th { background: #f8f9fa; color: #555; font-weight: 600; }
        
        .badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    min-width: 90px;
    text-align: center;
}
        .bg-warning { background: #fff3cd; color: #856404; }
        .bg-info { background: #d1ecf1; color: #0c5460; }
        .bg-success { background: #d4edda; color: #155724; }
    </style>
</head>
<body>

    <?php include 'app/views/layout/sidebar.php'; ?>

    <div class="main-content">
        
        <?php include 'app/views/layout/header.php'; ?>

        <div class="page-content">
            
            <div class="toolbar">
                <h2 style="margin: 0; color: #1565C0; font-size: 22px;">Tiếp nhận & Xử lý Sự cố</h2>
                <div class="search-box">
                    <input type="text" id="searchKeyword" placeholder="Tìm theo phòng hoặc nội dung...">
                    <button onclick="loadSuCo()">Tìm kiếm</button>
                </div>
            </div>

            <div class="layout-grid">
                <div class="panel">
                    <h3 style="color: #e65100; margin-top: 0; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; font-size: 16px;">
                        + Báo cáo hỏng hóc
                    </h3>
                    
                    <div class="form-group">
                        <label>Phòng xảy ra sự cố (*)</label>
                        <select id="phongSelect" class="form-control" style="border-color: #e65100;"></select>
                    </div>

                    <div class="form-group">
                        <label>Người báo cáo</label>
                        <input type="text" id="nguoiBao" class="form-control" placeholder="Tên SV hoặc Cán bộ">
                    </div>

                    <div class="form-group">
                        <label>Nội dung sự cố (*)</label>
                        <textarea id="noiDung" class="form-control" rows="4" placeholder="Ví dụ: Cháy bóng đèn..."></textarea>
                    </div>

                    <button class="btn btn-primary" style="background: #e65100;" onclick="saveSuCo()">Ghi nhận sự cố</button>
                </div>

                <div class="panel" style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 25%;">Phòng</th>
                                <th style="width: 25%;">Nội dung sự cố</th>
                                <th style="width: 25%;">Thời gian báo</th>
                                <th style="width: 25%; text-align: center;">Trạng thái</th>
                                
                            </tr>
                        </thead>
                        <tbody id="sucoTableBody">
                            <tr><td colspan="4" style="text-align: center; padding: 30px; color: #888;">Đang tải dữ liệu...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
        API_SUCO = 'api/suco_api.php';
        const USER_ROLE = '<?php echo $_SESSION['vai_tro'] ?? ''; ?>';
        const IS_STAFF = (USER_ROLE === 'admin' || USER_ROLE === 'nhan_vien');
        const IS_STUDENT = (USER_ROLE === 'sinh_vien');
        const STUDENT_NAME = <?php echo json_encode($_SESSION['fullname'] ?? ''); ?>;
        const STUDENT_ROOM_ID = <?php echo json_encode($_SESSION['phong_id_cua_sv'] ?? ''); ?>;
        
        async function loadRooms() {
    try {
        // Sinh viên chỉ được báo sự cố ở phòng của mình
        if (IS_STUDENT) {
            const roomId = String(STUDENT_ROOM_ID || '').trim();
            if (!roomId) {
                document.getElementById('phongSelect').innerHTML = '<option value="">Chưa được xếp phòng</option>';
                document.getElementById('phongSelect').disabled = true;
                return;
            }
            document.getElementById('phongSelect').innerHTML = `<option value="${roomId}">Phòng của tôi (ID: ${roomId})</option>`;
            document.getElementById('phongSelect').disabled = true;
            return;
        }

        const res = await fetch('api/phong_api.php?action=get_all');
        const rooms = await res.json();
        let html = '<option value="">-- Chọn phòng --</option>';
        rooms.forEach(r => html += `<option value="${r.id}">${r.ma_phong}</option>`);
        document.getElementById('phongSelect').innerHTML = html;
    } catch(e) { console.log("Lỗi tải phòng: ", e); }
}

        async function loadSuCo() {
            const kw = document.getElementById('searchKeyword').value;
            try {
                const res = await fetch(`${API_SUCO}?action=get_all&keyword=${encodeURIComponent(kw)}`);
                const result = await res.json();
                let html = '';
                
               if(!result.data || result.data.length === 0) {
    html = '<tr><td colspan="4" style="text-align:center; padding: 30px; color: #888;">Chưa có báo cáo sự cố nào.</td></tr>';
} else {
    result.data.forEach(sc => {
        let badgeCls = sc.trang_thai === 'cho_xu_ly' ? 'bg-warning' : (sc.trang_thai === 'dang_sua' ? 'bg-info' : 'bg-success');
        const statusText = sc.trang_thai === 'cho_xu_ly' ? 'Chờ xử lý' : (sc.trang_thai === 'dang_sua' ? 'Đang sửa' : 'Đã xong');

        let statusHtml = `<span class="badge ${badgeCls}">${statusText}</span>`;
        if (IS_STAFF) {
            statusHtml = `
                <select class="form-control" onchange="changeStatus(${sc.id}, this.value)">
                    <option value="cho_xu_ly" ${sc.trang_thai === 'cho_xu_ly' ? 'selected' : ''}>Chờ xử lý</option>
                    <option value="dang_sua" ${sc.trang_thai === 'dang_sua' ? 'selected' : ''}>Đang sửa</option>
                    <option value="da_xong" ${sc.trang_thai === 'da_xong' ? 'selected' : ''}>Đã xong</option>
                </select>
            `;
        }

        html += `
            <tr>
                <td>
                    <strong style="color:#1565C0; font-size:15px;">${sc.ma_phong || 'N/A'}</strong><br>
                    <small style="color:#777;">${sc.nguoi_bao || ''}</small>
                </td>
                <td>${sc.noi_dung}</td>
                <td style="color:#666; font-size:13px;">${sc.ngay_bao}</td>
                <td style="text-align:center;">${statusHtml}</td>
            </tr>
        `;
    });
}
                document.getElementById('sucoTableBody').innerHTML = html;
            } catch(e) { console.log("Lỗi tải sự cố: ", e); }
        }

        async function saveSuCo() {
    const data = {
        phong_id: IS_STUDENT ? STUDENT_ROOM_ID : document.getElementById('phongSelect').value,
        nguoi_bao: IS_STUDENT ? STUDENT_NAME : document.getElementById('nguoiBao').value,
        noi_dung: document.getElementById('noiDung').value
    };

    if(!data.phong_id || !data.noi_dung) return alert("Vui lòng chọn phòng và mô tả nội dung hỏng hóc!");

    const res = await fetch(`${API_SUCO}?action=insert`, { method: 'POST', body: JSON.stringify(data) });
    const result = await res.json();
    if(result.success) {
        document.getElementById('noiDung').value = '';
        loadSuCo();
    } else {
        alert(result.message || 'Lỗi ghi nhận sự cố');
    }
}

        async function changeStatus(id, newStatus) {
            await fetch(`${API_SUCO}?action=update_status`, { method: 'PUT', body: JSON.stringify({id: id, trang_thai: newStatus}) });
            loadSuCo();
        }

        async function deleteSuCo(id) {
            if(!confirm("Xóa bản ghi sự cố này?")) return;
            const res = await fetch(`${API_SUCO}?action=delete`, { method: 'DELETE', body: JSON.stringify({id: id}) });
            const result = await res.json();
            if(result.success) loadSuCo();
        }

        // Bắt sự kiện ấn Enter để tìm kiếm
        document.getElementById('searchKeyword').addEventListener('keypress', function(e) {
            if(e.key === 'Enter') loadSuCo();
        });

        // Tự động load dữ liệu khi vào trang
        document.addEventListener('DOMContentLoaded', () => { loadRooms(); loadSuCo(); });
    </script>
</body>
</html>