<?php
namespace Website\TinTuc\Controllers;

use Website\TinTuc\Database;
use Website\TinTuc\Models\TagModel;
use PDO;

class SearchController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function index() {
        $query = trim($_GET['q'] ?? '');
        $currentPage = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 5;
        $offset = ($currentPage - 1) * $perPage;

        $results = [];
        $totalResults = 0;
        $tagSuggestions = []; // posts suggested by matching tag

        if ($query !== '') {
            // Đếm tổng kết quả
            $stmtTotal = $this->conn->prepare("
                SELECT COUNT(*) 
                FROM bai_viet 
                WHERE tieu_de LIKE :q OR noi_dung LIKE :q
            ");
            $stmtTotal->execute(['q' => "%$query%"]);
            $totalResults = (int) $stmtTotal->fetchColumn();

            // Lấy dữ liệu trang hiện tại
                // Select all columns so view can use whichever image column exists
                $stmt = $this->conn->prepare("
                    SELECT *
                    FROM bai_viet 
                    WHERE tieu_de LIKE :q OR noi_dung LIKE :q
                    ORDER BY ngay_dang DESC
                    LIMIT :offset, :perpage
                ");
            $stmt->bindValue(':q', "%$query%", PDO::PARAM_STR);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':perpage', $perPage, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // If the query looks like a tag (matches any tag name), fetch posts that have that tag
        try {
            $tagModel = new TagModel();
            // Normalize tag query: strip leading #/@ and trim
            $tagQuery = preg_replace('/^[#@]+/', '', trim($query));
            $tagQuery = $tagQuery === '' ? $query : $tagQuery;
            // Use case-insensitive comparison
            $stmtTags = $this->conn->prepare("SELECT id, ten_tag FROM the_tag WHERE LOWER(ten_tag) LIKE LOWER(:q) LIMIT 5");
            $stmtTags->execute(['q' => "%$tagQuery%"]);
            $matchingTags = $stmtTags->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($matchingTags)) {
                $tagIds = array_column($matchingTags, 'id');
                // Build placeholders for IN clause
                $placeholders = implode(',', array_fill(0, count($tagIds), '?'));
                // Assume bai_viet uses column `id_the_tag` to reference the_tag.id (common in this project)
                $sql = "SELECT id, tieu_de, mo_ta_ngan, ngay_dang, hinh_anh FROM bai_viet WHERE id_the_tag IN ($placeholders) AND trang_thai = 'da_dang' ORDER BY ngay_dang DESC LIMIT 6";
                $stmtTagPosts = $this->conn->prepare($sql);
                // Bind tag ids
                foreach ($tagIds as $i => $tid) {
                    $stmtTagPosts->bindValue($i+1, (int)$tid, PDO::PARAM_INT);
                }
                $stmtTagPosts->execute();
                // Debug log when tag matches found (non-fatal)
                if (!empty($matchingTags)) {
                    error_log('SearchController: tagQuery="' . $tagQuery . '" matched tags: ' . implode(',', array_column($matchingTags,'ten_tag')));
                }
                $tagSuggestions = $stmtTagPosts->fetchAll(PDO::FETCH_ASSOC);
                // Merge tag-suggested posts into main results (avoid duplicates)
                if (!empty($tagSuggestions)) {
                    $existing = [];
                    foreach ($results as $r) {
                        $existing[(int)$r['id']] = $r;
                    }
                    // Prepend tag suggestions so tag matches are more visible
                    foreach ($tagSuggestions as $tp) {
                        $id = (int)$tp['id'];
                        if (!isset($existing[$id])) {
                            array_unshift($results, $tp);
                            $existing[$id] = $tp;
                        }
                    }
                    // Reindex results
                    $results = array_values($results);
                    // Adjust totalResults conservatively to the number of distinct results currently loaded
                    $totalResults = max($totalResults, count($existing));
                }
            }
        } catch (\Exception $e) {
            // non-fatal: if tag table doesn't exist or column mismatch, ignore suggestions
            error_log('Tag suggestion error: ' . $e->getMessage());
            $tagSuggestions = [];
        }

        // Truyền dữ liệu sang view
        include __DIR__ . '/../../views/frontend/search.php';
    }
    
}
