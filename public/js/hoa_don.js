// public/js/hoa_don.js
// LOGIC TÍNH TIỀN ĐIỆN NƯỚC
const giaDien = 3500;
const giaNuoc = 15000;

const phongSelect = document.getElementById('phongSelect');
const thangInput = document.querySelector('[name="thang_nam"]');

const dienCu  = document.querySelector('[name="dien_cu"]');
const dienMoi = document.querySelector('[name="dien_moi"]');
const nuocCu  = document.querySelector('[name="nuoc_cu"]');
const nuocMoi = document.querySelector('[name="nuoc_moi"]');

const tienDien = document.querySelector('[name="tiendien"]');
const tienNuoc = document.querySelector('[name="tiennuoc"]');
const tongTien = document.querySelector('[name="tongtien"]');

const tienPhongInput = document.querySelector('[name="tien_phong"]');
const tienDVInput = document.querySelector('[name="tien_dich_vu"]');

async function loadRooms() {
    try {
        const res = await fetch('api/phong_api.php?action=get_all');
        const rooms = await res.json();
        const currentRoomId = "<?= $editData['phong_id'] ?? '' ?>";

        let html = '<option value="">-- Hãy chọn phòng để tính tiền --</option>';
        rooms.forEach(r => {
            const selected = (currentRoomId == r.id) ? 'selected' : '';
            html += `<option value="${r.id}" ${selected}>${r.ma_phong} (Phòng ${r.loai_phong})</option>`;
        });
        phongSelect.innerHTML = html;

        if (!dienCu.value || dienCu.value === "0") {
            layChiSoCu();
            layTienPhongVaDV();
        }
    } catch(e) { console.error("Lỗi load phòng"); }
}

async function tinhTien() {
    const dCu = parseInt(dienCu.value) || 0;
    const dMoi = parseInt(dienMoi.value) || 0;
    const nCu = parseInt(nuocCu.value) || 0;
    const nMoi = parseInt(nuocMoi.value) || 0;

    if (dMoi < dCu || nMoi < nCu) return;

    // Gọi API để tính tiền
    try {
        const response = await fetch(`api/dien_nuoc_api.php?action=tinh_tien&dien_cu=${dCu}&dien_moi=${dMoi}&nuoc_cu=${nCu}&nuoc_moi=${nMoi}`);
        const data = await response.json();

        const tp  = parseInt(tienPhongInput.value) || 0;
        const tdv = parseInt(tienDVInput.value) || 0;

        tienDien.value = data.tien_dien;
        tienNuoc.value = data.tien_nuoc;
        tongTien.value = data.tien_dien + data.tien_nuoc + tp + tdv;
    } catch (error) {
        console.error("Lỗi tính tiền:", error);
    }
}

function layChiSoCu() {
    const phong_id = phongSelect.value;
    const thang = thangInput.value;

    if (!phong_id || !thang) return;

    fetch(`api/dien_nuoc_api.php?action=get_chi_so_cu&phong_id=${phong_id}&thang=${thang}`)
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
    const phong_id = phongSelect.value;
    if (!phong_id) {
        tienPhongInput.value = 0;
        tienDVInput.value = 0;
        tinhTien();
        return;
    }

    fetch(`api/dien_nuoc_api.php?action=get_tien_phong_dv&phong_id=${phong_id}`)
        .then(res => res.json())
        .then(data => {
            tienPhongInput.value = data.tien_phong || 0;
            tienDVInput.value = data.tien_dich_vu || 0;
            tinhTien();
        });
}

phongSelect.addEventListener('change', () => { layChiSoCu(); layTienPhongVaDV(); });
thangInput.addEventListener('change', layChiSoCu);
dienMoi.addEventListener('input', tinhTien);
nuocMoi.addEventListener('input', tinhTien);
document.addEventListener('DOMContentLoaded', loadRooms);

// ==================== LOGIC XUẤT EXCEL ====================
async function exportExcelHoaDon() {
    try {
        // Gọi API để lấy dữ liệu hóa đơn
        const response = await fetch('api/admin_hoa_don_api.php?action=get_all_for_export');
        const result = await response.json();

        if (!result.success || !result.data || result.data.length === 0) {
            alert("Không có dữ liệu hóa đơn nào để xuất!");
            return;
        }

        const rawData = result.data;

        // Tạo mảng dữ liệu mới với tiêu đề cột tiếng Việt rõ ràng
        const rows = rawData.map((r, i) => ({
            'STT': i + 1,
            'Mã Hóa Đơn': r.ma_hd,
            'Phòng': r.ma_phong || 'Chưa rõ',
            'Tháng/Năm': r.thang_nam,
            'Số Điện': parseInt(r.so_dien) || 0,
            'Số Nước': parseInt(r.so_nuoc) || 0,
            'Tiền Điện (VNĐ)': parseInt(r.tien_dien) || 0,
            'Tiền Nước (VNĐ)': parseInt(r.tien_nuoc) || 0,
            'Tiền Phòng (VNĐ)': parseInt(r.tien_phong) || 0,
            'Tiền Dịch Vụ (VNĐ)': parseInt(r.tien_dich_vu) || 0,
            'Tổng tiền (VNĐ)': parseInt(r.tong_tien) || 0,
            'Trạng thái': r.trang_thai == 1 ? 'Đã thu' : 'Chưa thu',
            'Ngày lập': r.ngay_lap
        }));

        // Gọi thư viện SheetJS để tạo file
        const ws = XLSX.utils.json_to_sheet(rows);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Danh sách Hóa đơn');

        // Tải xuống file với tên có chứa ngày tháng hiện tại
        const fileName = `HoaDon_KTX_${new Date().toLocaleDateString('vi-VN').replace(/\//g,'-')}.xlsx`;
        XLSX.writeFile(wb, fileName);

    } catch (error) {
        alert("Có lỗi xảy ra trong quá trình tạo file Excel!");
        console.error(error);
    }
}