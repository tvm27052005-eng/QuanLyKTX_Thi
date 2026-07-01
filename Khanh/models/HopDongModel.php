<?php
class HopDongModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($keyword = '') {
        $sql = "SELECT * FROM hop_dong";
        if (!empty($keyword)) {
            $kw = $this->conn->real_escape_string($keyword);
            $sql .= " WHERE ma_sv LIKE '%$kw%' OR ho_ten LIKE '%$kw%'";
        }
        $sql .= " ORDER BY id DESC";
        $result = $this->conn->query($sql);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function insert($ma_sv, $ho_ten, $lop, $ngay_sinh, $gioi_tinh, $dia_chi, $sdt, $ngay_vao, $ngay_het_han) {
        $this->conn->begin_transaction();
        try {
            $sql_sv = "INSERT INTO sinh_vien (ma_sv, ho_ten, ngay_sinh, gioi_tinh, lop, sdt, dia_chi, trang_thai_hop_dong) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, 'Hieu_luc') 
                       ON DUPLICATE KEY UPDATE 
                       ho_ten=VALUES(ho_ten), ngay_sinh=VALUES(ngay_sinh), gioi_tinh=VALUES(gioi_tinh), 
                       lop=VALUES(lop), sdt=VALUES(sdt), dia_chi=VALUES(dia_chi)";           
            $stmt_sv = $this->conn->prepare($sql_sv);
            $stmt_sv->bind_param("sssssss", $ma_sv, $ho_ten, $ngay_sinh, $gioi_tinh, $lop, $sdt, $dia_chi);
            $stmt_sv->execute();
            $sql_hd = "INSERT INTO hop_dong (ma_sv, ho_ten, lop, ngay_sinh, gioi_tinh, dia_chi, sdt, ngay_vao, ngay_het_han) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_hd = $this->conn->prepare($sql_hd);
            $stmt_hd->bind_param("sssssssss", $ma_sv, $ho_ten, $lop, $ngay_sinh, $gioi_tinh, $dia_chi, $sdt, $ngay_vao, $ngay_het_han);
            $stmt_hd->execute();
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function update($id, $ma_sv, $ho_ten, $lop, $ngay_sinh, $gioi_tinh, $dia_chi, $sdt, $ngay_vao, $ngay_het_han) {
        $this->conn->begin_transaction();
        try {
            $sql_sv = "UPDATE sinh_vien SET ho_ten=?, ngay_sinh=?, gioi_tinh=?, lop=?, sdt=?, dia_chi=? WHERE ma_sv=?";
            $stmt_sv = $this->conn->prepare($sql_sv);
            $stmt_sv->bind_param("sssssss", $ho_ten, $ngay_sinh, $gioi_tinh, $lop, $sdt, $dia_chi, $ma_sv);
            $stmt_sv->execute();

            $sql_hd = "UPDATE hop_dong SET ma_sv=?, ho_ten=?, lop=?, ngay_sinh=?, gioi_tinh=?, dia_chi=?, sdt=?, ngay_vao=?, ngay_het_han=? WHERE id=?";
            $stmt_hd = $this->conn->prepare($sql_hd);
            $stmt_hd->bind_param("sssssssssi", $ma_sv, $ho_ten, $lop, $ngay_sinh, $gioi_tinh, $dia_chi, $sdt, $ngay_vao, $ngay_het_han, $id);
            $stmt_hd->execute();
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function delete($id) {
        $this->conn->begin_transaction();
        try {
        $stmt_get = $this->conn->prepare("SELECT ma_sv FROM hop_dong WHERE id = ?");
        $stmt_get->bind_param("i", $id);
        $stmt_get->execute();
        $result = $stmt_get->get_result();
        $ma_sv = $result->fetch_assoc()['ma_sv'] ?? null;

        if ($ma_sv) {
            $this->conn->query("UPDATE sinh_vien SET trang_thai_hop_dong = 'Khong_hieu_luc' WHERE ma_sv = '$ma_sv'");
        }

        $stmt_del = $this->conn->prepare("DELETE FROM hop_dong WHERE id = ?");
        $stmt_del->bind_param("i", $id);
        $stmt_del->execute();
        $this->conn->commit();
        return true;
        } catch (Exception $e) {
        $this->conn->rollback();
        return false;
        }
    }
}
?>