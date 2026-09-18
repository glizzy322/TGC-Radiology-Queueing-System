<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class AdvertisementRepository
{
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAllActive(): array
    {
        $stmt = $this->db->query("SELECT * FROM advertisements WHERE is_active = 1 ORDER BY display_order ASC, id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM advertisements ORDER BY display_order ASC, id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(string $filepath, int $durationSeconds, bool $isActive, int $displayOrder = 0, bool $isLive = false): array
    {
        $stmt = $this->db->prepare("INSERT INTO advertisements (filepath, duration_seconds, is_active, display_order, is_live) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$filepath, $durationSeconds, $isActive ? 1 : 0, $displayOrder, $isLive ? 1 : 0]);
        $id = $this->db->lastInsertId();
        
        $get = $this->db->prepare("SELECT * FROM advertisements WHERE id = ?");
        $get->execute([$id]);
        return $get->fetch(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM advertisements WHERE id = ?");
        $stmt->execute([$id]);
        $ad = $stmt->fetch(PDO::FETCH_ASSOC);
        return $ad ?: null;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM advertisements WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
