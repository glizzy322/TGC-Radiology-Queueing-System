<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class CategoryRepository
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM patient_categories ORDER BY priority_rank ASC");
        return $stmt->fetchAll();
    }
}
