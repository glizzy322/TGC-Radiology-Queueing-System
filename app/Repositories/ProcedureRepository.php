<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class ProcedureRepository
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM procedures ORDER BY id ASC");
        return $stmt->fetchAll();
    }
}
