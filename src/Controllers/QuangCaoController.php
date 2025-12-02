<?php
namespace Website\TinTuc\Controllers;

use Website\TinTuc\Models\QuangCaoModel;

class QuangCaoController
{
    private $model;

    public function __construct()
    {
        $this->model = new QuangCaoModel();
    }

    public function index()
    {
        $quangCaos = $this->model->all();
        // render inside admin layout
        $_GET['action'] = 'quang_cao';
        include __DIR__ . '/../../views/backend/layout.php';
    }

    public function create()
    {
        $_GET['action'] = 'quang_cao';
        $_GET['sub'] = 'create';
        include __DIR__ . '/../../views/backend/layout.php';
    }

    public function store()
    {
        $data = [
            'tieu_de' => $_POST['tieu_de'] ?? '',
            'lien_ket' => $_POST['lien_ket'] ?? '',
            'vi_tri' => $_POST['vi_tri'] ?? 'Trang_chu',
            'trang_thai' => $_POST['trang_thai'] ?? 'on',
            'ngay_tao' => date('Y-m-d H:i:s'),
            'hinh_anh' => '',
        ];

        // handle upload
        if (!empty($_FILES['hinh_anh']['name'])) {
            $fileName = basename($_FILES['hinh_anh']['name']);
            $fileName = preg_replace('/[^A-Za-z0-9_.-]/u', '_', $fileName);
            $uploadDir = __DIR__ . '/../../public/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            move_uploaded_file($_FILES['hinh_anh']['tmp_name'], $uploadDir . $fileName);
            $data['hinh_anh'] = $fileName;
        }

        $this->model->create($data);
        header('Location: admin.php?action=quang_cao');
        exit;
    }

    public function edit($id)
    {
        $quangcao = $this->model->find($id);
        $_GET['action'] = 'quang_cao';
        $_GET['sub'] = 'edit';
        include __DIR__ . '/../../views/backend/layout.php';
    }

    public function update($id)
    {
        $existing = $this->model->find($id);
        $fileName = $existing['hinh_anh'] ?? '';

        if (!empty($_FILES['hinh_anh']['name'])) {
            $fileName = basename($_FILES['hinh_anh']['name']);
            $fileName = preg_replace('/[^A-Za-z0-9_.-]/u', '_', $fileName);
            $uploadDir = __DIR__ . '/../../public/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            move_uploaded_file($_FILES['hinh_anh']['tmp_name'], $uploadDir . $fileName);
        }

        $data = [
            'tieu_de' => $_POST['tieu_de'] ?? '',
            'lien_ket' => $_POST['lien_ket'] ?? '',
            'vi_tri' => $_POST['vi_tri'] ?? 'Trang_chu',
            'trang_thai' => $_POST['trang_thai'] ?? 'on',
            'hinh_anh' => $fileName,
        ];

        $this->model->update($id, $data);
        header('Location: admin.php?action=quang_cao');
        exit;
    }

    public function delete($id)
    {
        $record = $this->model->find($id);
        if ($record && !empty($record['hinh_anh'])) {
            $path = __DIR__ . '/../../public/uploads/' . $record['hinh_anh'];
            if (is_file($path)) @unlink($path);
        }
        $this->model->delete($id);
        header('Location: admin.php?action=quang_cao');
        exit;
    }

    public function toggleStatus($id)
    {
        $record = $this->model->find($id);
        if ($record) {
            $newStatus = ($record['trang_thai'] === 'on') ? 'off' : 'on';
            $this->model->updateStatus($id, $newStatus);
        }
        header('Location: admin.php?action=quang_cao');
        exit;
    }
}
