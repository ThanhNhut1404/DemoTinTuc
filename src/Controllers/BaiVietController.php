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

        // Before rendering, publish any scheduled posts whose time has arrived
        // (automatically move from 'Cho_duyet' to 'Da_dang')
        try {
            $publishedCount = $this->model->publishDueScheduled();
            if ($publishedCount > 0) {
                // set a temporary flash message so admin sees auto-publish occurred
                $_SESSION['flash'] = "Đã tự động đăng " . intval($publishedCount) . " bài đã đến hạn.";
            }
        } catch (\Exception $e) {
            // swallow - publishDueScheduled already logs errors
        }

        // If viewing the approval subpage, only load pending posts
        if (isset($_GET['sub']) && $_GET['sub'] === 'duyet') {
            $baiviets = $this->model->getPending();
        } elseif (isset($_GET['sub']) && $_GET['sub'] === 'lich') {
            $baiviets = $this->model->getScheduled();
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
        // Fetch all categories for dropdown
        $chuyenMucModel = new ChuyenMucModel();
        $chuyenMucList = $chuyenMucModel->getAll();
        
        // render 'them' fragment inside admin layout
        $_GET['sub'] = 'them';
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
            'tag' => $_POST['tag'] ?? null,
            'id_chuyen_muc' => $_POST['id_chuyen_muc'] ?? 0,
            'la_noi_bat' => isset($_POST['la_noi_bat']) ? 1 : 0,
            'trang_thai' => $_POST['trang_thai'] ?? 'Nhap',
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
        // Fetch all categories for dropdown
        $chuyenMucModel = new ChuyenMucModel();
        $chuyenMucList = $chuyenMucModel->getAll();
        // render 'sua' fragment inside admin layout
        $_GET['sub'] = 'sua';
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
            'tag' => $_POST['tag'] ?? null,
            'id_chuyen_muc' => $_POST['id_chuyen_muc'] ?? 0,
            'la_noi_bat' => isset($_POST['la_noi_bat']) ? 1 : 0,
            'trang_thai' => $_POST['trang_thai'] ?? 'Nhap',
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
    
    /**
     * Handle image upload from rich text editor (TinyMCE).
     * Expects file input named 'file'. Returns JSON: { location: '/path/to/file' }
     */
    public function uploadImage()
    {
        header('Content-Type: application/json; charset=utf-8');
        if (empty($_FILES['file']['name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'No file uploaded']);
            return;
        }

        $file = $_FILES['file'];
        $fileName = basename($file['name']);
        $fileName = preg_replace('/[^A-Za-z0-9_.-]/u', '_', $fileName);

        // Basic server-side validation
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxBytes = 5 * 1024 * 1024; // 5 MB
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowedMimes, true)) {
            http_response_code(400);
            echo json_encode(['error' => 'Loại file không hợp lệ. Chỉ cho phép JPG, PNG, GIF, WEBP.']);
            return;
        }
        if ($file['size'] > $maxBytes) {
            http_response_code(400);
            echo json_encode(['error' => 'File quá lớn. Kích thước tối đa 5MB.']);
            return;
        }

        // Save into the existing public/uploads/ folder so images are served correctly
        $uploadDir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Avoid overwriting: if exists, append timestamp
        $target = $uploadDir . $fileName;
        if (file_exists($target)) {
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $base = pathinfo($fileName, PATHINFO_FILENAME);
            $fileName = $base . '_' . time() . '.' . $ext;
            $target = $uploadDir . $fileName;
        }

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            http_response_code(500);
            echo json_encode(['error' => 'Không thể lưu file lên server.']);
            return;
        }

        // Build a public URL dynamically so it works regardless of document-root setup.
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        // dirname($_SERVER['SCRIPT_NAME']) points to the current script's directory (e.g. '/DemoTinTuc/public')
        $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
        // If scriptDir is just '/', avoid double slashes when joining
        $pathPrefix = $scriptDir === '' || $scriptDir === '/' ? '' : $scriptDir;
        $publicPath = $pathPrefix . '/uploads/' . $fileName;
        $publicUrl = $scheme . '://' . $host . $publicPath;

        // Also provide multiple keys for compatibility and debugging:
        // - 'url' is used by CKEditor 5 simple upload
        // - 'location' is used by TinyMCE
        // - 'uploaded' and 'fileName' help debugging in various clients
        $url_relative = '/uploads/' . $fileName;
        $url_absolute = $publicUrl;

        $response = [
            // prefer a root-relative URL so the editor can load images regardless of host
            'url' => $url_relative,
            // keep absolute URL for debugging or external use
            'url_absolute' => $url_absolute,
            // TinyMCE compatibility key
            'location' => $url_absolute,
            'uploaded' => 1,
            'fileName' => $fileName,
            'url_relative' => $url_relative,
        ];

        echo json_encode($response);
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
