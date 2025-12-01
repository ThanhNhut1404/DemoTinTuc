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
        // the_tag is the correct table name in DB
        $sql = "SELECT * FROM the_tag ORDER BY ten_tag ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT * FROM the_tag WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

