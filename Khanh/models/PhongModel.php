<?php
class PhongModel {
    private $conn;
    public function __construct($db) { $this->conn = $db; }

    public function getAll($keyword = '') {
        $where = "1";
        if ($keyword) {
            $kw = $this->conn->real_escape_string($keyword);
            $where .= " AND (ma_phong LIKE '%$kw%' OR loai_phong LIKE '%$kw%' OR trang_thai LIKE '%$kw%')";
        }
        $result = $this->conn->query("SELECT * FROM phong WHERE $where ORDER BY id DESC");
        $data = [];
        while ($row = $result->fetch_assoc()) $data[] = $row;
        return $data;
    }
}
?>