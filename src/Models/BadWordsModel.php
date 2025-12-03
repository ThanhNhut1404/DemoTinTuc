<?php
namespace Website\TinTuc\Models;

use Website\TinTuc\Database;
use PDO;

class BadWordsModel {
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getAll()
    {
        $stmt = $this->conn->query("SELECT id, word, created_at, active FROM bad_words ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add(string $tu_khoa)
    {
        $word = trim($tu_khoa);
        if ($word === '') return false;
        $stmt = $this->conn->prepare("INSERT INTO bad_words (word, created_at, active) VALUES (?, NOW(), 1)");
        try {
            return $stmt->execute([$word]);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function delete(int $id)
    {
        $stmt = $this->conn->prepare("DELETE FROM bad_words WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getKeywords(): array
    {
        $stmt = $this->conn->query("SELECT word FROM bad_words WHERE active = 1");
        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return array_filter(array_map('trim', $rows));
    }

    public function find(int $id)
    {
        $stmt = $this->conn->prepare("SELECT id, word, created_at, active FROM bad_words WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update(int $id, string $word, int $active = 1)
    {
        $word = trim($word);
        $active = $active ? 1 : 0;
        $stmt = $this->conn->prepare("UPDATE bad_words SET word = ?, active = ? WHERE id = ?");
        return $stmt->execute([$word, $active, $id]);
    }

    public function copy(int $id)
    {
        $row = $this->find($id);
        if (!$row) return false;
        $stmt = $this->conn->prepare("INSERT INTO bad_words (word, created_at, active) VALUES (?, NOW(), ?)");
        return $stmt->execute([$row['word'], $row['active']]);
    }

    public function toggleActive(int $id)
    {
        $row = $this->find($id);
        if (!$row) return false;
        $new = $row['active'] ? 0 : 1;
        $stmt = $this->conn->prepare("UPDATE bad_words SET active = ? WHERE id = ?");
        return $stmt->execute([$new, $id]);
    }
}
