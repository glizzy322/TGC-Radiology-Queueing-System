<?php
namespace App\Services;

use PDO;

final class QueueAccess
{
    public static function check(PDO $db, int $staffId, string $key, int $slot): void
    {
        $limits = ['xray' => 2, 'ultrasound' => 2, 'ctscan' => 1];
        if (!isset($limits[$key]) || $slot < 0 || $slot >= $limits[$key]) {
            throw new \InvalidArgumentException('Invalid procedure or serving slot', 400);
        }
        $stmt = $db->prepare('SELECT role, assigned_procedure, assigned_slot FROM staff_users WHERE id = ?');
        $stmt->execute([$staffId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user || !in_array($user['role'], ['administrator', 'radiology_staff'], true)) {
            throw new \DomainException('Unauthorized', 403);
        }
        if ($user['role'] !== 'administrator' && $user['assigned_procedure'] !== null &&
            ($user['assigned_procedure'] !== $key || (int)$user['assigned_slot'] !== $slot)) {
            throw new \DomainException('This room is not assigned to you', 403);
        }
    }
}
