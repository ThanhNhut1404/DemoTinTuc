<?php

namespace Website\TinTuc\Controllers;

use Website\TinTuc\Models\BaiVietModel;
use Website\TinTuc\Models\ChuyenMucModel;

class ChuyenMucController
{
    public function hienThiTheoChuyenMuc($id)
    {
        $baiVietModel = new BaiVietModel();
        $chuyenMucModel = new ChuyenMucModel();

        try {
            // Lấy thông tin chuyên mục
            $cmInfo = $chuyenMucModel->getById($id);
            // Lấy danh sách bài viết trong chuyên mục
            $tinTheoChuyenMuc = $baiVietModel->getTinTheoChuyenMuc($id);
        } catch (\PDOException $e) {
            error_log("Lỗi lấy dữ liệu chuyên mục: " . $e->getMessage());
            $cmInfo = [];
            $tinTheoChuyenMuc = [];
        }

        include __DIR__ . '/../../views/frontend/trang_chu.php';
    }
    public function ajaxLoadChuyenMuc($id)
    {
        header('Content-Type: text/html; charset=utf-8');

        $baiVietModel = new BaiVietModel();
        $chuyenMucModel = new ChuyenMucModel();

        try {
            $cmInfo = $chuyenMucModel->getById($id);
            $tinTheoChuyenMuc = $baiVietModel->getTinTheoChuyenMuc($id);
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
}
