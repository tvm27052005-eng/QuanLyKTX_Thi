<?php
require_once 'app/models/PhongModel.php';
class PhongController {
    private $model;
    public function __construct() {
        $db = (new Database())->getConnection();
        $this->model = new PhongModel($db);
    }
    public function index() {
        $keyword = $_GET['keyword'] ?? '';
        $listPhong = $this->model->getAll($keyword);
        require_once 'app/views/phong/index.php';
    }
}
?>