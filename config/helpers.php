<?php
// config/helpers.php

if (!function_exists('base_url')) {
    function base_url($path = '') {
        // Đường dẫn gốc dự án của bạn (thay đúng với tên thư mục)
        $base = '/Demotintuc';
        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset_url')) {
    function asset_url($path = '') {
        return base_url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('redirect')) {
    function redirect($path = '') {
        header("Location: " . base_url($path));
        exit;
    }
}