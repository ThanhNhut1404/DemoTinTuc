<?php
namespace Website\TinTuc\Controllers;

use Website\TinTuc\Database;
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
            $stmt = $this->conn->prepare("
                SELECT id, tieu_de, mo_ta_ngan, ngay_dang 
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

        // Truyền dữ liệu sang view
        include __DIR__ . '/../../views/frontend/search.php';
    }
    
}
