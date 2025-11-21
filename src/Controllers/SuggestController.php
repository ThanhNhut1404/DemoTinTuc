<?php
namespace Website\TinTuc\Controllers;

use Website\TinTuc\Models\SuggestModel;

class SuggestController
{
    public function index()
    {
        header('Content-Type: application/json');

        $keyword = $_GET['q'] ?? '';

        if ($keyword === '') {
            echo json_encode([]);
            return;
        }

        $model = new SuggestModel();
        $results = $model->searchTitles($keyword);

        echo json_encode($results);
    }
}
