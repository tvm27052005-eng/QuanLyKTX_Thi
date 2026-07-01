<?php
class SinhVienModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($keyword = '') {
        $where = "1";
        if (!empty($keyword)) {
            $kw = $this->conn->real_escape_string($keyword);
            $where .= " AND (sv.ma_sv LIKE '%$kw%' OR sv.ho_ten LIKE '%$kw%')";
        }
        $sql = "SELECT sv.*, p.ma_phong 
                FROM sinh_vien sv 
                LEFT JOIN phong p ON sv.phong_id = p.id 
                WHERE $where ORDER BY sv.id DESC";
        $result = $this->conn->query($sql);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) { $data[] = $row; }
        }
        return $data;
    }

    // TÌM PHÒNG TRỐNG THEO GIỚI TÍNH
    public function getAvailableRooms($gioi_tinh) {
        $gt = $this->conn->real_escape_string($gioi_tinh);
        $sql = "SELECT id, ma_phong, so_nguoi_hien_tai, so_nguoi_toi_da 
                FROM phong 
                WHERE loai_phong LIKE '%$gt%' AND trang_thai = 'hoat_dong' AND so_nguoi_hien_tai < so_nguoi_toi_da";
        $result = $this->conn->query($sql);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) { $data[] = $row; }
        }
        return $data;
    }

    public function updateSync($ma_sv, $data) {
    $this->conn->begin_transaction();
    try {
        // 1. Lấy id phòng cũ của sinh viên
        $stmt_old = $this->conn->prepare("SELECT phong_id FROM sinh_vien WHERE ma_sv = ?");
        $stmt_old->bind_param("s", $ma_sv);
        $stmt_old->execute();
        $result_old = $stmt_old->get_result();
        $phong_id_cu = $result_old->fetch_assoc()['phong_id'] ?? null;

        $phong_id_moi = !empty($data['phong_id']) ? (int)$data['phong_id'] : null;

        // 2. Cập nhật sĩ số bảng Phòng nếu có sự thay đổi phòng
        if ($phong_id_cu != $phong_id_moi) {
            if ($phong_id_cu) {
                $this->conn->query("UPDATE phong SET so_nguoi_hien_tai = so_nguoi_hien_tai - 1 WHERE id = $phong_id_cu");
            }
            if ($phong_id_moi) {
                $this->conn->query("UPDATE phong SET so_nguoi_hien_tai = so_nguoi_hien_tai + 1 WHERE id = $phong_id_moi");
            }
        }

        // 3. Cập nhật bảng Sinh viên (Sửa lại bind_param cho phong_id)
        $stmt1 = $this->conn->prepare("UPDATE sinh_vien SET ho_ten=?, ngay_sinh=?, gioi_tinh=?, lop=?, sdt=?, dia_chi=?, phong_id=? WHERE ma_sv=?");
        $stmt1->bind_param("ssssssis", $data['ho_ten'], $data['ngay_sinh'], $data['gioi_tinh'], $data['lop'], $data['sdt'], $data['dia_chi'], $phong_id_moi, $ma_sv);
        $stmt1->execute();

        // 4. Cập nhật bảng Hợp đồng (ĐÃ SỬA TÊN CỘT THEO FILE .SQL)
        // ten_sinh_vien -> ho_ten | so_dien_thoai -> sdt | ma_sinh_vien -> ma_sv
        $stmt2 = $this->conn->prepare("UPDATE hop_dong SET ho_ten=?, ngay_sinh=?, gioi_tinh=?, lop=?, sdt=?, dia_chi=? WHERE ma_sv=?");
        $stmt2->bind_param("sssssss", $data['ho_ten'], $data['ngay_sinh'], $data['gioi_tinh'], $data['lop'], $data['sdt'], $data['dia_chi'], $ma_sv);
        $stmt2->execute();

        $this->conn->commit();
        return true;
    } catch (Exception $e) {
        $this->conn->rollback();
        // Bạn có thể dùng error_log($e->getMessage()) để xem lỗi cụ thể trong file log của XAMPP
        return false;
    }
}

    // XÓA SINH VIÊN VÀ TRẢ LẠI GIƯỜNG TRỐNG
    public function deleteFull($ma_sv) {
        $this->conn->begin_transaction();
        try {
            // Rút số người khỏi phòng hiện tại
            $stmt_old = $this->conn->prepare("SELECT phong_id FROM sinh_vien WHERE ma_sv = ?");
            $stmt_old->bind_param("s", $ma_sv);
            $stmt_old->execute();
            $phong_id = $stmt_old->get_result()->fetch_assoc()['phong_id'] ?? null;

            if ($phong_id) {
                $this->conn->query("UPDATE phong SET so_nguoi_hien_tai = so_nguoi_hien_tai - 1 WHERE id = $phong_id");
            }

            $this->conn->query("DELETE FROM hop_dong WHERE ma_sv = '$ma_sv'");
            $this->conn->query("DELETE FROM sinh_vien WHERE ma_sv = '$ma_sv'");
            
            // THÊM ĐOẠN NÀY ĐỂ BẮT LỖI TC_05: Kiểm tra xem có thực sự xóa được dòng nào không
            if ($this->conn->affected_rows == 0) {
                $this->conn->rollback();
                return false;
            }
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
    // LẤY 1 SINH VIÊN THEO MÃ SV (PHỤC VỤ TRANG SINH VIÊN)
public function getByMaSV($ma_sv) {
    $stmt = $this->conn->prepare("
        SELECT sv.*, p.ma_phong 
        FROM sinh_vien sv
        LEFT JOIN phong p ON sv.phong_id = p.id
        WHERE sv.ma_sv = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $ma_sv);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
}
?>