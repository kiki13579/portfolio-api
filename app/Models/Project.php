<?php
// app/Models/Project.php
namespace App\Models;

use DateTime;
use Doctrine\DBAL\Connection;

class Project
{
    public static function fetchAll(Connection $db): array
    {
        return $db->createQueryBuilder()
            ->select('id', 'title', 'description', 'projectUrl', 'createdAt')
            ->from('project')
            ->orderBy('createdAt', 'DESC')
            ->fetchAllAssociative();
    }

    public static function fetchOne(Connection $db, int $id): array|false
    {
        return $db->createQueryBuilder()
            ->select('id', 'title', 'description', 'projectUrl', 'createdAt')
            ->from('project')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();
    }

    public static function create(Connection $db, array $data): string|false
    {
        $data['createdAt'] = (new DateTime())->format('Y-m-d H:i:s');

        $result = $db->insert(
            'project', [
                'title' => $data['title'],
                'description' => $data['description'],
                'projectUrl' => $data['projectUrl'] ?? null,
                'createdAt' => $data['createdAt']
        ]);

        return $result ? $db->lastInsertId() : false;
    }

    public static function update(Connection $db, int $id, array $data): bool
    {
        $result = $db->update(
            'project', [
                'title' => $data['title'],
                'description' => $data['description'],
                'projectUrl' => $data['projectUrl'] ?? null
            ], [
                'id' => $id
            ]);

        return $result > 0;
    }

    public static function delete(Connection $db, int $id): bool
    {
        $result = $db->delete(
            'project', [
                'id' => $id
            ]);
        return $result > 0;
    }
}