<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán hóa đơn - UTT KTX</title>
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        .payment-panel { max-width: 900px; margin: 30px auto; background: #fff; padding: 25px; border-radius: 14px; box-shadow: 0 10px 35px rgba(0,0,0,0.08); }
        .payment-panel h2 { margin-top: 0; color: #1565C0; }
        .payment-info { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px; }
        .payment-card { background: #f8f9fa; padding: 18px 20px; border-radius: 12px; border: 1px solid #e0e0e0; }
        .payment-card h3 { margin: 0 0 10px; color: #333; font-size: 16px; }
        .payment-card p { margin: 8px 0; color: #444; font-size: 14px; line-height: 1.5; }
        .payment-action { text-align: center; margin-top: 20px; }
        .btn-pay { background: #2e7d32; color: #fff; padding: 14px 30px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; transition: 0.2s; }
        .btn-pay:hover { background: #27642a; }
        .qr-area { text-align: center; }
        .qr-area img { max-width: 260px; width: 100%; border: 1px solid #ddd; border-radius: 12px; }
        .note { margin-top: 20px; color: #c62828; font-weight: 700; }
        
        .status-notification { padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; display: none; }
        .status-notification.success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .status-notification.error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .status-notification.pending { background: #fff3cd; border: 1px solid #ffeeba; color: #856404; }
        .status-notification.show { display: block; }
        .status-icon { display: inline-block; margin-right: 8px; font-weight: bold; }
    </style>
</head>
<body>
    <?php include 'app/views/layout/sidebar.php'; ?>
    <div class="main-content">
        <?php include 'app/views/layout/header.php'; ?>
        <div class="page-content">
            <div class="payment-panel">
                <h2>Thanh toán hóa đơn phòng</h2>
                
                <!-- Status Notification -->
                <div id="statusNotification" class="status-notification">
                    <span class="status-icon" id="statusIcon"></span>
                    <span id="statusMessage"></span>
                </div>
                
                <div id="paymentContainer">
                    <p>Đang tải thông tin hóa đơn...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const API_PAYMENT = 'api/hoa_don_api.php?action=get_my_bill';
        const API_PAY = 'api/hoa_don_api.php?action=pay';
        const API_STATUS = 'api/hoa_don_api.php?action=get_payment_status';

        function formatCurrency(value) {
            return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
        }

        function showStatusNotification(status, message) {
            const notification = document.getElementById('statusNotification');
            const icon = document.getElementById('statusIcon');
            const msg = document.getElementById('statusMessage');
            
            notification.className = 'status-notification show ' + status;
            
            if (status === 'success') {
                icon.textContent = '✓';
                msg.textContent = message || 'Yêu cầu thanh toán của bạn đã được chấp nhận!';
            } else if (status === 'error') {
                icon.textContent = '✕';
                msg.textContent = message || 'Yêu cầu thanh toán bị từ chối. Vui lòng liên hệ admin.';
            } else if (status === 'pending') {
                icon.textContent = '⧖';
                msg.textContent = message || 'Yêu cầu thanh toán đang chờ xử lý...';
            }
        }

        function hideStatusNotification() {
            document.getElementById('statusNotification').className = 'status-notification';
        }

        async function checkPaymentStatus() {
            try {
                const res = await fetch(API_STATUS);
                const result = await res.json();
                
                if (result.success && result.data) {
                    const status = result.data.trang_thai;
                    if (status === 'approved') {
                        showStatusNotification('success', 'Yêu cầu thanh toán của bạn đã được Admin xác nhận. Hóa đơn đã thanh toán!');
                    } else if (status === 'rejected') {
                        showStatusNotification('error', 'Yêu cầu thanh toán bị từ chối. Vui lòng liên hệ Admin để biết thêm chi tiết.');
                    } else if (status === 'pending') {
                        showStatusNotification('pending', 'Yêu cầu thanh toán của bạn đang chờ Admin xác nhận...');
                    }
                }
            } catch (error) {
                console.error('Lỗi kiểm tra trạng thái:', error);
            }
        }

        async function loadPaymentData() {
            try {
                const res = await fetch(API_PAYMENT);
                const result = await res.json();
                const container = document.getElementById('paymentContainer');

                if (!result.success || !result.data) {
                    container.innerHTML = `<p style="color:#c62828;">${result.message || 'Không có hóa đơn để thanh toán.'}</p>`;
                    return;
                }

                const bill = result.data;
                const amountText = formatCurrency(parseInt(bill.tong_tien || 0, 10));
                const qrText = `PHÒNG:${bill.ma_phong}|HÓA_ĐƠN:${bill.ma_hd}|SỐ_TIỀN:${bill.tong_tien}`;

                container.innerHTML = `
                    <div class="payment-info">
                        <div class="payment-card">
                            <h3>Thông tin hóa đơn</h3>
                            <p><strong>Phòng:</strong> ${bill.ma_phong || '---'}</p>
                            <p><strong>Kỳ hóa đơn:</strong> ${bill.thang_nam}</p>
                            <p><strong>Mã hóa đơn:</strong> ${bill.ma_hd}</p>
                            <p><strong>Số tiền cần thanh toán là:</strong> <span style="color:#c62828; font-weight:bold; font-size:16px;">${amountText}</span></p>
                            <p class="note">Vui lòng thực hiện thanh toán qua mã QR bên cạnh.</p>
                        </div>
                        <div class="payment-card qr-area">
                            <h3>Mã QR thanh toán</h3>
                            <img id="qrImage" src="public/images/qr_thanh_toan.jpg" alt="QR Thanh toán" style="max-width: 250px; border: 2px solid #1565C0; border-radius: 12px;">
                            <p style="margin-top: 12px; color:#1565C0; font-weight:bold; font-size:14px;">Chủ TK: TĂNG VĂN MINH - MB Bank</p>
                            <p style="margin-top: 5px; color:#c62828; font-size:13px;">Ghi chú: Nhập đúng số tiền và nội dung CK (VD: P102 HD01)</p>
                        </div>
                    </div>
                    <div class="payment-action">
                        <button class="btn-pay" onclick="payBill(${bill.id})">Tôi đã chuyển khoản xong</button>
                    </div>
                `;
            } catch (error) {
                document.getElementById('paymentContainer').innerHTML = `<p style="color:#c62828;">Lỗi tải dữ liệu. Vui lòng thử lại.</p>`;
                console.error(error);
            }
        }

        async function payBill(id) {
            if (!confirm('Xác nhận bạn đã thanh toán hóa đơn này?')) return;

            try {
                const res = await fetch(API_PAY, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });
                const result = await res.json();
                alert(result.message);
                if (result.success) {
                    // Hiển thị trạng thái pending khi yêu cầu gửi đi
                    showStatusNotification('pending', 'Yêu cầu thanh toán đang chờ Admin xác nhận...');
                    setTimeout(() => {
                        loadPaymentData();
                        checkPaymentStatus();
                    }, 1000);
                }
            } catch (error) {
                alert('Lỗi khi gửi yêu cầu thanh toán.');
                console.error(error);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadPaymentData();
            // Kiểm tra trạng thái mỗi 5 giây
            checkPaymentStatus();
            setInterval(checkPaymentStatus, 5000);
        });
    </script>
</body>
</html>