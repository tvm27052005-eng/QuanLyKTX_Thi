<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Dịch vụ - UTT KTX</title>
    <link rel="stylesheet" href="public/css/style.css">
    
    <style>
        .grid-container { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 5px; font-size: 14px;}
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; outline: none; }
        .btn { padding: 8px 15px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; color: white;}
        .btn-primary { background: #1565C0; } .btn-warning { background: #f39c12; } .btn-danger { background: #e74c3c; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table th, table td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
        .checkbox-item { display: flex; align-items: center; gap: 10px; padding: 10px; border: 1px solid #eee; border-radius: 6px; margin-bottom: 10px; background: #f9f9f9; cursor: pointer; transition: 0.2s;}
        .checkbox-item:hover { background: #e8f5e9; border-color: #2e7d32;}
        .checkbox-item input { width: 18px; height: 18px; cursor: pointer; }
    </style>
</head>
<body>

    <?php include 'app/views/layout/sidebar.php'; ?>

    <main class="main-content">
        <?php include 'app/views/layout/header.php'; ?>

        <div class="page-content">
            <h2 style="margin-bottom: 20px; color: #333; font-weight: 600;">Quản lý Dịch vụ Ký túc xá</h2>

            <div class="grid-container">
                <div class="panel">
                    <div class="panel-header">
                        <h3 style="color: #1565C0; margin:0;">1. Danh mục Dịch vụ chung</h3>
                    </div>
                    <div style="padding: 20px;">
                        <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                            <input type="hidden" id="dv_id">
                            <div style="flex: 2;"><input type="text" id="dv_ten" class="form-control" placeholder="Tên dịch vụ "></div>
                            <div style="flex: 1;"><input type="number" id="dv_gia" class="form-control" placeholder="Đơn giá (VNĐ)"></div>
                            <button class="btn btn-primary" onclick="saveDV()">Lưu</button>
                        </div>

                        <table>
                            <thead>
                                <tr style="background: #f8f9fa;">
                                    <th>Tên dịch vụ</th> <th>Đơn giá</th> <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="dvTableBody">
                                <tr><td colspan="3" style="text-align: center;">Đang tải...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-header">
                        <h3 style="color: #2e7d32; margin:0;">2. Gán Dịch vụ theo Phòng</h3>
                    </div>
                    <div style="padding: 20px;">
                        <div class="form-group">
                            <label>họn Phòng cần cấu hình phí dịch vụ:</label>
                            <select id="phongSelect" class="form-control" style="border-color: #2e7d32; font-weight: bold; color: #2e7d32; background: #e8f5e9;" onchange="loadRoomServices()">
                                <option value="">-- Tải danh sách phòng... --</option>
                            </select>
                        </div>

                        <div style="margin-top: 20px;">
                            <label style="font-weight: 600; margin-bottom:10px; display:block;">Đánh dấu các dịch vụ phòng này sử dụng:</label>
                            <div id="serviceCheckboxes">
                                <div style="color: #888; font-style: italic; padding: 10px; background: #f5f5f5; border-radius: 6px;">Vui lòng chọn 1 phòng ở trên để xem.</div>
                            </div>
                        </div>

                        <button class="btn btn-primary" style="background: #2e7d32; width: 100%; margin-top: 15px; padding: 12px; font-size: 15px;" onclick="saveRoomServices()">Xác nhận gán dịch vụ</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        const apiDV = "api/dichvu_api.php";
        let globalServices = [];

        // Tải danh sách các loại dịch vụ
        async function loadDV() {
            const res = await fetch(`${apiDV}?action=get_all`);
            const result = await res.json();
            globalServices = result.data;
            let html = "";
            result.data.forEach(item => {
                html += `
                    <tr style="border-bottom: 1px solid #eee;">
                        <td><strong>${item.ten_dich_vu}</strong></td>
                        <td style="color:red; font-weight:bold;">${new Intl.NumberFormat('vi-VN').format(item.don_gia)} đ</td>
                        <td>
                            <button class="btn btn-warning" style="padding: 4px 8px; font-size: 12px;" onclick='editDV(${JSON.stringify(item)})'>Sửa</button>
                            <button class="btn btn-danger" style="padding: 4px 8px; font-size: 12px;" onclick="deleteDV(${item.id})">Xóa</button>
                        </td>
                    </tr>`;
            });
            document.getElementById('dvTableBody').innerHTML = html || '<tr><td colspan="3" style="text-align: center;">Chưa có dịch vụ nào</td></tr>';
            loadRoomServices(); // Cập nhật lại checkbox bên cột 2
        }

        async function saveDV() {
            const data = {
                id: document.getElementById('dv_id').value,
                ten_dich_vu: document.getElementById('dv_ten').value.trim(),
                don_gia: document.getElementById('dv_gia').value
            };
            if (!data.ten_dich_vu || Number(data.don_gia) <= 0) {
               return alert("Vui lòng nhập đủ thông tin hợp lệ!");
            }

            const res = await fetch(`${apiDV}?action=save`, {
                method: 'POST', body: JSON.stringify(data)
            });
            const result = await res.json();
            alert(result.message);
            if (result.success) {
                document.getElementById('dv_id').value = '';
                document.getElementById('dv_ten').value = '';
                document.getElementById('dv_gia').value = '';
                loadDV();
            }
        }

        function editDV(item) {
            document.getElementById('dv_id').value = item.id;
            document.getElementById('dv_ten').value = item.ten_dich_vu;
            document.getElementById('dv_gia').value = item.don_gia;
        }

        async function deleteDV(id) {
            if (!confirm("Bạn có chắc chắn muốn xóa dịch vụ này khỏi hệ thống?")) return;
            const res = await fetch(`${apiDV}?action=delete`, {
                method: 'POST', body: JSON.stringify({id: id})
            });
            const result = await res.json();
            alert(result.message);
            if (result.success) loadDV();
        }

        // Tải danh sách phòng vào Dropdown
        async function loadRoomsForSelect() {
            const res = await fetch('api/phong_api.php?action=get_all');
            const rooms = await res.json();
            let html = '<option value="">-- Hãy chọn 1 phòng --</option>';
            rooms.forEach(r => html += `<option value="${r.id}" data-loai="${r.loai_phong}">${r.ma_phong} (Phòng ${r.loai_phong})</option>`);
            document.getElementById('phongSelect').innerHTML = html;
        }

        // Tải các dịch vụ đã gán của 1 phòng
        async function loadRoomServices() {
            const phongSelect = document.getElementById('phongSelect');
            const phong_id = phongSelect.value;
            const container = document.getElementById('serviceCheckboxes');
            
            if (!phong_id) {
                container.innerHTML = '<div style="color: #888; font-style: italic; padding: 10px; background: #f5f5f5; border-radius: 6px;">Vui lòng chọn 1 phòng ở trên để xem.</div>';
                return;
            }

            // Kiểm tra phòng VIP
            const selectedOption = phongSelect.options[phongSelect.selectedIndex];
            const loaiPhong = selectedOption.dataset.loai || '';
            const isVIP = loaiPhong === 'Nam VIP' || loaiPhong === 'Nữ VIP';

            const res = await fetch(`${apiDV}?action=get_room_services&phong_id=${phong_id}`);
            const result = await res.json();
            const selectedIds = result.data;

            let html = '';
            globalServices.forEach(dv => {
                const tenLower = dv.ten_dich_vu.toLowerCase();
                const isDieuHoa = tenLower.includes('điều hoà') || tenLower.includes('điều hòa') || tenLower.includes('dieu hoa');
                const forceChecked = isVIP && isDieuHoa;
                const isChecked = forceChecked || selectedIds.includes(dv.id.toString());

                const disabledAttr = forceChecked ? 'disabled' : '';
                const vipBadge = forceChecked ? '<span style="background:#7b1fa2;color:#fff;font-size:11px;padding:2px 7px;border-radius:10px;margin-left:8px;">VIP</span>' : '';
                const wrapStyle = forceChecked ? 'background:#f3e5f5; border-color:#7b1fa2;' : '';

                html += `
                    <label class="checkbox-item" style="${wrapStyle}">
                        <input type="checkbox" class="room-service-cb" value="${dv.id}" ${isChecked ? 'checked' : ''} ${disabledAttr}>
                        <span>${dv.ten_dich_vu}${vipBadge} <strong style="color:red; margin-left: 5px;">(+${new Intl.NumberFormat('vi-VN').format(dv.don_gia)}đ)</strong></span>
                    </label>
                `;
            });
            container.innerHTML = html || '<i>Chưa có danh mục dịch vụ nào trên hệ thống.</i>';
        }

        // Gửi danh sách dịch vụ đã đánh dấu lên Server
        async function saveRoomServices() {
            const phong_id = document.getElementById('phongSelect').value;
            if (!phong_id) return alert("Vui lòng chọn phòng trước khi gán dịch vụ!");

            // Lấy cả checkbox thường lẫn checkbox disabled (điều hoà VIP bắt buộc)
            const checkboxes = document.querySelectorAll('.room-service-cb:checked, .room-service-cb[disabled]');
            const selectedIds = [...new Set(Array.from(checkboxes).map(cb => cb.value))];

            const res = await fetch(`${apiDV}?action=save_room_services`, {
                method: 'POST',
                body: JSON.stringify({ phong_id: phong_id, dich_vu_ids: selectedIds })
            });
            const result = await res.json();
            alert(result.message);
        }

        // Khởi chạy khi load trang
        loadDV();
        loadRoomsForSelect();
    </script>
</body>
</html>