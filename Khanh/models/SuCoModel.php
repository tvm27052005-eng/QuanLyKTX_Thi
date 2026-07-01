<?php
class SuCoModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy danh sách sự cố có kèm từ khóa tìm kiếm
    public function getAll($keyword = '') {
        $where = "1";
        if (!empty($keyword)) {
            $kw = $this->conn->real_escape_string($keyword);
            $where .= " AND (p.ma_phong LIKE '%$kw%' OR sc.nguoi_bao LIKE '%$kw%' OR sc.noi_dung LIKE '%$kw%')";
        }
        
        $sql = "SELECT sc.*, p.ma_phong 
                FROM su_co sc 
                LEFT JOIN phong p ON sc.phong_id = p.id 
                WHERE $where ORDER BY sc.id DESC";
                
        $result = $this->conn->query($sql);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // Thêm báo cáo mới
    public function insert($phong_id, $nguoi_bao, $noi_dung) {
        $stmt = $this->conn->prepare("INSERT INTO su_co (phong_id, nguoi_bao, noi_dung, trang_thai) VALUES (?, ?, ?, 'cho_xu_ly')");
        $stmt->bind_param("iss", $phong_id, $nguoi_bao, $noi_dung);
        return $stmt->execute();
    }

    // Cập nhật trạng thái
    public function updateStatus($id, $trang_thai) {
        $stmt = $this->conn->prepare("UPDATE su_co SET trang_thai = ? WHERE id = ?");
        $stmt->bind_param("si", $trang_thai, $id);
        return $stmt->execute();
    }

    // Xóa sự cố
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM su_co WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>