<?php
namespace App\Models;

use PDO;

class ApiKey
{
    public static function isValid(string $key, PDO $pdo): bool
    {
        $stmt = $pdo->prepare('SELECT 1 FROM api_keys WHERE api_key = :api_key');
        $stmt->execute(['api_key' => $key]);
        
        return $stmt->fetchColumn() !== false;
    }
}