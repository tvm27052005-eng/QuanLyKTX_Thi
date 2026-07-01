<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Hợp đồng - UTT KTX</title>
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        .form-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 20px; }
        .form-group label { font-size: 14px; font-weight: 600; display: block; margin-bottom: 5px; color: #333; }
        .form-group input, .form-group select { width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd; outline: none; }
        .form-group input:focus, .form-group select:focus { border-color: #1565C0; }
        .btn-action { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; color: white; transition: 0.2s;}
        .btn-primary { background-color: #1565C0; } .btn-primary:hover { background-color: #0d47a1;}
        .btn-secondary { background-color: #e0e0e0; color: #333; } .btn-secondary:hover { background-color: #ccc; }
        .panel { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px;}
        table { width: 100%; border-collapse: collapse; }
        table th, table td { padding: 12px 15px; border-bottom: 1px solid #eee; text-align: left; }
        table th { background: #f8f9fa; color: #555; }
        .btn-sm { padding: 5px 10px; font-size: 12px; border: none; border-radius: 4px; cursor: pointer; color: white; }
        .btn-warning { background-color: #f39c12; }
        .btn-danger { background-color: #e53935; }
        .details-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.7); justify-content: center; align-items: center; }
        .modal-content { background: white; padding: 20px; border-radius: 8px; width: 400px; }
        .modal-content { background: white; padding: 20px; border-radius: 8px; width: 400px; max-height: 80vh; overflow-y: auto;}
   </style>
</head>


<body>
    <?php include 'app/views/layout/sidebar.php'; ?>
    <div class="main-content">
    <?php include 'app/views/layout/header.php'; ?>
        <div class="page-content">
            <div class="panel">
                <h2 style="color: #1565C0; margin-top: 0; margin-bottom: 20px;">Quản lý Hợp đồng</h2>
                <input type="hidden" id="id">              
                <div class="form-grid">
                    <div class="form-group"><label>Mã Sinh Viên</label><input type="text" id="ma_sv" placeholder="Mã SV"></div>
                    <div class="form-group"><label>Họ Tên</label><input type="text" id="ho_ten" placeholder="Tên sinh viên"></div>
                    <div class="form-group"><label>Lớp</label><input type="text" id="lop"></div>
                    <div class="form-group"><label>Ngày Sinh</label><input type="date" id="ngay_sinh"></div>
                    <div class="form-group">
                        <label>Giới Tính</label>
                        <select id="gioi_tinh">
                            <option value="Nam">Nam</option>
                            <option value="Nữ">Nữ</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Địa Chỉ</label><input type="text" id="dia_chi"></div>
                    <div class="form-group"><label>Số Điện Thoại</label><input type="text" id="sdt"></div>
                    <div class="form-group"><label>Ngày Vào</label><input type="date" id="ngay_vao"></div>
                    <div class="form-group"><label>Ngày Hết Hạn</label><input type="date" id="ngay_het_han"></div>
                </div>           
                <div>
                     <button class="btn-action btn-primary" onclick="saveHopDong()">Lưu Hợp Đồng</button>
                    <button class="btn-action btn-secondary" onclick="resetForm()">Làm mới</button>
                    <a href="#" onclick="showDetails()" style="margin-left: 10px; color: #1565C0;">Điều khoản hợp đồng</a>
                </div>
            </div>

            <div class="panel">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3 style="margin: 0; color: #333;">Danh sách hợp đồng</h3>
                    <input type="text" id="searchKeyword" placeholder="Tìm kiếm..." style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; width: 250px;">
                </div>
                <div>
                    <button class="btn-action btn-primary" onclick="loadHopDong()">Tìm kiếm</button>
                </div>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Mã SV</th>
                                <th>Họ Tên</th>
                                <th>Lớp</th>
                                <th>Địa chỉ</th>
                                <th>Số điện thoại</th>
                                <th>Ngày vào</th>
                                <th>Ngày hết hạn</th>
                                <th style="text-align: center;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="hopDongTableBody">
                            <tr><td colspan="8" style="text-align: center;">Đang tải dữ liệu...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    
    <script>
        const API_HD = 'api/hop_dong_api.php';

        async function loadHopDong() {
            const kw = document.getElementById("searchKeyword").value;
            try {
                const res = await fetch(`${API_HD}?action=get_all&keyword=${encodeURIComponent(kw)}`);
                const result = await res.json();
                let html = '';
                
                if(!result.data || result.data.length === 0) {
                    html = '<tr><td colspan="8" style="text-align:center;">Không có dữ liệu hợp đồng.</td></tr>';
                } else {
                    result.data.forEach(item => {
                        html += `
                        <tr>
                            <td><strong>${item.ma_sv}</strong></td>
                            <td>${item.ho_ten}</td>
                            <td>${item.lop}</td>
                            <td>${item.dia_chi}</td>
                            <td>${item.sdt}</td>
                            <td style="color: #2e7d32; font-weight: bold;">${item.ngay_vao}</td>
                            <td style="color: #c62828; font-weight: bold;">${item.ngay_het_han}</td>
                            <td style="text-align: center;">
                                <button class="btn-warning btn-sm" onclick='editHopDong(${JSON.stringify(item)})'>Sửa</button>
                                <button class="btn-danger btn-sm" onclick='deleteHopDong(${item.id})'>Xóa</button>
                            </td>
                        </tr>`;
                    });
                }
                document.getElementById("hopDongTableBody").innerHTML = html;
            } catch (error) { console.error(error); alert("Lỗi tải dữ liệu!"); }
        }

        
        function calculateEndDate(startDate) {
            const date = new Date(startDate);
            date.setMonth(date.getMonth() + 6);
            return date.toISOString().split('T')[0];
        }

        document.getElementById("ngay_vao").addEventListener("change", function() {
            if (this.value) {
                const endDate = calculateEndDate(this.value);
                document.getElementById("ngay_het_han").value = endDate;
            }
        });
        

        async function saveHopDong() {
            if (!document.getElementById("acceptTerms").checked) {
            alert("Bạn phải chấp nhận điều khoản hợp đồng trước khi lưu!");
            return;
          }
            const data = {
            id: document.getElementById("id").value.trim(),
            ma_sv: document.getElementById("ma_sv").value.trim(),
            ho_ten: document.getElementById("ho_ten").value.trim(),
            lop: document.getElementById("lop").value.trim(),
            ngay_sinh: document.getElementById("ngay_sinh").value,
            gioi_tinh: document.getElementById("gioi_tinh").value,
            dia_chi: document.getElementById("dia_chi").value.trim(),
            sdt: document.getElementById("sdt").value.trim(),
            ngay_vao: document.getElementById("ngay_vao").value,
            ngay_het_han: document.getElementById("ngay_het_han").value
            };

            for (const [key, value] of Object.entries(data)) {
                if (key !== "id" && value === "") {
                    alert("Vui lòng nhập đầy đủ thông tin!");
                    return;
                }
            }

            const startDate = new Date(data.ngay_vao);
            const minEndDate = new Date(startDate);
            minEndDate.setMonth(minEndDate.getMonth() + 6);
            const endDate = new Date(data.ngay_het_han);
            if (endDate < minEndDate) {
                alert("Ngày hết hạn phải tối thiểu 6 tháng kể từ ngày vào!");
                return;
            }
           
            if (!/^\d+$/.test(data.sdt)) {
                alert("Số điện thoại chỉ được phép nhập số!");
                return;
            }
         
            if (!data.id) {
                const resCheck = await fetch(`${API_HD}?action=check_ma_sv&ma_sv=${encodeURIComponent(data.ma_sv)}`);
                const checkResult = await resCheck.json();
                if (checkResult.exists) {
                    alert("Mã sinh viên đã tồn tại, vui lòng nhập mã khác!");
                    return;
                }
            }

            const action = data.id ? 'update' : 'insert';
            const res = await fetch(`${API_HD}?action=${action}`, {
                method: data.id ? 'PUT' : 'POST',
                body: JSON.stringify(data)
            });
            const result = await res.json();
            alert(result.message);
            if (result.success) { resetForm(); loadHopDong(); }
        }



        function editHopDong(item) {
            document.getElementById("id").value = item.id;
            document.getElementById("ma_sv").value = item.ma_sv;
            document.getElementById("ho_ten").value = item.ho_ten;
            document.getElementById("lop").value = item.lop;
            document.getElementById("ngay_sinh").value = item.ngay_sinh;
            document.getElementById("gioi_tinh").value = item.gioi_tinh;
            document.getElementById("dia_chi").value = item.dia_chi;
            document.getElementById("sdt").value = item.sdt;
            document.getElementById("ngay_vao").value = item.ngay_vao;
            document.getElementById("ngay_het_han").value = item.ngay_het_han;

            const endDate = calculateEndDate(item.ngay_vao);
            document.getElementById("ngay_het_han").value = endDate;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }


        function resetForm() {
            document.getElementById("id").value = "";
            document.getElementById("ma_sv").value = "";
            document.getElementById("ho_ten").value = "";
            document.getElementById("lop").value = "";
            document.getElementById("ngay_sinh").value = "";
            document.getElementById("gioi_tinh").value = "Nam";
            document.getElementById("dia_chi").value = "";
            document.getElementById("sdt").value = "";
            document.getElementById("ngay_vao").value = "";
            document.getElementById("ngay_het_han").value = "";
        }


        async function deleteHopDong(id) {
            if(!confirm("Bạn có chắc muốn xóa hợp đồng này?")) return;
            const res = await fetch(`${API_HD}?action=delete`, { 
                method: 'DELETE', 
                body: JSON.stringify({id: id}) 
            });
            const result = await res.json();
            alert(result.message);
            if(result.success) loadHopDong();
        }


        document.getElementById("searchKeyword").addEventListener("keypress", e => { if(e.key === "Enter") loadHopDong(); });
        document.addEventListener('DOMContentLoaded', loadHopDong);
        function showDetails() {
        document.getElementById("detailsModal").style.display = "flex";
        }

        function closeDetails(reset = true) {
             document.getElementById("detailsModal").style.display = "none";
             if (reset) {
             document.getElementById("acceptTerms").checked = false;
             document.getElementById("btnLuuHopDong").style.display = "none";
            }
        }

        function acceptTerms() {
            if (!document.getElementById("acceptTerms").checked) {
            alert("Bạn phải chấp nhận điều khoản hợp đồng để tiếp tục.");
            return;
            }
            closeDetails(false);
        }

        document.getElementById("acceptTerms").addEventListener("change", function() {
        if(this.checked) {
        document.getElementById("btnLuuHopDong").style.display = "inline-block";
        } else {
        document.getElementById("btnLuuHopDong").style.display = "none";
        }
        });
    </script>

<div class="details-modal" id="detailsModal">
    <div class="modal-content">
        <p><h3 style="text-align:center">Quy Định Ký Túc Xá</h3>
            <h4><br>Điều 1: Quy định về Trật tự và An ninh</h4>
                <br> - Giờ giấc: Ký túc xá đóng cửa vào lúc 23:00 và mở cửa vào lúc 05:00 sáng hôm sau. Mọi trường hợp đi muộn phải có lý do chính đáng và báo trước.
                <br> - Khách thăm: Chỉ được tiếp khách tại phòng sinh hoạt chung. Không được tự ý đưa người lạ vào phòng ở hoặc cho người lạ ngủ lại qua đêm khi chưa có sự đồng ý của Ban quản lý.
                <br> - Tiếng ồn: Không gây ồn ào, hát hò, mở nhạc quá lớn sau 22:00.
            <h4><br>Điều 2: Quy định về Vệ sinh và Tài sản</h4>
                <br> - Vệ sinh: Giữ gìn vệ sinh chung trong phòng và khu vực hành lang. Rác phải được phân loại và đổ đúng nơi quy định.
                <br> - Tài sản: Bên B có trách nhiệm bảo quản các trang thiết bị đã bàn giao (giường, tủ, đèn, quạt...). Nếu làm hỏng hoặc mất mát phải bồi thường theo giá thị trường.
                <br> - Cải tạo: Không được tự ý đóng đinh, khoan tường, sơn sửa hoặc dán giấy dán tường làm thay đổi hiện trạng phòng khi chưa được phép.
            <h4><br>Điều 3: Quy định về Phòng cháy chữa cháy (PCCC) và An toàn</h4>
                <br> - Nấu nướng: Không nấu ăn trong phòng ngủ (trừ khu vực bếp chung nếu có).
                <br> - Thiết bị điện: Không sử dụng các thiết bị có công suất lớn dễ gây cháy nổ như bếp từ, lò vi sóng, bàn là... trong phòng ở mà không đăng ký.
                <br> - Chất cấm: Tuyệt đối không tàng trữ, sử dụng chất gây nghiện, vũ khí, vật liệu nổ hoặc các loại hóa chất độc hại.
                <h4><br>Điều 4: Các hành vi bị nghiêm cấm</h4>
                <br> - Đánh bạc, tổ chức đánh bạc dưới mọi hình thức.
                <br> - Uống rượu bia, hút thuốc lá trong khuôn viên phòng ở.
                <br> - Xem, truyền bá các văn hóa phẩm đồi trụy.
                <br> - Trộm cắp, gây gổ đánh nhau hoặc có hành vi thiếu văn hóa.
            <h4><br>Điều 5: Điều khoản thi hành</h4>
                <br> - Bên nào vi phạm các điều khoản trên tùy theo mức độ sẽ bị nhắc nhở, phạt tiền hoặc chấm dứt hợp đồng trước thời hạn mà không được hoàn lại tiền cọc.
                <br> - Hợp đồng này được lập thành 02 bản, mỗi bên giữ 01 bản có giá trị pháp lý như nhau.</p>
    <br><label><input type="checkbox" id="acceptTerms"> Chấp nhận điều khoản hợp đồng</label>
        <div style="margin-top: 20px;">
            <button class="btn-action btn-primary" onclick="acceptTerms()">Tiếp tục</button>
            <button class="btn-action btn-danger" onclick="closeDetails()">Đóng</button>
        </div>
    </div>
</div>
</body>
</html>