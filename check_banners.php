<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Website\TinTuc\Models\BannerModel;

$model = new BannerModel();
$banners = $model->getAllBanners();

echo "Total banners: " . count($banners) . "\n\n";

foreach ($banners as $b) {
    echo "ID: {$b['id']}\n";
    echo "hinh_banner: '" . ($b['hinh_banner'] ?? 'NULL') . "'\n";
    echo "mo_ta: '" . ($b['mo_ta'] ?? 'NULL') . "'\n";
    echo "lien_ket: '" . ($b['lien_ket'] ?? 'NULL') . "'\n";
    echo "trang_thai: '" . ($b['trang_thai'] ?? 'NULL') . "'\n";
    echo "ngay_tao: '" . ($b['ngay_tao'] ?? 'NULL') . "'\n";
    echo "---\n";
}
?>