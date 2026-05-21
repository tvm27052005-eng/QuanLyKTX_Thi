// public/js/cua_toi.js
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modal-detail');
    const modalContent = modal.querySelector('div');
    const btnClose = document.getElementById('btn-close-modal');
    const btnsDetail = document.querySelectorAll('.btn-detail');

    function openModal() {
        modal.style.display = 'block';
        setTimeout(() => {
            modalContent.style.transform = 'translate(-50%, -50%) scale(1)';
            modalContent.style.opacity = '1';
        }, 10);
    }

    function closeModal() {
        modalContent.style.transform = 'translate(-50%, -50%) scale(0.9)';
        modalContent.style.opacity = '0';
        setTimeout(() => {
            modal.style.display = 'none';
            modalContent.style.transform = 'translate(-50%, -50%) scale(0.9)';
        }, 300);
    }

    btnsDetail.forEach(btn => {
        btn.addEventListener('click', function() {
            const data = this.dataset;
            document.getElementById('modal-ten-phong').textContent = data.tenPhong;
            document.getElementById('modal-dien-cu').textContent = data.dienCu;
            document.getElementById('modal-dien-moi').textContent = data.dienMoi;
            document.getElementById('modal-nuoc-cu').textContent = data.nuocCu;
            document.getElementById('modal-nuoc-moi').textContent = data.nuocMoi;
            document.getElementById('modal-tien-dien').textContent = new Intl.NumberFormat('vi-VN').format(data.tienDien);
            document.getElementById('modal-tien-nuoc').textContent = new Intl.NumberFormat('vi-VN').format(data.tienNuoc);
            document.getElementById('modal-tien-phong').textContent = new Intl.NumberFormat('vi-VN').format(data.tienPhong);
            document.getElementById('modal-tien-dich-vu').textContent = new Intl.NumberFormat('vi-VN').format(data.tienDichVu);
            document.getElementById('modal-tong-tien').textContent = new Intl.NumberFormat('vi-VN').format(data.tongTien);

            const serviceList = document.getElementById('modal-dich-vu-list');
            serviceList.innerHTML = '';
            let services = [];
            try {
                services = JSON.parse(data.dichVuJson || '[]');
            } catch (e) {
                services = [];
            }

            if (Array.isArray(services) && services.length > 0) {
                services.forEach(item => {
                    const li = document.createElement('li');
                    const name = item.ten_dich_vu || 'Dịch vụ';
                    const price = new Intl.NumberFormat('vi-VN').format(item.don_gia || 0);
                    li.style.margin = '3px 0';
                    li.textContent = `${name} - ${price} đ`;
                    serviceList.appendChild(li);
                });
            } else {
                serviceList.innerHTML = '<li style="margin:3px 0; color:#555;">Không có dịch vụ</li>';
            }

            openModal();
        });
    });

    btnClose.addEventListener('click', closeModal);

    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    // ESC key to close
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modal.style.display === 'block') {
            closeModal();
        }
    });
});
