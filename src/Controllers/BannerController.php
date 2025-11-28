\<?php
namespace Website\TinTuc\Controllers;

use Website\TinTuc\Models\BannerModel;

class BannerController
{
    private $model;

    public function __construct()
    {
        $this->model = new BannerModel();
    }

    public function index()
    {
        $banners = $this->model->all();
        $_GET['action'] = 'banner';
        include __DIR__ . '/../../views/backend/layout.php';
    }

    public function create()
    {
        $_GET['action'] = 'banner';
        $_GET['sub'] = 'create';
        include __DIR__ . '/../../views/backend/layout.php';
    }

    public function store()
    {
        $data = [
            'lien_ket' => $_POST['lien_ket'] ?? '',
            'mo_ta' => $_POST['mo_ta'] ?? '',
            // For enum on/off — accept 'on' else default 'off'
            'trang_thai' => (isset($_POST['trang_thai']) && strtolower(trim($_POST['trang_thai'])) === 'on') ? 'on' : 'off',
            'ngay_tao' => date('Y-m-d H:i:s'),
            'hinh_banner' => '',
        ];

        if (!empty($_FILES['hinh_banner']['name'])) {
            $fileName = basename($_FILES['hinh_banner']['name']);
            $fileName = preg_replace('/[^A-Za-z0-9_.-]/u', '_', $fileName);
            $uploadDir = __DIR__ . '/../../public/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            move_uploaded_file($_FILES['hinh_banner']['tmp_name'], $uploadDir . $fileName);
            $data['hinh_banner'] = $fileName;
        }

        $this->model->create($data);
        header('Location: admin.php?action=banner');
        exit;
    }

    public function edit($id)
    {
        $banner = $this->model->find($id);
        $_GET['action'] = 'banner';
        $_GET['sub'] = 'edit';
        include __DIR__ . '/../../views/backend/layout.php';
    }

    public function update($id)
    {
        $existing = $this->model->find($id);
        $fileName = $existing['hinh_banner'] ?? '';

        if (!empty($_FILES['hinh_banner']['name'])) {
            $fileName = basename($_FILES['hinh_banner']['name']);
            $fileName = preg_replace('/[^A-Za-z0-9_.-]/u', '_', $fileName);
            $uploadDir = __DIR__ . '/../../public/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            move_uploaded_file($_FILES['hinh_banner']['tmp_name'], $uploadDir . $fileName);
        }

        $data = [
            'lien_ket' => $_POST['lien_ket'] ?? '',
            'mo_ta' => $_POST['mo_ta'] ?? '',
            'trang_thai' => (isset($_POST['trang_thai']) && strtolower(trim($_POST['trang_thai'])) === 'on') ? 'on' : 'off',
            'hinh_banner' => $fileName,
        ];

        $ok = $this->model->update($id, $data);
        header('Location: admin.php?action=banner' . ($ok ? '&updated=1' : '&error=1'));
        exit;
    }

    public function delete($id)
    {
        $record = $this->model->find($id);
        if ($record && !empty($record['hinh_banner'])) {
            $path = __DIR__ . '/../../public/uploads/' . $record['hinh_banner'];
            if (is_file($path)) @unlink($path);
        }
        $this->model->delete($id);
        header('Location: admin.php?action=banner');
        exit;
    }

    public function toggle($id)
    {
        $record = $this->model->find($id);
        if (! $record) {
            header('Location: admin.php?action=banner');
            exit;
        }
        $newStatus = (isset($record['trang_thai']) && $record['trang_thai'] === 'on') ? 'off' : 'on';
        $data = [
            'hinh_banner' => $record['hinh_banner'] ?? '',
            'lien_ket' => $record['lien_ket'] ?? '',
            'mo_ta' => $record['mo_ta'] ?? '',
            'trang_thai' => $newStatus,
        ];
        $this->model->update($id, $data);
        header('Location: admin.php?action=banner');
        exit;
    }
}













