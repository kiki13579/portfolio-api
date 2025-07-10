<?php
// app/Models/ApiKey.php
namespace App\Models;

use Doctrine\DBAL\Connection;

class ApiKey
{
    public static function isValid(string $key, Connection $db): bool
    {
        $stmt = $db->prepare('SELECT 1 FROM api_keys WHERE api_key = :api_key');
        $stmt->bindValue('api_key', $key);
        $result = $stmt->executeQuery();
        return $result->fetchOne() !== false;
    }
}