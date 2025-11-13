<?php

namespace Website\TinTuc\Controllers;

use Website\TinTuc\Models\BaiVietModel;
use Website\TinTuc\Models\ChuyenMucModel;
class BaiVietController
{
    private $model;

    public function __construct()
    {
        $this->model = new BaiVietModel();
    }

    public function index()
    {
        $baiviets = $this->model->all();
        include __DIR__ . '/../../views/backend/danhsach_baiviet.php';
    }

    public function create()
    {
        include __DIR__ . '/../../views/backend/them_baiviet.php';
    }

    public function store()
    {
        $data = [
            'tieu_de' => $_POST['tieu_de'],
            'mo_ta' => $_POST['mo_ta'],
            'noi_dung' => $_POST['noi_dung'],
            'anh_dai_dien' => $_FILES['anh_dai_dien']['name'] ?? '',
            'id_chuyen_muc' => $_POST['id_chuyen_muc'],
            'tag' => $_POST['tag'],
            'la_noi_bat' => isset($_POST['la_noi_bat']) ? 1 : 0,
            'trang_thai' => $_POST['trang_thai'],
            'ngay_dang' => $_POST['ngay_dang'] ?? date('Y-m-d H:i:s'),
        ];

        if (!empty($_FILES['anh_dai_dien']['name'])) {
            move_uploaded_file($_FILES['anh_dai_dien']['tmp_name'], __DIR__ . '/../../../public/uploads/' . $_FILES['anh_dai_dien']['name']);
        }

        $this->model->create($data);
        header('Location: index.php?action=list');
    }

    public function edit($id)
    {
        $baiviet = $this->model->find($id);
        include __DIR__ . '/../../views/backend/sua_baiviet.php';
    }

    public function update($id)
    {
        $this->model->update($id, $_POST);
        header('Location: index.php?action=list');
    }

    public function delete($id)
    {
        $this->model->delete($id);
        header('Location: index.php?action=list');
    }
     public function chiTiet($id)
    {
        $baiVietModel = new BaiVietModel();
        $chuyenMucModel = new ChuyenMucModel();

        try {
            $baiViet = $baiVietModel->getById($id);
            if (!$baiViet) {
                die("❌ Không tìm thấy bài viết.");
            }

            // Lấy thông tin chuyên mục (nếu cần hiển thị)
            $cm = $chuyenMucModel->getById($baiViet['id_chuyen_muc']);

            // Tăng lượt xem
            $baiVietModel->tangLuotXem($id);

        } catch (\PDOException $e) {
            die("Lỗi khi lấy bài viết: " . $e->getMessage());
        }

        // Gọi giao diện chi tiết
        include __DIR__ . '/../../views/chi_tiet_bai_viet.php';
    }
    public function search()
{
    $q = trim($_GET['q'] ?? '');
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $perPage = 10;
    $offset = ($page - 1) * $perPage;

    $totalResults = 0;
    $results = [];
    if ($q !== '') {
        $totalResults = $this->model->countSearch($q);
        $results = $this->model->search($q, $perPage, $offset);
    }

    // lấy chuyên mục nếu view header cần hiển thị menu
    $chuyenMucModel = new \Website\TinTuc\Models\ChuyenMucModel();
    $chuyenMuc = $chuyenMucModel->getAll();

    // biến cho view: $query, $results, $totalResults, $currentPage, $perPage
    $query = $q;
    $currentPage = $page;
    include __DIR__ . '/../../views/frontend/search.php';
}

public function ajaxSearch()
{
    $q = trim($_GET['q'] ?? '');
    header('Content-Type: application/json; charset=utf-8');
    if ($q === '') {
        echo json_encode([]);
        return;
    }
    $suggestions = $this->model->suggest($q, 7);
    // đảm bảo json safe
    echo json_encode($suggestions, JSON_UNESCAPED_UNICODE);
}
}
