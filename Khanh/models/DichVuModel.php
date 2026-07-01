<?php
class DichVuModel {
    private $conn;
    public function __construct($db) { $this->conn = $db; }

    // Quản lý Dịch vụ gốc
    public function getAll() {
        $result = $this->conn->query("SELECT * FROM dich_vu ORDER BY id DESC");
        $data = [];
        while($row = $result->fetch_assoc()) $data[] = $row;
        return $data;
    }

    public function insert($ten, $gia) {
        return $this->conn->query("INSERT INTO dich_vu (ten_dich_vu, don_gia) VALUES ('$ten', $gia)");
    }

    public function update($id, $ten, $gia) {
        return $this->conn->query("UPDATE dich_vu SET ten_dich_vu='$ten', don_gia=$gia WHERE id=$id");
    }

    public function delete($id) {
        return $this->conn->query("DELETE FROM dich_vu WHERE id=$id");
    }

    // Lấy các dịch vụ đang được gán cho một phòng
    public function getRoomServices($phong_id) {
        $phong_id = (int)$phong_id;
        $result = $this->conn->query("SELECT dich_vu_id FROM phong_dich_vu WHERE phong_id=$phong_id");
        $data = [];
        while($row = $result->fetch_assoc()) $data[] = $row['dich_vu_id'];
        return $data;
    }

    // Cập nhật lại danh sách dịch vụ của phòng
    public function saveRoomServices($phong_id, $dich_vu_ids) {
        $this->conn->begin_transaction();
        try {
            // Xóa hết dịch vụ cũ của phòng này
            $this->conn->query("DELETE FROM phong_dich_vu WHERE phong_id=$phong_id");
            // Thêm lại các dịch vụ mới được tick chọn
            if (!empty($dich_vu_ids)) {
                foreach ($dich_vu_ids as $dv_id) {
                    $dv_id = (int)$dv_id;
                    $this->conn->query("INSERT INTO phong_dich_vu (phong_id, dich_vu_id) VALUES ($phong_id, $dv_id)");
                }
            }
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
    
    public function existsByName($ten, $excludeId = 0) {
        $ten = $this->conn->real_escape_string(trim($ten));
        $excludeId = (int)$excludeId;

        $sql = "SELECT id FROM dich_vu WHERE LOWER(TRIM(ten_dich_vu)) = LOWER('$ten')";
    if ($excludeId > 0) {
        $sql .= " AND id <> $excludeId";
    }
        $sql .= " LIMIT 1";

         $rs = $this->conn->query($sql);
    return $rs && $rs->num_rows > 0;
}
}
?>