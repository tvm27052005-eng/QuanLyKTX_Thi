<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Thanh toán - UTT KTX</title>
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        .panel { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px;}
        table { width: 100%; border-collapse: collapse; }
        table th, table td { padding: 12px 15px; border-bottom: 1px solid #eee; text-align: left; }
        table th { background: #f8f9fa; color: #555; }
        .btn-sm { padding: 6px 12px; font-size: 12px; border: none; border-radius: 4px; cursor: pointer; color: white; margin-right: 5px; }
        .btn-success { background-color: #2e7d32; }
        .btn-success:hover { background-color: #27642a; }
        .btn-warning { background-color: #f39c12; }
        .btn-warning:hover { background-color: #e67e22; }
        .btn-danger { background-color: #e53935; }
        .btn-danger:hover { background-color: #c62828; }
        .status-pending { background: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
        .status-approved { background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
        .status-rejected { background: #f8d7da; color: #721c24; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
    </style>
</head>
<body>
    <?php include 'app/views/layout/sidebar.php'; ?>
    <div class="main-content">
        <?php include 'app/views/layout/header.php'; ?>
        <div class="page-content">
            <div class="panel">
                <h2 style="color: #1565C0; margin-top: 0; margin-bottom: 20px;">Quản lý Yêu cầu Thanh toán</h2>
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3 style="margin: 0; color: #333;">Danh sách yêu cầu</h3>
                    <input type="text" id="searchKeyword" placeholder="Tìm kiếm phòng hoặc mã sinh viên..." style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; width: 300px;">
                </div>
                
                <div>
                    <button style="padding: 10px 20px; background: #1565C0; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;" onclick="loadPaymentRequests()">Tìm kiếm</button>
                </div>

                <div style="overflow-x: auto; margin-top: 15px;">
                    <table>
                        <thead>
                            <tr>
                                <th>Phòng</th>
                                <th>Mã SV</th>
                                <th>Mã Hóa đơn</th>
                                <th>Kỳ</th>
                                <th>Số tiền</th>
                                <th>Trạng thái</th>
                                <th>Yêu cầu lúc</th>
                                <th style="text-align: center;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr><td colspan="8" style="text-align: center;">Đang tải dữ liệu...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        const API = 'api/thanh_toan_api.php';

        function formatCurrency(value) {
            return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
        }

        function getStatusBadge(status) {
            if (status === 'pending') return '<span class="status-pending">Chờ xử lý</span>';
            if (status === 'approved') return '<span class="status-approved">Đã duyệt</span>';
            if (status === 'rejected') return '<span class="status-rejected">Từ chối</span>';
            return status;
        }

        async function loadPaymentRequests() {
            const kw = document.getElementById('searchKeyword').value;
            try {
                const res = await fetch(`${API}?action=get_all&keyword=${encodeURIComponent(kw)}`);
                const result = await res.json();
                const tbody = document.getElementById('tableBody');

                if (!result.success || !result.data || result.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;">Không có yêu cầu nào.</td></tr>';
                    return;
                }

                let html = '';
                result.data.forEach(item => {
                    const createdAt = new Date(item.created_at).toLocaleString('vi-VN');
                    const actionBtns = item.trang_thai === 'pending' ? `
                        <button class="btn-sm btn-success" onclick="approvePayment(${item.id})">Xác nhận</button>
                        <button class="btn-sm btn-warning" onclick="rejectPayment(${item.id})">Từ chối</button>
                    ` : `
                        <button class="btn-sm btn-danger" onclick="deletePayment(${item.id})">Xóa</button>
                    `;

                    html += `
                        <tr>
                            <td><strong>${item.ma_phong || '---'}</strong></td>
                            <td>${item.ma_sinh_vien || '---'}</td>
                            <td>${item.ma_hd || '---'}</td>
                            <td>${item.thang_nam || '---'}</td>
                            <td style="color:#c62828; font-weight:bold;">${formatCurrency(item.so_tien)}</td>
                            <td>${getStatusBadge(item.trang_thai)}</td>
                            <td>${createdAt}</td>
                            <td style="text-align: center;">
                                ${actionBtns}
                            </td>
                        </tr>
                    `;
                });

                tbody.innerHTML = html;
            } catch (error) {
                console.error(error);
                document.getElementById('tableBody').innerHTML = '<tr><td colspan="8" style="text-align:center; color:red;">Lỗi tải dữ liệu!</td></tr>';
            }
        }

        async function approvePayment(id) {
            if (!confirm('Xác nhận đã nhận được tiền và thanh toán không?')) return;
            
            try {
                const res = await fetch(`${API}?action=approve`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });
                const result = await res.json();
                alert(result.message);
                if (result.success) loadPaymentRequests();
            } catch (error) {
                alert('Lỗi khi xác nhận.');
                console.error(error);
            }
        }

        async function rejectPayment(id) {
            if (!confirm('Từ chối yêu cầu này?')) return;
            
            try {
                const res = await fetch(`${API}?action=reject`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });
                const result = await res.json();
                alert(result.message);
                if (result.success) loadPaymentRequests();
            } catch (error) {
                alert('Lỗi khi từ chối.');
                console.error(error);
            }
        }

        async function deletePayment(id) {
            if (!confirm('Xóa yêu cầu này?')) return;
            
            try {
                const res = await fetch(`${API}?action=delete`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });
                const result = await res.json();
                alert(result.message);
                if (result.success) loadPaymentRequests();
            } catch (error) {
                alert('Lỗi khi xóa.');
                console.error(error);
            }
        }

        document.getElementById('searchKeyword').addEventListener('keypress', e => {
            if (e.key === 'Enter') loadPaymentRequests();
        });

        document.addEventListener('DOMContentLoaded', loadPaymentRequests);
    </script>
</body>
</html>
