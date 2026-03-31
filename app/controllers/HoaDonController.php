<?php
require_once 'app/models/HoaDonModel.php';

class HoaDonController {
    private $model;
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->model = new HoaDonModel($this->db);
    }

    // Hiển thị giao diện chính
    public function index() {
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        $listHoaDon = $this->model->getAll($keyword);
        
        $editData = null;
        $dien_cu_edit = 0;
        $nuoc_cu_edit = 0;

        // Nếu có yêu cầu sửa (edit)
        if (isset($_GET['edit'])) {
            $id = $_GET['edit'];
            $editData = $this->model->getById($id);
            if ($editData) {
                $thang_truoc = date('Y-m', strtotime($editData['thang'] . '-01 -1 month'));
                $old = $this->model->getChiSoCu($editData['phong'], $thang_truoc);
                if ($old) {
                    $dien_cu_edit = $old['dien_moi'];
                    $nuoc_cu_edit = $old['nuoc_moi'];
                }
            }
        }

        // Gọi View để hiển thị
        require_once 'app/views/hoa_don/index.php';
    }

    // Xử lý Xóa
    public function delete() {
        if (isset($_GET['id'])) {
            $this->model->delete($_GET['id']);
        }
        header("Location: index.php?controller=hoadon&action=index");
        exit();
    }

    // Xử lý Lưu (Thêm/Sửa)
    public function save() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Lấy dữ liệu từ form
            $data = [
                'id' => $_POST['id'] ?? '',
                'mahd' => trim($_POST['mahd']),
                'phong' => trim($_POST['phong']),
                'thang' => $_POST['thang'],
                'ngaylap' => $_POST['ngaylap'],
                'dien_cu' => (int)($_POST['dien_cu'] ?? 0),
                'dien_moi' => (int)($_POST['dien_moi'] ?? 0),
                'nuoc_cu' => (int)($_POST['nuoc_cu'] ?? 0),
                'nuoc_moi' => (int)($_POST['nuoc_moi'] ?? 0),
                'trangthai' => (int)($_POST['trangthai'] ?? 0),
                'tien_phong' => (int)($_POST['tien_phong'] ?? 0),
                'tien_dich_vu' => (int)($_POST['tien_dich_vu'] ?? 0)
            ];

            // Validate cơ bản
            if ($data['dien_moi'] < $data['dien_cu'] || $data['nuoc_moi'] < $data['nuoc_cu']) {
                echo "<script>alert('Chỉ số mới không được nhỏ hơn chỉ số cũ!'); history.back();</script>";
                exit();
            }

            if ($this->model->checkMaHD($data['mahd'], $data['id'])) {
                echo "<script>alert('Mã hóa đơn đã tồn tại!'); history.back();</script>";
                exit();
            }

            if ($this->model->checkPhongThang($data['phong'], $data['thang'], $data['id'])) {
                echo "<script>alert('Phòng này đã có hóa đơn trong tháng này!'); history.back();</script>";
                exit();
            }

            // Tính toán
            $data['sodien'] = $data['dien_moi'] - $data['dien_cu'];
            $data['sonuoc'] = $data['nuoc_moi'] - $data['nuoc_cu'];
            $tiendien = $data['sodien'] * 3500;
            $tiennuoc = $data['sonuoc'] * 15000;
            $data['tongtien'] = $tiendien + $tiennuoc + $data['tien_phong'] + $data['tien_dich_vu'];

            // Lưu DB
            if ($data['id'] == '') {
                $this->model->insert($data);
            } else {
                $this->model->update($data);
            }

            header("Location: index.php?controller=hoadon&action=index");
            exit();
        }
    }

    // Hàm xuất Excel 
    public function export() {
        // Bạn có thể đưa code file export_excel.php cũ vào đây sau
        echo "Tính năng xuất Excel đang được cập nhật qua MVC...";
    }
}
?>