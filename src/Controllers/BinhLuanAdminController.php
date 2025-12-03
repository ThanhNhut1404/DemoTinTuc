<?php
namespace Website\TinTuc\Controllers;

use Website\TinTuc\Models\BinhLuanModel;

class BinhLuanAdminController
{
    private $model;

    public function __construct()
    {
        $this->model = new BinhLuanModel();
    }

    public function index()
    {
        $binhLuans = $this->model->getAllForAdmin();
        $_GET['action'] = 'binh_luan';
        include __DIR__ . '/../../views/backend/layout.php';
    }

    public function toggleStatus($id)
    {
        $record = $this->model->find($id);
        if ($record) {
            $newStatus = ($record['trang_thai'] === 'Hien') ? 'An' : 'Hien';
            $this->model->updateStatus($id, $newStatus);
        }
        header('Location: admin.php?action=binh_luan');
        exit;
    }

    public function delete($id)
    {
        $this->model->delete($id);
        header('Location: admin.php?action=binh_luan');
        exit;
    }
}
?>
