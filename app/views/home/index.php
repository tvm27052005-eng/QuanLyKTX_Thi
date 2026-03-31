<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang chủ - Hệ thống quản lý KTX</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

    <?php include 'app/views/layout/sidebar.php'; ?>

    <main class="main-content">
        <?php include 'app/views/layout/header.php'; ?>

        <div class="page-content">
            <h2 style="margin-bottom: 20px; color: #333;">Tổng quan hệ thống</h2>
            
            <div class="stats-grid">
                <div class="stat-card card-1">
                    <div class="stat-info">
                        <h3>120</h3>
                        <p>Tổng số phòng</p>
                    </div>
                    </div>
                
                <div class="stat-card card-2">
                    <div class="stat-info">
                        <h3>850</h3>
                        <p>Sinh viên đang ở</p>
                    </div>
                </div>
                
                <div class="stat-card card-3">
                    <div class="stat-info">
                        <h3>15</h3>
                        <p>Sự cố chờ xử lý</p>
                    </div>
                </div>
                
                <div class="stat-card card-4">
                    <div class="stat-info">
                        <h3>12,5M</h3>
                        <p>Doanh thu tháng</p>
                    </div>
                </div>
            </div>

            <div class="recent-grid">
                <div class="panel">
                    <div class="panel-header">
                        <h3>Hóa đơn chưa thanh toán gần đây</h3>
                        <a href="index.php?controller=hoadon" class="btn-view-all">Xem tất cả</a>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Phòng</th>
                                    <th>Tháng</th>
                                    <th>Số tiền</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>P101</strong></td>
                                    <td>03/2026</td>
                                    <td style="color:red; font-weight:bold;">150,000 đ</td>
                                    <td><span class="status unpaid">Chưa thu</span></td>
                                </tr>
                                <tr>
                                    <td><strong>P102</strong></td>
                                    <td>03/2026</td>
                                    <td style="color:red; font-weight:bold;">85,000 đ</td>
                                    <td><span class="status unpaid">Chưa thu</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bottom-card">
                    <h3>Truy cập nhanh</h3>
                    <div class="quick-actions">
                        <a href="index.php?controller=hoadon" class="action-btn">
                            <span>+ Lập hóa đơn</span>
                        </a>
                        <a href="index.php?controller=sinhvien" class="action-btn">
                            <span>+ Thêm sinh viên</span>
                        </a>
                        <a href="index.php?controller=hopdong" class="action-btn">
                            <span>+ Hợp đồng mới</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </main>

</body>
</html>