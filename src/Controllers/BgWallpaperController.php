<?php
namespace Website\TinTuc\Controllers;

use Website\TinTuc\Models\BgWallpaperModel;

class BgWallpaperController {
    private $model;

    public function __construct() {
        $this->model = new BgWallpaperModel();
    }

    public function index() {
        $wallpapers = $this->model->getAll();
        $_GET['action'] = 'bg_wallpaper';
        include __DIR__ . '/../../views/backend/layout.php';
    }

    public function create() {
        $_GET['action'] = 'bg_wallpaper';
        $_GET['sub'] = 'create';
        include __DIR__ . '/../../views/backend/layout.php';
    }

    public function store() {
        $data = [
            'ten_wallpaper' => $_POST['ten_wallpaper'] ?? '',
            'mo_ta' => $_POST['mo_ta'] ?? '',
            'trang_thai' => (isset($_POST['trang_thai']) && $_POST['trang_thai'] === 'on') ? 'on' : 'off',
            'duong_dan_file' => ''
        ];

        if (!empty($_FILES['anh_nen']['name'])) {
            $fileName = basename($_FILES['anh_nen']['name']);
            $fileName = preg_replace('/[^A-Za-z0-9_.-]/u', '_', $fileName);
            $uploadDir = __DIR__ . '/../../public/uploads/wallpapers/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            
            if (move_uploaded_file($_FILES['anh_nen']['tmp_name'], $uploadDir . $fileName)) {
                $data['duong_dan_file'] = $fileName;
                $this->model->create($data);
            }
        }
        
        header('Location: admin.php?action=bg_wallpaper');
        exit;
    }

    public function edit($id) {
        $wallpaper = $this->model->find($id);
        $_GET['action'] = 'bg_wallpaper';
        $_GET['sub'] = 'edit';
        include __DIR__ . '/../../views/backend/layout.php';
    }

    public function update($id) {
        $existing = $this->model->find($id);
        $fileName = $existing['duong_dan_file'] ?? '';

        if (!empty($_FILES['anh_nen']['name'])) {
            $fileName = basename($_FILES['anh_nen']['name']);
            $fileName = preg_replace('/[^A-Za-z0-9_.-]/u', '_', $fileName);
            $uploadDir = __DIR__ . '/../../public/uploads/wallpapers/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            
            if (!empty($existing['duong_dan_file'])) {
                @unlink($uploadDir . $existing['duong_dan_file']);
            }
            
            move_uploaded_file($_FILES['anh_nen']['tmp_name'], $uploadDir . $fileName);
        }

        $data = [
            'ten_wallpaper' => $_POST['ten_wallpaper'] ?? '',
            'mo_ta' => $_POST['mo_ta'] ?? '',
            'trang_thai' => (isset($_POST['trang_thai']) && $_POST['trang_thai'] === 'on') ? 'on' : 'off',
            'duong_dan_file' => $fileName
        ];

        $this->model->update($id, $data);
        header('Location: admin.php?action=bg_wallpaper&updated=1');
        exit;
    }

    public function delete($id) {
        $wallpaper = $this->model->find($id);
        if ($wallpaper && !empty($wallpaper['duong_dan_file'])) {
            $path = __DIR__ . '/../../public/uploads/wallpapers/' . $wallpaper['duong_dan_file'];
            if (is_file($path)) @unlink($path);
        }
        $this->model->delete($id);
        header('Location: admin.php?action=bg_wallpaper');
        exit;
    }

    public function toggle($id) {
        $this->model->toggle($id);
        header('Location: admin.php?action=bg_wallpaper');
        exit;
    }
}
