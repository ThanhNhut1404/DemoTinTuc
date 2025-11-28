<?php
// File: src/helpers.php - Các hàm tiện ích chung

if (!function_exists('img_url')) {
    /**
     * Chuẩn hóa đường dẫn ảnh từ database
     * Xử lý các trường hợp:
     * - uploads/filename.jpg -> ../uploads/filename.jpg
     * - filename.jpg -> ../uploads/filename.jpg
     * - http://... -> giữ nguyên
     * - /uploads/... -> giữ nguyên
     */
    function img_url($src)
    {
        $src = trim((string)$src);
        if ($src === '') return '../uploads/no_image.png';

        // full URL or protocol-less
        if (preg_match('#^(https?:)?//#i', $src)) return $src;

        // absolute path on domain
        if (strpos($src, '/') === 0) return $src;

        // already contains uploads/ -> just ensure ../uploads/
        if (stripos($src, 'uploads/') !== false) {
            // if it already starts with ../ or / or http, keep it
            if (strpos($src, '../') === 0 || strpos($src, '/') === 0 || preg_match('#^(https?:)?//#i', $src)) {
                return $src;
            }
            // otherwise prefix with ../
            return '../' . ltrim($src, '/');
        }

        // otherwise assume filename -> prefix ../uploads/
        return '../uploads/' . $src;
    }
}
?>
