<?php
namespace Website\TinTuc\Controllers;

use Website\TinTuc\Models\BaiVietModel;
use Website\TinTuc\Models\ChuyenMucModel;

class ChuyenMucController
{
    public function hienThiTheoChuyenMuc($id)
    {
        if (!$id || !is_numeric($id)) {
            die("❌ ID chuyên mục không hợp lệ.");
        }

        $baiVietModel = new BaiVietModel();
        $chuyenMucModel = new ChuyenMucModel();

        // ✅ Lấy thông tin chuyên mục
        $chuyenMuc = $chuyenMucModel->getById($id);
        if (!$chuyenMuc) {
            die("❌ Không tìm thấy chuyên mục này.");
        }

        $tenChuyenMuc = $chuyenMuc['ten_chuyen_muc'];

        // ✅ Bộ lọc (mặc định là 'moi_nhat')
        $filter = $_GET['filter'] ?? 'moi_nhat';

        // ✅ Phân trang
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // ✅ Lấy danh sách bài viết có áp dụng bộ lọc
        $baiViet = $baiVietModel->getByChuyenMucFilter($id, $limit, $offset, $filter);

        // ✅ Tổng số bài viết để tính phân trang
        $total = $baiVietModel->countByChuyenMuc($id);
        $totalPages = ceil($total / $limit);

        // ✅ Load view
        include __DIR__ . '/../../views/frontend/chuyen_muc.php';
    }
}
