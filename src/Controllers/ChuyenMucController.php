<?php

namespace Website\TinTuc\Controllers;

use Website\TinTuc\Models\BaiVietModel;
use Website\TinTuc\Models\ChuyenMucModel;

class ChuyenMucController
{
    private $baiVietModel;
    private $chuyenMucModel;

    public function __construct()
    {
        $this->baiVietModel = new BaiVietModel();
        $this->chuyenMucModel = new ChuyenMucModel();
    }

    // --- Hiển thị tất cả bài viết trong chuyên mục (bản gốc không AJAX)
    public function hienThiTheoChuyenMuc($id)
    {
        try {
            $cmInfo = $this->chuyenMucModel->getById($id);
            $tinTheoChuyenMuc = $this->baiVietModel->getTinTheoChuyenMuc($id);
        } catch (\PDOException $e) {
            error_log("Lỗi lấy dữ liệu chuyên mục: " . $e->getMessage());
            $cmInfo = [];
            $tinTheoChuyenMuc = [];
        }

        include __DIR__ . '/../../views/frontend/trang_chu.php';
    }

    // --- Bản AJAX cũ (không phân trang)
    public function ajaxLoadChuyenMuc($id)
    {
        header('Content-Type: text/html; charset=utf-8');

        try {
            $cmInfo = $this->chuyenMucModel->getById($id);
            $tinTheoChuyenMuc = $this->baiVietModel->getTinTheoChuyenMuc($id);
        } catch (\PDOException $e) {
            error_log("Lỗi AJAX chuyên mục: " . $e->getMessage());
            $cmInfo = [];
            $tinTheoChuyenMuc = [];
        }

        if (empty($tinTheoChuyenMuc)) {
            echo "<div class='section'><h2>📰 Bài viết thuộc chuyên mục: " . htmlspecialchars($cmInfo['ten_chuyen_muc'] ?? 'Không xác định') . "</h2>";
            echo "<p>Chưa có bài viết nào trong chuyên mục này.</p></div>";
            return;
        }

        echo "<div class='section'><h2>📰 Bài viết thuộc chuyên mục: " . htmlspecialchars($cmInfo['ten_chuyen_muc']) . "</h2>";
        foreach ($tinTheoChuyenMuc as $tin) {
            echo '<div class="tin">';
            echo '<img src="' . htmlspecialchars($tin['anh_dai_dien']) . '" alt="">';
            echo '<div>';
            echo '<b>' . htmlspecialchars($tin['tieu_de']) . '</b>';
            echo '<small>Ngày đăng: ' . htmlspecialchars($tin['ngay_dang']) . '</small>';
            echo '<p>' . htmlspecialchars(mb_strimwidth($tin['mo_ta_ngan'] ?? '', 0, 100, "...")) . '</p>';
            echo '</div>';
            echo '</div>';
        }
        echo "</div>";
    }

    // --- Bản AJAX có phân trang (mới)
public function loadChuyenMuc()
{
    $id = $_GET['id'] ?? 0;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 5;
    $offset = ($page - 1) * $limit;

    $baiVietModel = new \Website\TinTuc\Models\BaiVietModel();
    $chuyenMucModel = new \Website\TinTuc\Models\ChuyenMucModel();

    try {
        $cmInfo = $chuyenMucModel->getById($id);
        $total = $baiVietModel->countByChuyenMuc($id);
        $totalPages = ceil($total / $limit);
        $tinTheoChuyenMuc = $baiVietModel->getByChuyenMuc($id, $limit, $offset);
    } catch (\PDOException $e) {
        error_log("Lỗi load chuyên mục: " . $e->getMessage());
        $cmInfo = [];
        $tinTheoChuyenMuc = [];
        $totalPages = 1;
        $page = 1;
    }

    include __DIR__ . '/../../views/frontend/chuyen_muc_ajax.php';
}

}
