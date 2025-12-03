<?php

namespace Website\TinTuc\Controllers;

use Website\TinTuc\Models\ChuyenMucModel;
use Website\TinTuc\Models\BaiVietModel;
use Website\TinTuc\Models\QuangCaoModel;
use Website\TinTuc\Models\BannerModel; // thêm dòng này nếu bạn có BannerModel
use Website\TinTuc\Models\BgWallpaperModel;

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
        // Hiển thị trên trang chủ chỉ lấy banner đã active
        $banners = $bannerModel->getOnBanners();
        // sử lý quản cáo
        $qcModel = new QuangCaoModel();

        // Lấy chỉ quảng cáo trang chủ (không lấy sidebar)
        $quangCaoTrangChu = $qcModel->getQuangCaoTheoViTri('Trang_chu');
        $quangCaoTrai = $quangCaoTrangChu;
        $quangCaoPhai = $quangCaoTrangChu;

        // Nếu không có banner trong bảng `banner`, fallback dùng quảng cáo vị trí 'Trang_chu'
        // (nhiều project upload ảnh banner vào bảng quang_cao via admin). Map trường
        // 'hinh_anh' -> 'hinh_banner', 'tieu_de' -> 'mo_ta' để view không cần thay đổi.
        if (empty($banners)) {
            $adsForBanner = $qcModel->getQuangCaoTheoViTri('Trang_chu');
            $banners = [];
            foreach ($adsForBanner as $a) {
                $banners[] = [
                    'id' => $a['id'] ?? null,
                    'hinh_banner' => $a['hinh_anh'] ?? ($a['hinh'] ?? ''),
                    'mo_ta' => $a['tieu_de'] ?? ($a['mo_ta'] ?? ''),
                    'lien_ket' => $a['lien_ket'] ?? '#',
                ];
            }
        }
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
        // --- Lấy bài viết theo từng CHUYÊN MỤC CHA (nhóm các chuyên mục con thuộc về cha) ---
        $chuyenMucChaModel = new \Website\TinTuc\Models\ChuyenMucChaModel();
        $chuyenMucModel = new \Website\TinTuc\Models\ChuyenMucModel();

        $parents = $chuyenMucChaModel->getAll();
        $baiVietTheoChuyenMuc = [];
        foreach ($parents as $parent) {
            // Lấy id các chuyên mục con thuộc parent
            $children = $chuyenMucModel->getChildren($parent['id']);
            $childIds = array_map(function($c){ return $c['id']; }, $children);
            // Lấy bài theo danh sách id chuyên mục con
            $baiVietTheoChuyenMuc[$parent['id']] = $baiVietModel->getTinTheoChuyenMucList($childIds, 6);
        }
        // expose parents to view as $chuyenMucCha
        $chuyenMucCha = $parents;

        // Biến $banners, $tinMoiNhat... sẽ có sẵn trong view
        // Lấy wallpaper nền website đang kích hoạt
        $bgWallpaperModel = new BgWallpaperModel();
        $activeWallpaper = $bgWallpaperModel->getActive();
        
        include __DIR__ . '/../../views/frontend/trang_chu.php';
    }
}
