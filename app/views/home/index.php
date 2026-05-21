<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang chủ - UTT KTX</title>
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        .dashboard-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: #fff; padding: 25px 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); display: flex; flex-direction: column; justify-content: center; border-left: 6px solid #ccc; transition: 0.3s;}
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
        .stat-card.blue { border-left-color: #1565C0; }
        .stat-card.green { border-left-color: #2e7d32; }
        .stat-card.orange { border-left-color: #f39c12; }
        .stat-card.red { border-left-color: #e74c3c; }
        .stat-card h3 { font-size: 30px; margin: 0 0 8px 0; color: #333; font-weight: 700;}
        .stat-card p { margin: 0; color: #666; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;}
        
        .dashboard-bottom { display: grid; grid-template-columns: 2fr 1fr; gap: 25px; }
        .panel { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .panel-title { font-size: 16px; font-weight: 700; margin-bottom: 20px; color: #333; display: flex; justify-content: space-between; align-items: center; }
        
        .table-no { width: 100%; border-collapse: collapse; }
        .table-no th, .table-no td { padding: 15px 10px; text-align: left; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
        .table-no th { color: #888; font-weight: 600; text-transform: uppercase; font-size: 12px;}
        
        .quick-access { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .qa-btn { background: #f8f9fa; border: 2px dashed #ddd; padding: 20px 10px; text-align: center; border-radius: 8px; color: #555; text-decoration: none; font-weight: 600; transition: 0.2s; font-size: 14px;}
        .qa-btn:hover { background: #e3f2fd; border-color: #1565C0; color: #1565C0; }
    </style>
</head>
<body>

    <?php include 'app/views/layout/sidebar.php'; ?>

    <main class="main-content">
        <?php include 'app/views/layout/header.php'; ?>

        <div class="page-content">
            <h2 style="margin-bottom: 25px; color: #333; font-weight: 600;">Tổng quan hệ thống</h2>

            <div class="dashboard-cards">
                <div class="stat-card blue">
                    <h3 id="txt_tong_phong">0</h3>
                    <p>Tổng số phòng</p>
                </div>
                <div class="stat-card green">
                    <h3 id="txt_sinh_vien">0</h3>
                    <p>Sinh viên đang ở</p>
                </div>
                <div class="stat-card orange">
                    <h3 id="txt_su_co">0</h3>
                    <p>Sự cố chờ xử lý</p>
                </div>
                <div class="stat-card red">
                    <h3 id="txt_doanh_thu" style="color: #e74c3c;">0 đ</h3>
                    <p>Doanh thu tháng này</p>
                </div>
            </div>

            <div class="dashboard-bottom">
                <div class="panel">
                    <div class="panel-title">
                        <span>Hóa đơn chưa thanh toán gần đây</span>
                        <a href="index.php?controller=hoadon" style="font-size: 13px; color: #1565C0; text-decoration: none; font-weight: 600;">Xem tất cả &rarr;</a>
                    </div>
                    <table class="table-no">
                        <thead>
                            <tr style="background: #fafafa;">
                                <th>Phòng</th>
                                <th>Tháng</th>
                                <th>Số tiền</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody id="hoa_don_no_body">
                            <tr><td colspan="4" style="text-align:center; padding: 20px;">Đang tải dữ liệu...</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="panel">
                    <div class="panel-title">Truy cập nhanh</div>
                    <div class="quick-access">
                        <a href="index.php?controller=hoadon" class="qa-btn">+ Lập hóa đơn</a>
                        <a href="index.php?controller=sinhvien" class="qa-btn">+ Xếp phòng SV</a>
                        <a href="index.php?controller=hopdong" class="qa-btn" style="grid-column: span 2;">+ Lập hợp đồng mới</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        async function loadDashboardStats() {
            try {
                const res = await fetch('api/home_api.php?action=get_stats');
                const result = await res.json();
                
                if(result.success) {
                    const data = result.data;
                    
                    // Cập nhật 4 con số thẻ
                    document.getElementById('txt_tong_phong').innerText = new Intl.NumberFormat('vi-VN').format(data.tong_phong);
                    document.getElementById('txt_sinh_vien').innerText = new Intl.NumberFormat('vi-VN').format(data.sinh_vien);
                    document.getElementById('txt_su_co').innerText = new Intl.NumberFormat('vi-VN').format(data.su_co);
                    document.getElementById('txt_doanh_thu').innerText = new Intl.NumberFormat('vi-VN').format(data.doanh_thu) + ' đ';

                    // Đổ dữ liệu vào bảng hóa đơn nợ
                    let hdHtml = '';
                    if(data.hoa_don_no && data.hoa_don_no.length > 0) {
                        data.hoa_don_no.forEach(hd => {
                            hdHtml += `
                                <tr>
                                    <td><strong>${hd.ma_phong || 'Chưa rõ'}</strong></td>
                                    <td>${hd.thang_nam}</td>
                                    <td style="color: #c62828; font-weight: bold;">${new Intl.NumberFormat('vi-VN').format(hd.tong_tien)} đ</td>
                                    <td><span style="background: #ffebee; color: #c62828; padding: 5px 10px; border-radius: 6px; font-size: 12px; font-weight: bold;">Chưa thu</span></td>
                                </tr>
                            `;
                        });
                    } else {
                        hdHtml = `<tr><td colspan="4" style="text-align:center; padding: 30px; color: #888;">Tất cả hóa đơn đã được thanh toán! 🎉</td></tr>`;
                    }
                    document.getElementById('hoa_don_no_body').innerHTML = hdHtml;
                }
            } catch(e) {
                console.error("Lỗi load trang chủ: ", e);
                document.getElementById('hoa_don_no_body').innerHTML = `<tr><td colspan="4" style="text-align:center; color: red;">Lỗi kết nối máy chủ!</td></tr>`;
            }
        }

        // Tự động load dữ liệu khi vào trang
        document.addEventListener('DOMContentLoaded', loadDashboardStats);
    </script>
</body>
</html>