<?php

namespace Website\TinTuc\Controllers;

use Website\TinTuc\Models\BaiVietModel;

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
}
