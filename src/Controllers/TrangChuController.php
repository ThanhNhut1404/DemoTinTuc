<?php

namespace Website\TinTuc\Controllers;

use Website\TinTuc\Models\ChuyenMucModel;
use Website\TinTuc\Models\BaiVietModel;
use Website\TinTuc\Models\QuangCaoModel;
use Website\TinTuc\Models\BannerModel; // thêm dòng này nếu bạn có BannerModel

class TrangChuController
{
    public function index()
    {
        $baiVietModel = new BaiVietModel();
        // --- Lấy danh mục chuyên mục ---
        $chuyenMucModel = new ChuyenMucModel();
        $chuyenMuc = $chuyenMucModel->getAll();
        // tạo banner model và lấy dữ liệu
        $bannerModel = new BannerModel();
        $banners = $bannerModel->getAllBanners();
        // sử lý quản cáo
        $qcModel = new QuangcaoModel();

        // Lấy quảng cáo hai bên
        $quangCaoTrai = $qcModel->getQuangCaoTheoViTri('Trang_chu');
        $quangCaoPhai = $qcModel->getQuangCaoTheoViTri('Sidebar');
        // Lấy tin tức
        $baiVietModel = new BaiVietModel();
        $tinMoiNhat = $baiVietModel->getTinMoiNhat(6);
        $tinNoiBat = $baiVietModel->getTinNoiBat(5);
        $tinXemNhieu = $baiVietModel->getTinXemNhieu(5);
        // --- Lấy bài viết theo từng chuyên mục (phục vụ phần “📂 Bài viết theo chuyên mục”) ---
        $baiVietTheoChuyenMuc = [];
        foreach ($chuyenMuc as $cm) {
            // Lấy toàn bộ bài viết theo chuyên mục, từ mới đến cũ
            $baiVietTheoChuyenMuc[$cm['id']] = $baiVietModel->getTinTheoChuyenMuc($cm['id']);
        }

        // Biến $banners, $tinMoiNhat... sẽ có sẵn trong view
        include __DIR__ . '/../../views/frontend/trang_chu.php';
    }
}
