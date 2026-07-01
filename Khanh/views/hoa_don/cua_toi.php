
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa đơn phòng tôi - UTT KTX</title>
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        #btn-close-modal:hover {
            background: #d32f2f !important;
            transform: scale(1.1);
        }
        #btn-close-modal:active {
            transform: scale(0.95);
        }
    </style>
</head>
<body>

    <?php include 'app/views/layout/sidebar.php'; ?>

    <div class="main-content">
        <?php include 'app/views/layout/header.php'; ?>

        <div class="page-content">
            <div class="content-header" style="margin-bottom:20px;">
                <h1 style="font-size:22px; color:#333;">Hóa đơn phòng tôi</h1>
            </div>

            <div class="panel" style="padding:20px;">
                <?php if (empty($listHoaDon)): ?>
                    <p style="text-align:center; color:#888; padding:20px;">
                        Bạn chưa được xếp phòng hoặc chưa có hóa đơn nào!
                    </p>
                <?php else: ?>
                    <div style="overflow-x:auto;">
                        <table style="width:100%; border-collapse:collapse;">
                            <thead>
                                <tr style="background:#f8f9fa; text-align:left;">
                                    <th style="padding:12px; border-bottom:2px solid #ddd;">Kỳ hóa đơn</th>
                                    <th style="padding:12px; border-bottom:2px solid #ddd;">Chỉ số Điện</th>
                                    <th style="padding:12px; border-bottom:2px solid #ddd;">Chỉ số Nước</th>
                                    <th style="padding:12px; border-bottom:2px solid #ddd;">Tiền điện</th>
                                    <th style="padding:12px; border-bottom:2px solid #ddd;">Tiền nước</th>
                                    <th style="padding:12px; border-bottom:2px solid #ddd;">Tiền phòng</th>
                                    <th style="padding:12px; border-bottom:2px solid #ddd;">Tiền dịch vụ</th>
                                    <th style="padding:12px; border-bottom:2px solid #ddd;">Tổng tiền</th>
                                    <th style="padding:12px; border-bottom:2px solid #ddd;">Trạng thái</th>
                                    <th style="padding:12px; border-bottom:2px solid #ddd;">Hành động</th>
</tr>
                            </thead>
                            <tbody>
                                <?php foreach ($listHoaDon as $hd): ?>
                                    <tr style="border-bottom:1px solid #eee;">
                                        <td style="padding:12px; font-weight:bold;"><?php echo $hd['thang_nam']; ?></td>
                                        <td style="padding:12px;">Sử dụng: <?php echo $hd['so_dien']; ?> số</td>
<td style="padding:12px;">Sử dụng: <?php echo $hd['so_nuoc']; ?> khối</td>
                                        <td style="padding:12px;"> <?php echo $hd['tien_dien']; ?> đ</td>
                                        <td style="padding:12px;"><?php echo $hd['tien_nuoc']; ?> đ</td>
                                        <td style="padding:12px;"> <?php echo $hd['tien_phong']; ?> đ</td>
                                        <td style="padding:12px;"><?php echo $hd['tien_dich_vu']; ?> đ</td>
                                        <td style="padding:12px; color:#c62828; font-weight:bold;">
                                            <?php echo number_format($hd['tong_tien']); ?> đ
                                        </td>
                                        <td style="padding:12px;">
                                            <?php if ($hd['trang_thai'] == 1): ?>
                                                <span style="background:#e8f5e9; color:#2e7d32; padding:4px 8px; border-radius:4px; font-size:12px; font-weight:bold;white-space:nowrap;">Đã thu</span>
                                            <?php else: ?>
                                                <span style="background:#ffebee; color:#c62828; padding:4px 8px; border-radius:4px; font-size:12px; font-weight:bold;white-space:nowrap;">Chưa thu</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding:12px;">
                                            <button class="btn-detail" 
                                                    data-id="<?php echo $hd['id']; ?>"
                                                    data-ten-phong="<?php echo $ten_phong; ?>"
                                                    data-dien-cu="<?php echo $hd['dien_cu']; ?>"
                                                    data-dien-moi="<?php echo $hd['dien_moi']; ?>"
                                                    data-nuoc-cu="<?php echo $hd['nuoc_cu']; ?>"
                                                    data-nuoc-moi="<?php echo $hd['nuoc_moi']; ?>"
                                                    data-tien-dien="<?php echo $hd['tien_dien']; ?>"
                                                    data-tien-nuoc="<?php echo $hd['tien_nuoc']; ?>"
                                                    data-tien-phong="<?php echo $hd['tien_phong']; ?>"
