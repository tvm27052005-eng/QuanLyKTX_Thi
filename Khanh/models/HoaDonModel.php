<?php
class HoaDonModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy danh sách hóa đơn (Join với bảng phong để lấy tên phòng)
    public function getAll($keyword = '') {
        $where = "1";
        if (!empty($keyword)) {
            $kw = $this->conn->real_escape_string($keyword);
            $where .= " AND (hd.ma_hd LIKE '%$kw%' OR p.ma_phong LIKE '%$kw%')";
        }
        $sql = "SELECT hd.*, p.ma_phong 
                FROM hoa_don_dien_nuoc hd 
                LEFT JOIN phong p ON hd.phong_id = p.id 
                WHERE $where ORDER BY hd.id DESC";
        $result = $this->conn->query($sql);
        
        $data = [];
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // Lấy 1 hóa đơn theo ID (để sửa)
    public function getById($id) {
        $id = (int)$id;
        $sql = "SELECT hd.*, p.ma_phong FROM hoa_don_dien_nuoc hd LEFT JOIN phong p ON hd.phong_id = p.id WHERE hd.id = $id";
        $result = $this->conn->query($sql);
        return $result->fetch_assoc();
    }

    // Lấy chỉ số tháng cũ
    public function getChiSoCu($phong_id, $thang_truoc) {
        $phong_id = (int)$phong_id;
        $sql = "SELECT dien_moi, nuoc_moi FROM hoa_don_dien_nuoc WHERE phong_id=$phong_id AND thang_nam='$thang_truoc'";
        $result = $this->conn->query($sql);
        return $result->fetch_assoc();
    }

    // Kiểm tra trùng mã hóa đơn
    public function checkMaHD($mahd, $id = '') {
        $sql = "SELECT id FROM hoa_don_dien_nuoc WHERE ma_hd='$mahd'";
        if ($id != '') $sql .= " AND id<>$id";
        return $this->conn->query($sql)->num_rows > 0;
    }

    // Kiểm tra phòng đã có hóa đơn tháng này chưa
    public function checkPhongThang($phong_id, $thang_nam, $id = '') {
        $phong_id = (int)$phong_id;
        $sql = "SELECT id FROM hoa_don_dien_nuoc WHERE phong_id=$phong_id AND thang_nam='$thang_nam'";
        if ($id != '') $sql .= " AND id<>$id";
        return $this->conn->query($sql)->num_rows > 0;
    }

    // Thêm mới
    public function insert($data) {
        extract($data);
        $sql = "INSERT INTO hoa_don_dien_nuoc
                (ma_hd, phong_id, thang_nam, dien_cu, dien_moi, nuoc_cu, nuoc_moi, so_dien, so_nuoc,
                 tien_dien, tien_nuoc, tien_phong, tien_dich_vu, tong_tien, ngay_lap, trang_thai, danh_sach_dich_vu)
                VALUES (
                '$mahd', $phong_id, '$thang_nam', $dien_cu, $dien_moi, $nuoc_cu, $nuoc_moi, $sodien, $sonuoc,
                $tiendien, $tiennuoc, $tien_phong, $tien_dich_vu, $tongtien, '$ngaylap', $trangthai,
                '$danh_sach_dich_vu')";
        return $this->conn->query($sql);
    }

    // Cập nhật
    public function update($data) {
        extract($data);
        $sql = "UPDATE hoa_don_dien_nuoc SET
ma_hd='$mahd', phong_id=$phong_id, thang_nam='$thang_nam', 
                dien_cu=$dien_cu, dien_moi=$dien_moi, nuoc_cu=$nuoc_cu, nuoc_moi=$nuoc_moi,
                so_dien=$sodien, so_nuoc=$sonuoc, tien_dien=$tiendien, tien_nuoc=$tiennuoc,
                tien_phong=$tien_phong, tien_dich_vu=$tien_dich_vu, tong_tien=$tongtien, 
                ngay_lap='$ngaylap', trang_thai=$trangthai,
                danh_sach_dich_vu='$danh_sach_dich_vu'
                WHERE id=$id";
        return $this->conn->query($sql);
    }

    // Xóa
    public function delete($id) {
        $id = (int)$id;
        return $this->conn->query("DELETE FROM hoa_don_dien_nuoc WHERE id=$id");
    }
    // Lấy hóa đơn theo ID phòng (Dành cho Sinh viên)
    public function getHoaDonByPhong($phong_id) {
        $phong_id = (int)$phong_id;
        $sql = "SELECT * FROM hoa_don_dien_nuoc WHERE phong_id = $phong_id ORDER BY id DESC";
        $result = $this->conn->query($sql);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) { $data[] = $row; }
        }
        return $data;
    }

    public function getOutstandingBillByRoom($phong_id) {
    $phong_id = (int)$phong_id;
    $sql = "SELECT hd.*, p.ma_phong
        FROM hoa_don_dien_nuoc hd
        JOIN phong p ON hd.phong_id = p.id
        WHERE hd.phong_id = $phong_id
        AND hd.trang_thai = 0
        ORDER BY hd.id DESC
        LIMIT 1";
    $result = $this->conn->query($sql);
    return $result ? $result->fetch_assoc() : null;
} 
}
?>


