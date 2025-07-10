<?php
// app/Models/Skill.php
namespace App\Models;

use Doctrine\DBAL\Connection;

class Skill
{
    public static function fetchAll(Connection $db): array
    {
        return $db->createQueryBuilder()
            ->select('id', 'name', 'category')
            ->from('skills')
            ->orderBy('category', 'ASC')
            ->addOrderBy('name', 'ASC')
            ->fetchAllAssociative();
    }
    public static function fetchOne(Connection $db, int $id): array|false
    {
        return $db->createQueryBuilder()
            ->select('id', 'name', 'category')
            ->from('skills')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();
    }
    public static function create(Connection $db, array $data): string|false
    {
        $result = $db->insert('skills', [
            'name' => $data['name'],
            'category' => $data['category']
        ]);
        return $result ? $db->lastInsertId() : false;
    }
    public static function update(Connection $db, int $id, array $data): bool
    {
        $result = $db->update('skills', [
            'name' => $data['name'],
            'category' => $data['category']
        ], ['id' => $id]);

        return $result > 0;
    }

    public static function delete(Connection $db, int $id): bool
    {
        $result = $db->delete('skills', ['id' => $id]);

        return $result > 0;
    }
}