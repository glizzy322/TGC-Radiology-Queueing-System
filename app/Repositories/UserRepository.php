<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class UserRepository
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByName(string $name): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM staff_users WHERE name = ? LIMIT 1");
        $stmt->execute([$name]);
        $user = $stmt->fetch();
        return $user ?: null;
    }
}
