<?php
namespace Website\TinTuc\Models;

use Website\TinTuc\Database;
use PDO;

class SuggestModel
{
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function searchTitles($keyword)
{
    $sql = "
        SELECT tieu_de 
        FROM bai_viet 
        WHERE tieu_de LIKE :kw
        ORDER BY ngay_dang DESC
        LIMIT 10
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute(["kw" => "$keyword%"]); // CHỈ LỌC TỪ BẮT ĐẦU
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

}
