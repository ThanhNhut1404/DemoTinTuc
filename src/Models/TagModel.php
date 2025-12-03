<?php
namespace Website\TinTuc\Models;

use Website\TinTuc\Database;
use PDO;

class TagModel extends Database
{
    private $db;

    public function __construct() {
        $this->db = $this->connect();
    }

    public function getAll() {
        // the_tag table: include new fields related_tag and seo_keywords
        $sql = "SELECT id, ten_tag, related_tag, seo_keywords FROM the_tag ORDER BY ten_tag ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT id, ten_tag, related_tag, seo_keywords FROM the_tag WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($ten_tag, $related_tag = null, $seo_keywords = null) {
        $sql = "INSERT INTO the_tag (ten_tag, related_tag, seo_keywords) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$ten_tag, $related_tag, $seo_keywords]);
    }

    public function update($id, $ten_tag, $related_tag = null, $seo_keywords = null) {
        $sql = "UPDATE the_tag SET ten_tag = ?, related_tag = ?, seo_keywords = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$ten_tag, $related_tag, $seo_keywords, $id]);
    }

    public function delete($id) {
        $sql = "DELETE FROM the_tag WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

}