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
    $results = [];

    // 1) title suggestions
    $sql = "
        SELECT tieu_de 
        FROM bai_viet 
        WHERE tieu_de LIKE :kw
        ORDER BY ngay_dang DESC
        LIMIT 8
    ";

    $stmt = $this->conn->prepare($sql);
    // match anywhere in title for more flexible suggestions
    $stmt->execute(["kw" => "%$keyword%"]); 
    $titles = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($titles as $t) {
        $results[] = $t;
    }

    // 2) tag name suggestions (prefixed with # to indicate tag)
    $sqlTag = "SELECT ten_tag FROM the_tag WHERE ten_tag LIKE :kw ORDER BY ten_tag ASC LIMIT 6";
    $stmt2 = $this->conn->prepare($sqlTag);
    $stmt2->execute(["kw" => "%$keyword%"]);
    $tags = $stmt2->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tags as $tag) {
        $pref = '#' . ltrim($tag, '#');
        // avoid duplicates
        if (!in_array($pref, $results, true) && !in_array($tag, $results, true)) {
            $results[] = $pref;
        }
    }

    // return up to 10 suggestions
    return array_slice($results, 0, 10);
}

}
