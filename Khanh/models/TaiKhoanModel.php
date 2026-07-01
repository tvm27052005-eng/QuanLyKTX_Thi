<?php
class TaiKhoanModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả tài khoản
    public function getAll($keyword = '') {
        $where = "1=1";
        if (!empty($keyword)) {
            $kw = $this->conn->real_escape_string($keyword);
            $where .= " AND (ten_dang_nhap LIKE '%$kw%' OR ho_ten LIKE '%$kw%' OR ma_sv_lien_ket LIKE '%$kw%')";
        }
        
        $sql = "SELECT * FROM tai_khoan WHERE $where ORDER BY id DESC";
        $result = $this->conn->query($sql);
        
        $data = [];
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // Lấy chi tiết 1 tài khoản
    public function getById($id) {
        $id = (int)$id;
        $sql = "SELECT * FROM tai_khoan WHERE id = $id LIMIT 1";
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    // Kiểm tra tên đăng nhập đã tồn tại chưa (khi tạo mới hoặc sửa)
    public function checkTenDangNhap($ten_dang_nhap, $exclude_id = 0) {
        $tdn = $this->conn->real_escape_string(trim($ten_dang_nhap));
        $exclude_id = (int)$exclude_id;

        $sql = "SELECT id 
            FROM tai_khoan 
            WHERE LOWER(TRIM(ten_dang_nhap)) = LOWER('$tdn')";
    if ($exclude_id > 0) {
        $sql .= " AND id <> $exclude_id";
    }
    $sql .= " LIMIT 1";

    $result = $this->conn->query($sql);
    return $result && $result->num_rows > 0;
}

public function checkMaSVLienKet($ma_sv_lien_ket, $exclude_id = 0) {
    $ma_sv = trim((string)$ma_sv_lien_ket);
    if ($ma_sv === '') {
        return false; // NULL/empty không coi là trùng
    }

    $ma_sv = $this->conn->real_escape_string($ma_sv);
    $exclude_id = (int)$exclude_id;

    $sql = "SELECT id 
            FROM tai_khoan 
            WHERE TRIM(ma_sv_lien_ket) = '$ma_sv'";
    if ($exclude_id > 0) {
        $sql .= " AND id <> $exclude_id";
    }
    $sql .= " LIMIT 1";

    $result = $this->conn->query($sql);
    return $result && $result->num_rows > 0;
}

    // Tạo tài khoản mới
    public function insert($data) {
        $tdn = $this->conn->real_escape_string($data['ten_dang_nhap']);
        $mk = $this->conn->real_escape_string($data['mat_khau']);
        $ht = $this->conn->real_escape_string($data['ho_ten']);
        $vt = $this->conn->real_escape_string($data['vai_tro']);
        $ma_sv = !empty($data['ma_sv_lien_ket']) ? $this->conn->real_escape_string($data['ma_sv_lien_ket']) : null;
        
        $ma_sv_sql = $ma_sv ? "'$ma_sv'" : "NULL";
        
        $sql = "INSERT INTO tai_khoan (ten_dang_nhap, mat_khau, ho_ten, vai_tro, ma_sv_lien_ket) 
                VALUES ('$tdn', '$mk', '$ht', '$vt', $ma_sv_sql)";
        
        return $this->conn->query($sql);
    }

    // Cập nhật tài khoản
    public function update($id, $data) {
        $id = (int)$id;
        $ht = $this->conn->real_escape_string($data['ho_ten']);
        $vt = $this->conn->real_escape_string($data['vai_tro']);
        $ma_sv = !empty($data['ma_sv_lien_ket']) ? $this->conn->real_escape_string($data['ma_sv_lien_ket']) : null;
        
        $ma_sv_sql = $ma_sv ? "'$ma_sv'" : "NULL";
        
        $sql = "UPDATE tai_khoan SET ho_ten = '$ht', vai_tro = '$vt', ma_sv_lien_ket = $ma_sv_sql WHERE id = $id";
        
        return $this->conn->query($sql);
    }

    // Reset password về mặc định
    public function resetPassword($id, $password_mac_dinh = '123456') {
        $id = (int)$id;
        $mk = $this->conn->real_escape_string($password_mac_dinh);
        $sql = "UPDATE tai_khoan SET mat_khau = '$mk' WHERE id = $id";
        
        return $this->conn->query($sql);
    }

    // Xóa tài khoản
    public function delete($id) {
        $id = (int)$id;
        $sql = "DELETE FROM tai_khoan WHERE id = $id LIMIT 1";
        
        return $this->conn->query($sql);
    }

    // Đổi mật khẩu cho 1 tài khoản (admin sửa)
    public function changePassword($id, $new_password) {
        $id = (int)$id;
        $mk = $this->conn->real_escape_string($new_password);
        $sql = "UPDATE tai_khoan SET mat_khau = '$mk' WHERE id = $id";
        
        return $this->conn->query($sql);
    }
}
?>