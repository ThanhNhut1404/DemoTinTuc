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
        // Chuẩn bị mảng 4 quảng cáo dùng cho view (nếu ít hơn 4 sẽ lặp lại)
        $allAds = array_values(array_filter(array_merge($quangCaoTrai, $quangCaoPhai)));
        $ads = [];
        if (!empty($allAds)) {
            $take = array_slice($allAds, 0, 4);
            while (count($take) < 4) {
                $take = array_merge($take, $allAds);
                $take = array_slice($take, 0, 4);
            }
            $ads = $take;
        }
        // Lấy tin tức
        $baiVietModel = new BaiVietModel();
        $tinMoiNhat = $baiVietModel->getTinMoiNhat(6);
        // Lấy tối đa 5 bài nổi bật. Nếu DB chưa có đủ (ví dụ chỉ 1 bài được gắn la_noi_bat=1),
        // bổ sung bằng các bài mới nhất để luôn hiển thị 5 item trong Top 5.
        $tinNoiBat = $baiVietModel->getTinNoiBat(5);
        if (count($tinNoiBat) < 5) {
            $needed = 5 - count($tinNoiBat);
            // Lấy nhiều hơn một chút để tránh trùng lặp nếu có
            $candidates = $baiVietModel->getTinMoiNhat($needed + 5);
            // Lọc trùng theo id
            $existingIds = array_column($tinNoiBat, 'id');
            foreach ($candidates as $c) {
                if (count($tinNoiBat) >= 5) break;
                if (in_array($c['id'], $existingIds)) continue;
                $tinNoiBat[] = $c;
                $existingIds[] = $c['id'];
            }
        }
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
