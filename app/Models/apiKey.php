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

    public static function isMasterKey(string $key, Connection $db): bool
    {
        return $db->createQueryBuilder()
            ->select('1')
            ->from('api_keys')
            ->where('api_key = :api_key')
            ->andWhere('role = :role')
            ->setParameter('api_key', $key)
            ->setParameter('role', 'master')
            ->fetchOne() !== false;
    }

    public static function fetchAll(Connection $db): array
    {
        return $db->fetchAllAssociative('SELECT id, name, LEFT(api_key, 8) as api_key_preview, role, created_at FROM api_keys ORDER BY id DESC');
    }

    public static function create(Connection $db, array $data): bool
    {
        return $db->insert('api_keys', [
            'name' => $data['name'],
            'api_key' => $data['api_key']
        ]) > 0;
    }
}