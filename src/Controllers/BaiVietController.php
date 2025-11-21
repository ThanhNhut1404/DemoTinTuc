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
        // Handle approval POST (from fragment forms)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['sub']) && $_GET['sub'] === 'duyet_action') {
            $id = $_POST['id'] ?? null;
            $actionType = $_POST['action_type'] ?? '';
            if ($id) {
                if ($actionType === 'approve') {
                    $this->model->updateStatus($id, 'Da_dang');
                    $_SESSION['flash'] = "Đã duyệt bài viết #{$id}.";
                } elseif ($actionType === 'reject') {
                    $this->model->updateStatus($id, 'Tu_choi');
                    $_SESSION['flash'] = "Đã từ chối bài viết #{$id}.";
                }
            }
            header('Location: admin.php?action=bai_viet&sub=duyet');
            exit;
        }

        // If viewing the approval subpage, only load pending posts
        if (isset($_GET['sub']) && $_GET['sub'] === 'duyet') {
            $baiviets = $this->model->getPending();
        } else {
            $baiviets = $this->model->all();
        }
        // Render inside admin layout so it appears in the content frame
        $_GET['sub'] = $_GET['sub'] ?? 'danhsach';
        // ensure layout uses the bai_viet fragment
        $_GET['action'] = 'bai_viet';
        include __DIR__ . '/../../views/backend/layout.php';
    }

    public function create()
    {
        // render 'them' fragment inside admin layout
        $_GET['sub'] = 'them';
        // ensure layout chooses bai_viet
        $_GET['action'] = 'bai_viet';
        include __DIR__ . '/../../views/backend/layout.php';
    }

    public function store()
    {
        $data = [
            'tieu_de' => $_POST['tieu_de'] ?? '',
            'mo_ta_ngan' => $_POST['mo_ta_ngan'] ?? '',
            'noi_dung' => $_POST['noi_dung'] ?? '',
            'anh_dai_dien' => $_FILES['anh_dai_dien']['name'] ?? '',
            'id_chuyen_muc' => $_POST['id_chuyen_muc'] ?? 0,
            'la_noi_bat' => isset($_POST['la_noi_bat']) ? 1 : 0,
            'trang_thai' => $_POST['trang_thai'] ?? 'nhap',
            'ngay_dang' => $_POST['ngay_dang'] ?? date('Y-m-d H:i:s'),
            'id_tac_gia' => $_SESSION['user_id'] ?? null,
        ];

        if (!empty($_FILES['anh_dai_dien']['name'])) {
            $fileName = basename($_FILES['anh_dai_dien']['name']);
            // sanitize filename (replace unsafe chars)
            $fileName = preg_replace('/[^A-Za-z0-9_.-]/u', '_', $fileName);
            $uploadDir = __DIR__ . '/../../public/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            move_uploaded_file($_FILES['anh_dai_dien']['tmp_name'], $uploadDir . $fileName);
            // update data with sanitized name
            $data['anh_dai_dien'] = $fileName;
        }

        $this->model->create($data);
        header('Location: admin.php?action=bai_viet');
    }

    public function edit($id)
    {
        $baiviet = $this->model->find($id);
        // render 'sua' fragment inside admin layout
        $_GET['sub'] = 'sua';
        // make layout include the bai_viet fragment which will load the 'sua' subfragment
        $_GET['action'] = 'bai_viet';
        include __DIR__ . '/../../views/backend/layout.php';
    }

    public function update($id)
    {
        // map POST to expected DB columns
        $fileName = $_FILES['anh_dai_dien']['name'] ?? '';
            if (!empty($fileName)) {
                $fileName = basename($fileName);
                $fileName = preg_replace('/[^A-Za-z0-9_.-]/u', '_', $fileName);
                $uploadDir = __DIR__ . '/../../public/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                move_uploaded_file($_FILES['anh_dai_dien']['tmp_name'], $uploadDir . $fileName);
        } else {
            $fileName = $_POST['existing_anh'] ?? '';
        }

        $updateData = [
            'tieu_de' => $_POST['tieu_de'] ?? '',
            'mo_ta_ngan' => $_POST['mo_ta_ngan'] ?? '',
            'noi_dung' => $_POST['noi_dung'] ?? '',
                'anh_dai_dien' => $fileName,
            'id_chuyen_muc' => $_POST['id_chuyen_muc'] ?? 0,
            'la_noi_bat' => isset($_POST['la_noi_bat']) ? 1 : 0,
            'trang_thai' => $_POST['trang_thai'] ?? 'nhap',
            'ngay_dang' => $_POST['ngay_dang'] ?? date('Y-m-d H:i:s'),
            'id_tac_gia' => $_SESSION['user_id'] ?? null,
        ];

        $this->model->update($id, $updateData);
        header('Location: admin.php?action=bai_viet');
    }

    public function delete($id)
    {
        $this->model->delete($id);
        header('Location: admin.php?action=bai_viet');
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
