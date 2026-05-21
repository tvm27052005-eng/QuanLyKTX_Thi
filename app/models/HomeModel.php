<?php
class HomeModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getDashboardStats() {
        $stats = [
            'tong_phong' => 0,
            'sinh_vien' => 0,
            'su_co' => 0,
            'doanh_thu' => 0,
            'hoa_don_no' => []
        ];

        // 1. Đếm tổng số phòng trên hệ thống
        $rs1 = $this->conn->query("SELECT COUNT(id) as total FROM phong");
        if ($rs1) $stats['tong_phong'] = $rs1->fetch_assoc()['total'];

        // 2. Đếm sinh viên đang có hợp đồng hiệu lực
        $rs2 = $this->conn->query("SELECT COUNT(id) as total FROM sinh_vien WHERE trang_thai_hop_dong = 'Hieu_luc'");
        if ($rs2) $stats['sinh_vien'] = $rs2->fetch_assoc()['total'];

        // 3. Đếm sự cố đang chờ xử lý
        $rs3 = $this->conn->query("SELECT COUNT(id) as total FROM su_co WHERE trang_thai = 'cho_xu_ly'");
        if ($rs3) $stats['su_co'] = $rs3->fetch_assoc()['total'];

        // 4. Tính tổng doanh thu hóa đơn ĐÃ THU trong tháng hiện tại
        $thang_hien_tai = date('Y-m'); // VD: 2026-04
        $rs4 = $this->conn->query("SELECT SUM(tong_tien) as total FROM hoa_don_dien_nuoc WHERE trang_thai = 1 AND thang_nam = '$thang_hien_tai'");
        if ($rs4) $stats['doanh_thu'] = $rs4->fetch_assoc()['total'] ?? 0;

        // 5. Lấy danh sách 5 hóa đơn CHƯA THANH TOÁN gần nhất
        $sql_hd = "SELECT hd.thang_nam, hd.tong_tien, p.ma_phong 
                   FROM hoa_don_dien_nuoc hd 
                   LEFT JOIN phong p ON hd.phong_id = p.id 
                   WHERE hd.trang_thai = 0 
                   ORDER BY hd.id DESC LIMIT 5";
        $rs5 = $this->conn->query($sql_hd);
        if ($rs5 && $rs5->num_rows > 0) {
            while ($row = $rs5->fetch_assoc()) {
                $stats['hoa_don_no'][] = $row;
            }
        }

        return $stats;
    }
}
?>