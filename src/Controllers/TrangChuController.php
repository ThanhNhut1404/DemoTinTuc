<?php
namespace Website\TinTuc\Controllers;

use Website\TinTuc\Models\BaiVietModel;
use Website\TinTuc\Models\QuangCaoModel;

class TrangChuController {
    private $baiVietModel;
    private $quangCaoModel;

    public function __construct() {
        $this->baiVietModel = new BaiVietModel();
        $this->quangCaoModel = new QuangCaoModel();
    }

    public function index() {
        $tinMoiNhat = $this->baiVietModel->getTinMoiNhat(5);
        $tinNoiBat = $this->baiVietModel->getTinNoiBat(5);
        $tinXemNhieu = $this->baiVietModel->getTinXemNhieu(5);
        $quangCao = $this->quangCaoModel->getQuangCaoTrangChu();

        include __DIR__ . '/../../views/frontend/trang_chu.php';
    }
}
