<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../app/config/database.php';

$db = (new Database())->getConnection();
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'get_tien_phong_dv':
        $phong = isset($_GET['phong']) ? $db->real_escape_string($_GET['phong']) : '';
        $tien_phong = 0; $tien_dich_vu = 0;

        // Lấy tiền phòng
        $rs = $db->query("SELECT gia_tien FROM phong WHERE ten_phong='$phong'");
        if ($rs && $rs->num_rows > 0) $tien_phong = (int)$rs->fetch_assoc()['gia_tien'];

        // Lấy tiền dịch vụ
        $rs2 = $db->query("SELECT SUM(s.price) AS total FROM rooms r JOIN room_services rs ON r.id = rs.room_id JOIN services s ON rs.service_id = s.id WHERE r.room_name = '$phong'");
        if ($rs2 && $rs2->num_rows > 0) $tien_dich_vu = (int)($rs2->fetch_assoc()['total']);

        echo json_encode(['tien_phong' => $tien_phong, 'tien_dich_vu' => $tien_dich_vu]);
        break;

    case 'get_chi_so_cu':
        // Đưa logic file chi_so_dien_nuoc_cu.php cũ vào đây...
        break;
        
    default:
        echo json_encode(['error' => 'Action không tồn tại']);
}
?>