<?php
class HoaDonModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy danh sách hóa đơn có tìm kiếm
    public function getAll($keyword = '') {
        $where = "1";
        if (!empty($keyword)) {
            $kw = $this->conn->real_escape_string($keyword);
            $where .= " AND (ma_hd LIKE '%$kw%' OR phong LIKE '%$kw%')";
        }
        $sql = "SELECT * FROM hoa_don_dien_nuoc WHERE $where ORDER BY id DESC";
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
        $sql = "SELECT * FROM hoa_don_dien_nuoc WHERE id = $id";
        $result = $this->conn->query($sql);
        return $result->fetch_assoc();
    }

    // Lấy chỉ số tháng cũ
    public function getChiSoCu($phong, $thang_truoc) {
        $sql = "SELECT dien_moi, nuoc_moi FROM hoa_don_dien_nuoc WHERE phong='$phong' AND thang='$thang_truoc'";
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
    public function checkPhongThang($phong, $thang, $id = '') {
        $sql = "SELECT id FROM hoa_don_dien_nuoc WHERE phong='$phong' AND thang='$thang'";
        if ($id != '') $sql .= " AND id<>$id";
        return $this->conn->query($sql)->num_rows > 0;
    }

    // Thêm mới
    public function insert($data) {
        extract($data);
        $sql = "INSERT INTO hoa_don_dien_nuoc
                (ma_hd, phong, thang, dien_cu, dien_moi, nuoc_cu, nuoc_moi, so_dien, so_nuoc,
                 tien_phong, tien_dich_vu, tong_tien, ngay_lap, trang_thai)
                VALUES (
                '$mahd', '$phong', '$thang', $dien_cu, $dien_moi, $nuoc_cu, $nuoc_moi, $sodien, $sonuoc,
                $tien_phong, $tien_dich_vu, $tongtien, '$ngaylap', $trangthai)";
        return $this->conn->query($sql);
    }

    // Cập nhật
    public function update($data) {
        extract($data);
        $sql = "UPDATE hoa_don_dien_nuoc SET
                ma_hd='$mahd', phong='$phong', thang='$thang', 
                so_dien=$sodien, so_nuoc=$sonuoc, tien_phong=$tien_phong, tien_dich_vu=$tien_dich_vu,
                tong_tien=$tongtien, ngay_lap='$ngaylap', trang_thai=$trangthai
                WHERE id=$id";
        return $this->conn->query($sql);
    }

    // Xóa
    public function delete($id) {
        $id = (int)$id;
        return $this->conn->query("DELETE FROM hoa_don_dien_nuoc WHERE id=$id");
    }
}
?>