<?php
class ThanhToanModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Tạo yêu cầu thanh toán từ sinh viên
    public function createPaymentRequest($hoa_don_id, $phong_id, $ma_sv, $so_tien) {
        $hoa_don_id = (int)$hoa_don_id;
        $phong_id = (int)$phong_id;
        $so_tien = (int)$so_tien;
        $sql = "INSERT INTO yeu_cau_thanh_toan (hoa_don_id, phong_id, ma_sinh_vien, so_tien, trang_thai, created_at) 
                VALUES ($hoa_don_id, $phong_id, '$ma_sv', $so_tien, 'pending', NOW())";
        return $this->conn->query($sql);
    }

    // Lấy danh sách yêu cầu thanh toán chưa xử lý
    public function getAll($keyword = '') {
        $where = "1";
        if (!empty($keyword)) {
            $kw = $this->conn->real_escape_string($keyword);
            $where .= " AND (p.ma_phong LIKE '%$kw%' OR yct.ma_sinh_vien LIKE '%$kw%')";
        }
        $sql = "SELECT yct.*, p.ma_phong, hd.ma_hd, hd.thang_nam 
                FROM yeu_cau_thanh_toan yct 
                LEFT JOIN phong p ON yct.phong_id = p.id 
                LEFT JOIN hoa_don_dien_nuoc hd ON yct.hoa_don_id = hd.id 
                WHERE $where 
                ORDER BY yct.created_at DESC";
        $result = $this->conn->query($sql);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // Xác nhận đã thanh toán
    public function approve($id) {
        $id = (int)$id;
        $result = $this->conn->query("SELECT hoa_don_id FROM yeu_cau_thanh_toan WHERE id = $id");
        $row = $result->fetch_assoc();
        if (!$row) return false;
        
        $hoa_don_id = $row['hoa_don_id'];
        $this->conn->begin_transaction();
        try {
            $this->conn->query("UPDATE yeu_cau_thanh_toan SET trang_thai = 'approved', updated_at = NOW() WHERE id = $id");
            $this->conn->query("UPDATE hoa_don_dien_nuoc SET trang_thai = 1 WHERE id = $hoa_don_id");
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    // Từ chối thanh toán
    public function reject($id) {
        $id = (int)$id;
        return $this->conn->query("UPDATE yeu_cau_thanh_toan SET trang_thai = 'rejected', updated_at = NOW() WHERE id = $id");
    }

    // Xóa yêu cầu thanh toán
    public function delete($id) {
        $id = (int)$id;
        return $this->conn->query("DELETE FROM yeu_cau_thanh_toan WHERE id = $id");
    }

    // Lấy chi tiết 1 yêu cầu
    public function getById($id) {
        $id = (int)$id;
        $sql = "SELECT yct.*, p.ma_phong, hd.ma_hd, hd.thang_nam 
                FROM yeu_cau_thanh_toan yct 
                LEFT JOIN phong p ON yct.phong_id = p.id 
                LEFT JOIN hoa_don_dien_nuoc hd ON yct.hoa_don_id = hd.id 
                WHERE yct.id = $id";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_assoc() : null;
    }
}
?>
