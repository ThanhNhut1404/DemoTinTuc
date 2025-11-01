<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Website\TinTuc\Controllers\TrangChuController;

$action = $_GET['action'] ?? 'home';

switch ($action) {
    case 'home':
    default:
        $controller = new TrangChuController();
        $controller->index();
        break;
}
