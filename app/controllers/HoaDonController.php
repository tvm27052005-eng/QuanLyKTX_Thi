
<?php
require_once 'app/models/HoaDonModel.php';

class HoaDonController {
    private $model;
    private $db;


    public function thanh_toan() {
    require_once 'app/views/hoa_don/thanh_toan.php';
}

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->model = new HoaDonModel($this->db);
    }

    private function getRoomServiceSnapshot($phong_id) {
        $services = [];
        $phong_id = (int)$phong_id;
        $sql = "SELECT dv.ten_dich_vu, dv.don_gia FROM phong_dich_vu pdv JOIN dich_vu dv ON pdv.dich_vu_id = dv.id WHERE pdv.phong_id = $phong_id";
        $result = $this->db->query($sql);
        while ($result && $row = $result->fetch_assoc()) {
            $services[] = $row;
        }
        return $services;
    }

    public function index() {
        $keyword = $_GET['keyword'] ?? '';
        $listHoaDon = $this->model->getAll($keyword);

        $editData = null;
        $dien_cu_edit = 0;
        $nuoc_cu_edit = 0;

        if (isset($_GET['edit'])) {
            $id = $_GET['edit'];
            $editData = $this->model->getById($id);

            if ($editData) {
                $thang_truoc = date('Y-m', strtotime($editData['thang_nam'] . '-01 -1 month'));
                $old = $this->model->getChiSoCu($editData['phong_id'], $thang_truoc);

                if ($old) {
                    $dien_cu_edit = $old['dien_moi'];
                    $nuoc_cu_edit = $old['nuoc_moi'];
                }
            }
        }

        require_once 'app/views/hoa_don/index.php';
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $this->model->delete($_GET['id']);
        }
        header("Location: index.php?controller=hoadon&action=index");
        exit();
    }

    public function save() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $data = [
'id' => $_POST['id'] ?? '',
                'mahd' => trim($_POST['mahd']),
                'phong_id' => (int)($_POST['phong_id']),
                'thang_nam' => $_POST['thang_nam'],
                'ngaylap' => $_POST['ngaylap'],
                'dien_cu' => (int)($_POST['dien_cu'] ?? 0),
                'dien_moi' => (int)($_POST['dien_moi'] ?? 0),
                'nuoc_cu' => (int)($_POST['nuoc_cu'] ?? 0),
                'nuoc_moi' => (int)($_POST['nuoc_moi'] ?? 0),
                'trangthai' => (int)($_POST['trangthai'] ?? 0),
                'tien_phong' => (int)($_POST['tien_phong'] ?? 0),
                'tien_dich_vu' => (int)($_POST['tien_dich_vu'] ?? 0)
            ];

            if ($data['dien_moi'] < $data['dien_cu'] || $data['nuoc_moi'] < $data['nuoc_cu']) {
                echo "<script>alert('Chỉ số mới không được nhỏ hơn chỉ số cũ!'); history.back();</script>";
                exit();
            }

            if ($this->model->checkMaHD($data['mahd'], $data['id'])) {
                echo "<script>alert('Mã hóa đơn đã tồn tại!'); history.back();</script>";
                exit();
            }
if ($this->model->checkPhongThang($data['phong_id'], $data['thang_nam'], $data['id'])) {
                echo "<script>alert('Phòng này đã có hóa đơn trong tháng này!'); history.back();</script>";
                exit();
            }

            $data['sodien'] = $data['dien_moi'] - $data['dien_cu'];
            $data['sonuoc'] = $data['nuoc_moi'] - $data['nuoc_cu'];

            $data['tiendien'] = $data['sodien'] * 3500;
            $data['tiennuoc'] = $data['sonuoc'] * 15000;

            $data['tongtien'] = $data['tiendien'] + $data['tiennuoc'] + $data['tien_phong'] + $data['tien_dich_vu'];

            $serviceSnapshot = $this->getRoomServiceSnapshot($data['phong_id']);
            $serviceJson = json_encode($serviceSnapshot, JSON_UNESCAPED_UNICODE);

            if ($data['id'] !== '') {
                $existingHoaDon = $this->model->getById($data['id']);
                if ($existingHoaDon && !empty($existingHoaDon['danh_sach_dich_vu'])) {
                    $serviceJson = $existingHoaDon['danh_sach_dich_vu'];
                }
            }

            $data['danh_sach_dich_vu'] = $this->db->real_escape_string($serviceJson);

            if ($data['id'] == '') {
                $this->model->insert($data);
            } else {
                $this->model->update($data);
            }

            header("Location: index.php?controller=hoadon&action=index");
            exit();
        }
    }

    public function cua_toi() {
        $phong_id = $_SESSION['phong_id_cua_sv'] ?? 0;
        $listHoaDon = ($phong_id > 0) ? $this->model->getHoaDonByPhong($phong_id) : [];

        // Lấy tên phòng
        $ten_phong = '';
        if ($phong_id > 0) {
            $sql = "SELECT ma_phong FROM phong WHERE id = $phong_id";
            $result = $this->db->query($sql);
            if ($result && $result->num_rows > 0) {
                $ten_phong = $result->fetch_assoc()['ma_phong'];
            }
        }

        // Dịch vụ mặc định của phòng để làm fallback khi hóa đơn cũ chưa lưu chi tiết dịch vụ
        $defaultDichVuList = [];
        if ($phong_id > 0) {
            $defaultDichVuList = $this->getRoomServiceSnapshot($phong_id);
        }

        require_once 'app/views/hoa_don/cua_toi.php';
    }

    public function export() {
        echo "Tính năng xuất Excel đang được cập nhật...";
    }
    
}
?>