<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Điện Nước - MVC</title>
    <link rel="stylesheet" href="public/css/style.css">
    
    <style>
        .form-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 20px; }
        .form-group label { font-size: 14px; font-weight: 600; display: block; margin-bottom: 5px; }
        .form-group input, .form-group select { width: 100%; padding: 8px 10px; border-radius: 6px; border: 1px solid #ddd; }
        .btn-save { background: #1565C0; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600; }
        .btn-save:hover { background: #0D47A1; }
        .btn-excel { background: #28a745; color: white; padding: 8px 15px; border-radius: 6px; font-size: 13px; margin-left: 10px; text-decoration: none; }
        
        .paid { color: green; font-weight: bold; background: #e8f5e9; padding: 4px 8px; border-radius: 4px; }
        .unpaid { color: red; font-weight: bold; background: #ffebee; padding: 4px 8px; border-radius: 4px; }
        
        .action a { margin-right: 15px; font-weight: 600; text-decoration: none; }
        .edit { color: #f39c12; }
        .delete { color: #e74c3c; }
        .inline-label { display: inline-block; width: auto; margin-bottom: 5px; }
    </style>
</head>
<body>

    <?php include 'app/views/layout/sidebar.php'; ?>

    <main class="main-content">
        <?php include 'app/views/layout/header.php'; ?>

        <div class="page-content">
            
            <div class="panel">
                <div class="panel-header">
                    <h3>Thông tin hóa đơn</h3>
                </div>
                <div style="padding: 20px;">
                    <form method="post" action="index.php?controller=hoadon&action=save">
                        <input type="hidden" name="id" value="<?= $editData['id'] ?? '' ?>">
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Mã Hóa đơn</label>
                                <input name="mahd" value="<?= $editData['ma_hd'] ?? '' ?>" required placeholder="HD...">
                            </div>
                            <div class="form-group">
                                <label>Phòng</label>
                                <input name="phong" value="<?= $editData['phong'] ?? '' ?>" required placeholder="P...">
                           </div>
                            <div class="form-group">
                                <label>Tháng</label>
                                <input type="month" name="thang" value="<?= $editData['thang'] ?? '' ?>" required>
                            </div>
                            
                            <div class="form-group">
                                 <label>Chỉ số điện cũ</label>
                                 <input type="number" name="dien_cu" value="<?= $dien_cu_edit ?? 0 ?>" readonly>
                            </div>

                           <div class="form-group">
                             <label>Chỉ số nước cũ</label>
                             <input type="number" name="nuoc_cu" value="<?= $nuoc_cu_edit ?? 0 ?>" readonly>
                           </div>

                            <div class="form-group">
                                <label>Ngày lập</label>
                                <input type="date" name="ngaylap" value="<?= $editData['ngay_lap'] ?? date('Y-m-d') ?>">
                            </div>
                           
                           <div class="form-group">
                              <label>Chỉ số điện mới</label>
                              <input type="number" name="dien_moi" min="0" value="<?= $editData['dien_moi'] ?? '' ?>" required>
                           </div>

                            <div class="form-group">
                              <label>Chỉ số nước mới</label>
                              <input type="number" name="nuoc_moi" min="0" value="<?= $editData['nuoc_moi'] ?? '' ?>" required>
                           </div>

                            <div class="form-group">
                                <label>Trạng thái thu tiền</label>
                                <select name="trangthai">
                                    <option value="0" <?= ($editData['trang_thai'] ?? 0) == 0 ? 'selected' : '' ?>>Chưa thu</option>
                                    <option value="1" <?= ($editData['trang_thai'] ?? 0) == 1 ? 'selected' : '' ?>>Đã thu</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Tiền điện</label>
                                <input name="tiendien" value="<?= $editData['tong_tien_dien'] ?? 0 ?>" readonly style="background:#eee">
                            </div>
                            
                            <div class="form-group">
                                <label>Tiền nước</label>
                                <input name="tiennuoc" value="<?= $editData['tong_tien_nuoc'] ?? 0 ?>" readonly style="background:#eee">
                            </div>
                            <div class="form-group">
                                <label>TỔNG CỘNG</label>
                                <input name="tongtien" value="<?= $editData['tong_tien'] ?? 0 ?>" 
                                readonly style="background:#fff3cd; font-weight:bold; color:red">
                            </div>

                            <div class="form-group">
                                <label class="inline-label">Tiền phòng</label>
                                <input name="tien_phong"  value="<?= $editData['tien_phong'] ?? 0 ?>" readonly style="background:#eee">
                            </div>

                            <div class="form-group">
                                 <label class="inline-label" >Tiền dịch vụ</label>
                                 <input name="tien_dich_vu" value="<?= $editData['tien_dich_vu'] ?? 0 ?>" readonly style="background:#eee">
                            </div>
                        </div>

                        <button type="submit" name="save" class="btn-save">
                            <?= isset($editData) ? 'Cập nhật' : 'Lưu hóa đơn' ?>
                        </button>
                        <a href="index.php?controller=hoadon&action=index" style="margin-left:10px; color:#666; text-decoration:none;">Hủy / Làm mới</a>
                    </form>
                </div>
            </div>

            <br>

            <div class="panel">
                <div class="panel-header">
                    <h3>Danh sách hóa đơn</h3>
                    <div style="display:flex; align-items:center;">
                        
                        <form method="get" action="index.php" style="display:flex; gap:5px;">
                            <input type="hidden" name="controller" value="hoadon">
                            <input type="hidden" name="action" value="index">
                            <input type="text" name="keyword" placeholder="Tìm kiếm..." value="<?= htmlspecialchars($keyword ?? '') ?>" style="padding:5px; border:1px solid #ddd; border-radius: 4px;">
                            <button type="submit" style="padding:5px 10px; cursor:pointer; background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px;">Tìm</button>
                        </form>
                        
                        <a href="index.php?controller=hoadon&action=export&keyword=<?= htmlspecialchars($keyword ?? '') ?>" class="btn-excel">Xuất Excel</a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Mã HD</th>
                                <th>Phòng</th>
                                <th>Tháng</th>
                                <th>Điện / Nước</th>
                                <th>Tổng tiền</th>
                                <th>Ngày lập</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($listHoaDon)): ?>
                                <?php foreach ($listHoaDon as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['ma_hd']) ?></td>
                                    <td><strong><?= htmlspecialchars($row['phong']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['thang']) ?></td>
                                    <td>Đ: <?= $row['so_dien'] ?> | N: <?= $row['so_nuoc'] ?></td>
                                    <td style="color:red; font-weight:bold"><?= number_format($row['tong_tien']) ?> đ</td>
                                    <td><?= date('d/m/Y', strtotime($row['ngay_lap'])) ?></td>
                                    <td>
                                        <?= $row['trang_thai'] == 1 ? "<span class='paid'>Đã thu</span>" : "<span class='unpaid'>Chưa thu</span>" ?>
                                    </td>
                                    <td class="action">
                                        <a class="edit" href="index.php?controller=hoadon&action=index&edit=<?= $row['id'] ?>">Sửa</a>
                                        <a class="delete" href="index.php?controller=hoadon&action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Xóa hóa đơn này?')">Xóa</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan='8' style='text-align:center'>Không có dữ liệu</td></tr>
                            <?php endif; ?>
                        </tbody>

                    </table>
                </div>
            </div>

        </div>
    </main>

<script>
const giaDien = 3500;
const giaNuoc = 15000;

const phongInput = document.querySelector('[name="phong"]');
const thangInput = document.querySelector('[name="thang"]');

const dienCu  = document.querySelector('[name="dien_cu"]');
const dienMoi = document.querySelector('[name="dien_moi"]');
const nuocCu  = document.querySelector('[name="nuoc_cu"]');
const nuocMoi = document.querySelector('[name="nuoc_moi"]');

const tienDien = document.querySelector('[name="tiendien"]');
const tienNuoc = document.querySelector('[name="tiennuoc"]');
const tongTien = document.querySelector('[name="tongtien"]');

const tienPhongInput = document.querySelector('[name="tien_phong"]');
const tienDVInput = document.querySelector('[name="tien_dich_vu"]');

function tinhTien() {
    const dCu = parseInt(dienCu.value) || 0;
    const dMoi = parseInt(dienMoi.value) || 0;
    const nCu = parseInt(nuocCu.value) || 0;
    const nMoi = parseInt(nuocMoi.value) || 0;

    if (dMoi < dCu || nMoi < nCu) return;

    const soDien = dMoi - dCu;
    const soNuoc = nMoi - nCu;

    const td = soDien * giaDien;
    const tn = soNuoc * giaNuoc;

    const tp  = parseInt(tienPhongInput.value) || 0;
    const tdv = parseInt(tienDVInput.value) || 0;

    tienDien.value = td;
    tienNuoc.value = tn;
    tongTien.value = td + tn + tp + tdv;
}

function layChiSoCu() {
    const phong = phongInput.value.trim();
    const thang = thangInput.value;

    if (!phong || !thang) return;

    // Fetch gọi API. Đường dẫn bắt đầu từ "api/..." vì thực tế code đang chạy trên file Router ngoài cùng
    fetch(`api/dien_nuoc_api.php?action=get_chi_so_cu&phong=${phong}&thang=${thang}`)
        .then(res => res.json())
        .then(data => {
            dienCu.value = data.dien_cu || 0;
            nuocCu.value = data.nuoc_cu || 0;
            tinhTien(); 
        })
        .catch(() => {
            dienCu.value = 0;
            nuocCu.value = 0;
            tinhTien();
        });
}

function layTienPhongVaDV() {
    const phong = phongInput.value.trim();
    if (!phong) return;

    fetch(`api/dien_nuoc_api.php?action=get_tien_phong_dv&phong=${phong}`)
        .then(res => res.json())
        .then(data => {
            tienPhongInput.value = data.tien_phong || 0;
            tienDVInput.value = data.tien_dich_vu || 0;
            tinhTien(); // cộng lại tổng
        });
}

phongInput.addEventListener('change', layChiSoCu);
thangInput.addEventListener('change', layChiSoCu);
phongInput.addEventListener('change', layTienPhongVaDV);

dienMoi.addEventListener('input', tinhTien);
nuocMoi.addEventListener('input', tinhTien);

document.addEventListener('DOMContentLoaded', () => {
    // Chỉ tự động fetch nếu không phải đang ở trạng thái Edit (chưa có giá trị điện cũ)
    if (!dienCu.value || dienCu.value === "0") {
        layChiSoCu();
    }
});

</script>

</body>
</html>