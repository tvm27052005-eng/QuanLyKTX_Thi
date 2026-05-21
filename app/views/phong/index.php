<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản lý Phòng - KTX UTT</title>
<link rel="stylesheet" href="public/css/style.css">
<style>
  /* ===== TOOLBAR ===== */
  .toolbar {
    display: flex; align-items: center; gap: 10px;
    padding: 18px 24px; background: white;
    border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    margin-bottom: 20px; flex-wrap: wrap;
  }
  .search-box {
    display: flex; align-items: center;
    border: 1px solid #ddd; border-radius: 7px;
    overflow: hidden; flex: 1; min-width: 200px; max-width: 340px;
  }
  .search-box input {
    border: none; outline: none; padding: 9px 14px;
    font-size: 14px; width: 100%;
  }
  .search-box button {
    background: #1565C0; color: white; border: none;
    padding: 9px 14px; cursor: pointer; font-size: 14px;
  }
  .btn { padding: 9px 18px; border-radius: 7px; border: none;
    font-size: 14px; font-weight: 600; cursor: pointer;
    display: flex; align-items: center; gap: 6px; transition: 0.2s;
  }
  .btn-add    { background: #1565C0; color: white; }
  .btn-add:hover { background: #0D47A1; }
  .btn-excel  { background: #2e7d32; color: white; }
  .btn-excel:hover { background: #1b5e20; }
  .btn-reset  { background: #f5f5f5; color: #555; border: 1px solid #ddd; }

  /* ===== PANEL / TABLE ===== */
  .panel { background: white; border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06); overflow: hidden; }
  .panel-header {
    padding: 15px 22px; border-bottom: 1px solid #eee;
    display: flex; justify-content: space-between; align-items: center;
  }
  .panel-header h3 { font-size: 16px; font-weight: 600; color: #333; }
  .count-badge {
    background: #e3f2fd; color: #1565C0;
    font-size: 13px; font-weight: 600;
    padding: 3px 10px; border-radius: 20px;
  }
  table { width: 100%; border-collapse: collapse; }
  th, td { padding: 12px 18px; text-align: left;
    border-bottom: 1px solid #f0f0f0; font-size: 14px; }
  th { background: #f8f9fa; color: #555; font-weight: 600; }
  tr:hover td { background: #fafcff; }

  .badge {
    padding: 4px 10px; border-radius: 20px;
    font-size: 12px; font-weight: 600; display: inline-block;
  }
  .badge-nam    { background: #e3f2fd; color: #1565C0; }
  .badge-nu     { background: #fce4ec; color: #c2185b; }
  .badge-nam-vip { background: #ede7f6; color: #4527a0; }
  .badge-nu-vip  { background: #fce4ec; color: #880e4f; }
  .badge-active { background: #e8f5e9; color: #2e7d32; }
  .badge-repair { background: #fff3e0; color: #e65100; }

  .capacity-bar {
    display: flex; align-items: center; gap: 8px;
  }
  .bar-wrap {
    width: 80px; height: 7px; background: #eee;
    border-radius: 4px; overflow: hidden;
  }
  .bar-fill { height: 100%; border-radius: 4px; background: #1565C0; }

  .action-btns { display: flex; gap: 8px; }
  .btn-edit   { background: #fff8e1; color: #f57c00;
    border: 1px solid #ffe082; padding: 5px 12px;
    border-radius: 5px; font-size: 13px; font-weight: 600; cursor: pointer; }
  .btn-edit:hover { background: #ffe082; }
  .btn-del    { background: #ffebee; color: #c62828;
    border: 1px solid #ffcdd2; padding: 5px 12px;
    border-radius: 5px; font-size: 13px; font-weight: 600; cursor: pointer; }
  .btn-del:hover { background: #ffcdd2; }

  /* ===== MODAL ===== */
  .modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.45); z-index: 999;
    justify-content: center; align-items: center;
  }
  .modal-overlay.show { display: flex; }
  .modal-box {
    background: white; border-radius: 12px;
    width: 520px; max-width: 95vw;
    box-shadow: 0 8px 32px rgba(0,0,0,0.18);
    animation: fadeIn .2s ease;
  }
  @keyframes fadeIn { from{transform:scale(.95);opacity:0} to{transform:scale(1);opacity:1} }
  .modal-header {
    padding: 18px 24px; border-bottom: 1px solid #eee;
    display: flex; justify-content: space-between; align-items: center;
  }
  .modal-header h3 { font-size: 17px; font-weight: 700; color: #1565C0; }
  .modal-close { background: none; border: none; font-size: 22px;
    cursor: pointer; color: #999; line-height: 1; }
  .modal-body { padding: 24px; }
  .form-grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
  .form-group label {
    display: block; font-size: 13px; font-weight: 600;
    color: #555; margin-bottom: 6px;
  }
  .form-group input, .form-group select {
    width: 100%; padding: 9px 12px; border: 1px solid #ddd;
    border-radius: 7px; font-size: 14px; outline: none; transition: 0.2s;
  }
  .form-group input:focus, .form-group select:focus { border-color: #1565C0; }
  .modal-footer {
    padding: 16px 24px; border-top: 1px solid #eee;
    display: flex; justify-content: flex-end; gap: 10px;
  }
  .btn-cancel { background: #f5f5f5; color: #555;
    border: 1px solid #ddd; padding: 9px 20px;
    border-radius: 7px; cursor: pointer; font-weight: 600; }
  .btn-submit { background: #1565C0; color: white;
    border: none; padding: 9px 24px;
    border-radius: 7px; cursor: pointer; font-weight: 700; font-size: 15px; }
  .btn-submit:hover { background: #0D47A1; }

  /* ===== TOAST ===== */
  #toast {
    position: fixed; bottom: 30px; right: 30px; z-index: 9999;
    padding: 13px 22px; border-radius: 8px; font-size: 14px;
    font-weight: 600; color: white; display: none;
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
  }
  #toast.success { background: #2e7d32; }
  #toast.error   { background: #c62828; }
</style>
</head>
<body>
<?php include 'app/views/layout/sidebar.php'; ?>
<main class="main-content">
  <?php include 'app/views/layout/header.php'; ?>
  <div class="page-content">

    <h2 style="margin-bottom:18px;color:#1565C0;font-size:22px;"> Quản lý Phòng</h2>

    <!-- TOOLBAR -->
    <div class="toolbar">
      <div class="search-box">
        <input type="text" id="searchInput" placeholder="Tìm mã phòng, loại, trạng thái..." />
        <button onclick="loadData()">Tìm kiếm </button>
      </div>
      <button class="btn btn-add" onclick="openModal()"> Thêm phòng</button>
      <button class="btn btn-excel" onclick="exportExcel()"> Xuất Excel</button>
      <button class="btn btn-reset" onclick="resetSearch()">↺ Làm mới</button>
    </div>

    <!-- BẢNG DỮ LIỆU -->
    <div class="panel">
      <div class="panel-header">
        <h3>Danh sách phòng</h3>
        <span class="count-badge" id="countBadge">0 phòng</span>
      </div>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Mã phòng</th>
              <th>Loại phòng</th>
              <th>Giá thuê</th>
              <th>Sĩ số</th>
              <th>Trạng thái</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody id="tableBody">
            <tr><td colspan="7" style="text-align:center;color:#aaa;padding:30px">Đang tải...</td></tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</main>

<!-- MODAL THÊM/SỬA -->
<div class="modal-overlay" id="modalOverlay">
  <div class="modal-box">
    <div class="modal-header">
      <h3 id="modalTitle">Thêm phòng mới</h3>
      <button class="modal-close" onclick="closeModal()">×</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="fId">
      <div class="form-grid2">
        <div class="form-group">
          <label>Mã phòng <span style="color:red">*</span></label>
          <input id="fMaPhong" placeholder="VD: P101" required>
        </div>
        <div class="form-group">
          <label>Loại phòng <span style="color:red">*</span></label>
          <select id="fLoaiPhong" onchange="onLoaiPhongChange()">
            <option value="Nam">Nam</option>
            <option value="Nữ">Nữ</option>
            <option value="Nam VIP">Nam VIP</option>
            <option value="Nữ VIP">Nữ VIP</option>
          </select>
        </div>
        <div class="form-group">
          <label>Giá thuê (đ/tháng)</label>
          <input type="number" id="fGiaThue" value="500000" min="0">
        </div>
        <div class="form-group">
          <label>Số người tối đa</label>
          <input type="number" id="fSoNguoi" value="8" min="1">
        </div>
        <div class="form-group" style="grid-column:span 2">
          <label>Trạng thái</label>
          <select id="fTrangThai">
            <option value="hoat_dong">Hoạt động</option>
            <option value="bao_tri">Bảo trì</option>
          </select>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-cancel" onclick="closeModal()">Hủy</button>
      <button class="btn-submit" onclick="savePhong()">Lưu</button>
    </div>
  </div>
</div>

<!-- TOAST THÔNG BÁO -->
<div id="toast"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
const API = 'api/phong_api.php';
let currentKeyword = '';

// ======= LOAD DỮ LIỆU =======
async function loadData() {
  currentKeyword = document.getElementById('searchInput').value.trim();
  const res  = await fetch(`${API}?action=get_all&keyword=${encodeURIComponent(currentKeyword)}`);
  const data = await res.json();
  renderTable(data);
}

function renderTable(data) {
  const tbody = document.getElementById('tableBody');
  document.getElementById('countBadge').textContent = data.length + ' phòng';
  if (!data.length) {
    tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;color:#aaa;padding:30px">Không có dữ liệu</td></tr>`;
    return;
  }
  tbody.innerHTML = data.map((r, i) => {
    const pct = r.so_nguoi_toi_da > 0
      ? Math.round(r.so_nguoi_hien_tai / r.so_nguoi_toi_da * 100) : 0;
    const loaiBadge = (() => {
      switch(r.loai_phong) {
        case 'Nam':     return `<span class="badge badge-nam">Nam</span>`;
        case 'Nữ':      return `<span class="badge badge-nu">Nữ</span>`;
        case 'Nam VIP': return `<span class="badge badge-nam-vip">Nam VIP</span>`;
        case 'Nữ VIP':  return `<span class="badge badge-nu-vip">Nữ VIP</span>`;
        default:        return `<span class="badge badge-nam">${r.loai_phong}</span>`;
      }
    })();
    const ttBadge = r.trang_thai === 'hoat_dong'
      ? `<span class="badge badge-active">Hoạt động</span>`
      : `<span class="badge badge-repair">Bảo trì</span>`;
    return `<tr>
      <td style="color:#aaa">${i+1}</td>
      <td><strong>${r.ma_phong}</strong></td>
      <td>${loaiBadge}</td>
      <td style="color:#1565C0;font-weight:600">${parseInt(r.gia_thue).toLocaleString('vi-VN')} đ</td>
      <td>
        <div class="capacity-bar">
          <span>${r.so_nguoi_hien_tai}/${r.so_nguoi_toi_da}</span>
          <div class="bar-wrap"><div class="bar-fill" style="width:${pct}%"></div></div>
        </div>
      </td>
      <td>${ttBadge}</td>
      <td>
        <div class="action-btns">
          <button class="btn-edit" onclick="editPhong(${r.id})"> Sửa</button>
          <button class="btn-del"  onclick="deletePhong(${r.id},'${r.ma_phong}')"> Xóa</button>
        </div>
      </td>
    </tr>`;
  }).join('');
}

// ======= RESET TÌM KIẾM =======
function resetSearch() {
  document.getElementById('searchInput').value = '';
  loadData();
}

// ======= MỞ MODAL THÊM =======
function openModal() {
  document.getElementById('modalTitle').textContent = ' Thêm phòng mới';
  document.getElementById('fId').value = '';
  document.getElementById('fMaPhong').value = '';
  document.getElementById('fLoaiPhong').value = 'Nam';
  document.getElementById('fGiaThue').value = '500000';
  document.getElementById('fSoNguoi').value = '8';
  document.getElementById('fSoNguoi').max = '';
  document.getElementById('fTrangThai').value = 'hoat_dong';
  document.getElementById('modalOverlay').classList.add('show');
}

// ======= MỞ MODAL SỬA =======
async function editPhong(id) {
  const res  = await fetch(`${API}?action=get_one&id=${id}`);
  const data = await res.json();
  document.getElementById('modalTitle').textContent = ' Cập nhật phòng';
  document.getElementById('fId').value = data.id;
  document.getElementById('fMaPhong').value = data.ma_phong;
  document.getElementById('fLoaiPhong').value = data.loai_phong;
  document.getElementById('fGiaThue').value = data.gia_thue;
  document.getElementById('fSoNguoi').value = data.so_nguoi_toi_da;
  document.getElementById('fSoNguoi').max = (data.loai_phong === 'Nam VIP' || data.loai_phong === 'Nữ VIP') ? 4 : '';
  document.getElementById('fTrangThai').value = data.trang_thai;
  document.getElementById('modalOverlay').classList.add('show');
}

function closeModal() {
  document.getElementById('modalOverlay').classList.remove('show');
}

// ======= LƯU (THÊM / SỬA) =======
async function savePhong() {
  const id   = document.getElementById('fId').value;

  const giaThue = document.getElementById('fGiaThue').value.trim();
  const soNguoi = document.getElementById('fSoNguoi').value.trim();

  const body = {
    id,
    ma_phong: document.getElementById('fMaPhong').value.trim(),
    loai_phong: document.getElementById('fLoaiPhong').value,
    gia_thue: giaThue,
    so_nguoi_toi_da: soNguoi,
    trang_thai: document.getElementById('fTrangThai').value,
  };

  // ===== VALIDATION =====
  if (!body.ma_phong) {
    showToast('Vui lòng nhập mã phòng!', 'error');
    return;
  }

  // Giá thuê phải >= 0
  if (isNaN(giaThue) || Number(giaThue) < 0) {
    showToast('Giá thuê phải lớn hơn hoặc bằng 0!', 'error');
    return;
  }

  // Số người tối đa phải là số nguyên dương
  if (
    !Number.isInteger(Number(soNguoi)) ||
    Number(soNguoi) <= 0
  ) {
    showToast('Số người tối đa phải là số nguyên lớn hơn 0!', 'error');
    return;
  }

  // Phòng VIP tối đa 4 người
  const loai = document.getElementById('fLoaiPhong').value;
  if ((loai === 'Nam VIP' || loai === 'Nữ VIP') && Number(soNguoi) > 4) {
    showToast('Phòng VIP chỉ được tối đa 4 người!', 'error');
    return;
  }

  const action = id ? 'update' : 'insert';

  const res = await fetch(`${API}?action=${action}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(body)
  });

  const result = await res.json();

  if (result.success) {
    closeModal();
    showToast(result.message, 'success');
    loadData();
  } else {
    showToast(result.message, 'error');
  }
}

// ======= XÓA =======
async function deletePhong(id, ten) {
  if (!confirm(`Bạn có chắc muốn xóa phòng "${ten}" không?`)) return;
  const res    = await fetch(`${API}?action=delete&id=${id}`);
  const result = await res.json();
  showToast(result.message, result.success ? 'success' : 'error');
  if (result.success) loadData();
}

// ======= XUẤT EXCEL =======
async function exportExcel() {
  const res  = await fetch(`${API}?action=export&keyword=${encodeURIComponent(currentKeyword)}`);
  const data = await res.json();
  const rows = data.map((r, i) => ({
    'STT': i + 1,
    'Mã phòng':  r.ma_phong,
    'Loại phòng': r.loai_phong,
    'Giá thuê (đ)': parseInt(r.gia_thue),
    'Số người tối đa': r.so_nguoi_toi_da,
    'Số người hiện tại': r.so_nguoi_hien_tai,
    'Trạng thái': r.trang_thai === 'hoat_dong' ? 'Hoạt động' : 'Bảo trì',
  }));
  const ws = XLSX.utils.json_to_sheet(rows);
  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, 'Danh sách phòng');
  XLSX.writeFile(wb, `DanhSachPhong_${new Date().toLocaleDateString('vi-VN').replace(/\//g,'-')}.xlsx`);
  showToast('Xuất Excel thành công!', 'success');
}

// ======= TOAST =======
function showToast(msg, type = 'success') {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className = type;
  t.style.display = 'block';
  setTimeout(() => t.style.display = 'none', 3000);
}

// ======= XỬ LÝ ĐỔI LOẠI PHÒNG =======
function onLoaiPhongChange() {
  const loai = document.getElementById('fLoaiPhong').value;
  const soNguoiInput = document.getElementById('fSoNguoi');
  if (loai === 'Nam VIP' || loai === 'Nữ VIP') {
    soNguoiInput.max = 4;
    if (parseInt(soNguoiInput.value) > 4) soNguoiInput.value = 4;
  } else {
    soNguoiInput.max = '';
  }
}

// Enter để tìm kiếm
document.getElementById('searchInput').addEventListener('keydown', e => {
  if (e.key === 'Enter') loadData();
});

// Click ngoài modal để đóng
document.getElementById('modalOverlay').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});

// Load khi vào trang
loadData();
</script>
</body>
</html>