data-tien-dich-vu="<?php echo $hd['tien_dich_vu']; ?>"
                                                    data-tong-tien="<?php echo $hd['tong_tien']; ?>"
                                                    data-dich-vu-json="<?php echo htmlspecialchars((isset($hd['danh_sach_dich_vu']) && $hd['danh_sach_dich_vu']) ? $hd['danh_sach_dich_vu'] : json_encode($defaultDichVuList, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>"
style="color:#1565C0; text-decoration:none; font-weight:bold; background:none; border:none; cursor:pointer;">Xem chi tiết</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal Chi Tiết Hóa Đơn -->
    <div id="modal-detail" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:1000; backdrop-filter: blur(2px);">
        <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%) scale(0.9); background:white; padding:30px; border-radius:12px; width:650px; max-height:85%; overflow-y:auto; box-shadow: 0 10px 30px rgba(0,0,0,0.3); border: 1px solid #e0e0e0; opacity: 0; transition: transform 0.3s ease, opacity 0.3s ease;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; border-bottom: 2px solid #f0f0f0; padding-bottom:15px;">
                <h3 style="margin:0; color:#1565C0; font-size:24px; font-weight:600;">Chi Tiết Hóa Đơn</h3>
                <button id="btn-close-modal" style="background:#f44336; color:white; border:none; border-radius:50%; width:35px; height:35px; cursor:pointer; font-size:18px; font-weight:bold; transition: background 0.3s, transform 0.2s;">×</button>
            </div>
            <div id="modal-content" style="line-height:1.6;">
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:25px;">
                    <div style="background:#f8f9fa; padding:15px; border-radius:8px; border-left:4px solid #1565C0;">
                        <h4 style="margin:0 0 10px 0; color:#333; font-size:16px;">Thông Tin Phòng</h4>
                        <p style="margin:5px 0;"><strong>Tên phòng:</strong> <span id="modal-ten-phong" style="color:#1565C0; font-weight:500;"></span></p>
                    </div>
                    <div style="background:#fff3e0; padding:15px; border-radius:8px; border-left:4px solid #ff9800;">
                        <h4 style="margin:0 0 10px 0; color:#333; font-size:16px;">Chỉ Số Điện</h4>
                        <p style="margin:5px 0;"><strong>Cũ:</strong> <span id="modal-dien-cu"></span> số</p>
<p style="margin:5px 0;"><strong>Mới:</strong> <span id="modal-dien-moi"></span> số</p>
                    </div>
                    <div style="background:#e8f5e9; padding:15px; border-radius:8px; border-left:4px solid #4caf50;">
                        <h4 style="margin:0 0 10px 0; color:#333; font-size:16px;">Chỉ Số Nước</h4>
                        <p style="margin:5px 0;"><strong>Cũ:</strong> <span id="modal-nuoc-cu"></span> khối</p>
<p style="margin:5px 0;"><strong>Mới:</strong> <span id="modal-nuoc-moi"></span> khối</p>
                    </div>
                    <div style="background:#fce4ec; padding:15px; border-radius:8px; border-left:4px solid #e91e63;">
                        <h4 style="margin:0 0 10px 0; color:#333; font-size:16px;">Dịch Vụ Sử Dụng</h4>
                        <ul id="modal-dich-vu-list" style="margin:0; padding-left:20px; list-style-type: disc;"></ul>
                    </div>
                </div>
                <div style="background:#f5f5f5; padding:20px; border-radius:8px; border:1px solid #ddd;">
                    <h4 style="margin:0 0 15px 0; color:#333; font-size:18px; text-align:center;">Tóm Tắt Chi Phí</h4>
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                        <p style="margin:8px 0;"><strong>Tiền điện:</strong> <span id="modal-tien-dien" style="color:#ff5722; font-weight:600;"></span> đ</p>
                        <p style="margin:8px 0;"><strong>Tiền nước:</strong> <span id="modal-tien-nuoc" style="color:#2196f3; font-weight:600;"></span> đ</p>
                        <p style="margin:8px 0;"><strong>Tiền phòng:</strong> <span id="modal-tien-phong" style="color:#4caf50; font-weight:600;"></span> đ</p>
                        <p style="margin:8px 0;"><strong>Tiền dịch vụ:</strong> <span id="modal-tien-dich-vu" style="color:#9c27b0; font-weight:600;"></span> đ</p>
                    </div>
                    <hr style="border:none; border-top:2px solid #ddd; margin:15px 0;">
                    <p style="margin:10px 0; text-align:center; font-size:18px;"><strong>Tổng tiền:</strong> <span id="modal-tong-tien" style="color:#c62828; font-weight:700; font-size:20px;"></span> đ</p>
                </div>
            </div>
        </div>
    </div>

    <script src="public/js/cua_toi.js"></script>

</body>
</html>