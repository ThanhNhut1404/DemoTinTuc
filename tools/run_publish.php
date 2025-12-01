<?php
require __DIR__ . '/../vendor/autoload.php';
use Website\TinTuc\Models\BaiVietModel;
try {
    $m = new BaiVietModel();
    $n = $m->publishDueScheduled();
    echo "Published: " . intval($n) . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
