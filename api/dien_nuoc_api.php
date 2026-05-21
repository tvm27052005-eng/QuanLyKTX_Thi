<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../app/config/database.php';

$db = (new Database())->getConnection();
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'get_tien_phong_dv':
        $phong_id = isset($_GET['phong_id']) ? (int)$_GET['phong_id'] : 0;
        $tien_phong = 0; $tien_dich_vu = 0;

        // Lấy tiền phòng
        $rs = $db->query("SELECT gia_thue FROM phong WHERE id=$phong_id");
        if ($rs && $rs->num_rows > 0) $tien_phong = (int)$rs->fetch_assoc()['gia_thue'];

        // LẤY TIỀN DỊCH VỤ DỰA THEO CÁC DỊCH VỤ ĐÃ ĐƯỢC GÁN CHO PHÒNG NÀY
        $sql_dv = "SELECT SUM(dv.don_gia) AS total 
                   FROM phong_dich_vu pdv 
                   JOIN dich_vu dv ON pdv.dich_vu_id = dv.id 
                   WHERE pdv.phong_id = $phong_id";
        $rs2 = $db->query($sql_dv);
        if ($rs2 && $rs2->num_rows > 0) {
            $tien_dich_vu = (int)($rs2->fetch_assoc()['total']);
        }

        echo json_encode(['tien_phong' => $tien_phong, 'tien_dich_vu' => $tien_dich_vu]);
        break;

    case 'get_chi_so_cu':
        $phong_id = isset($_GET['phong_id']) ? (int)$_GET['phong_id'] : 0;
        $thang = isset($_GET['thang']) ? $db->real_escape_string($_GET['thang']) : '';
        
        $thang_truoc = date('Y-m', strtotime($thang . '-01 -1 month'));
        $rs = $db->query("SELECT dien_moi, nuoc_moi FROM hoa_don_dien_nuoc WHERE phong_id=$phong_id AND thang_nam='$thang_truoc'");
        
        if ($rs && $rs->num_rows > 0) {
            $row = $rs->fetch_assoc();
            echo json_encode(['dien_cu' => $row['dien_moi'], 'nuoc_cu' => $row['nuoc_moi']]);
        } else {
            echo json_encode(['dien_cu' => 0, 'nuoc_cu' => 0]);
        }
        break;

    case 'tinh_tien':
        $dien_cu = isset($_GET['dien_cu']) ? (int)$_GET['dien_cu'] : 0;
        $dien_moi = isset($_GET['dien_moi']) ? (int)$_GET['dien_moi'] : 0;
        $nuoc_cu = isset($_GET['nuoc_cu']) ? (int)$_GET['nuoc_cu'] : 0;
        $nuoc_moi = isset($_GET['nuoc_moi']) ? (int)$_GET['nuoc_moi'] : 0;
        
        $gia_dien = 3500;
        $gia_nuoc = 15000;
        
        $so_dien = $dien_moi - $dien_cu;
        $so_nuoc = $nuoc_moi - $nuoc_cu;
        
        if ($so_dien < 0 || $so_nuoc < 0) {
            echo json_encode(['error' => 'Chỉ số mới không được nhỏ hơn chỉ số cũ']);
            break;
        }
        
        $tien_dien = $so_dien * $gia_dien;
        $tien_nuoc = $so_nuoc * $gia_nuoc;
        
        echo json_encode(['tien_dien' => $tien_dien, 'tien_nuoc' => $tien_nuoc]);
        break;
        
    default:
        echo json_encode(['error' => 'Action không tồn tại']);
}
?>