<?php
namespace App\Models;

use PDO;
use DateTime;

class Project
{
    public static function create(PDO $pdo, array $data): string|false
    {
        $stmt = $pdo->prepare(
            'INSERT INTO project (title, description, projectUrl, createdAt) VALUES (:title, :description, :projectUrl, :createdAt)'
        );

        $success = $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'],
            'projectUrl' => $data['projectUrl'] ?? null,
            'createdAt' => (new DateTime())->format('Y-m-d H:i:s')
        ]);

        return $success ? $pdo->lastInsertId() : false;
    }
    public static function fetchOne(PDO $pdo, int $id): array|false
    {
        $stmt = $pdo->prepare('SELECT id, title, description, projectUrl FROM project WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    public static function fetchAll(PDO $pdo): array
    {
        $stmt = $pdo->query('SELECT id, title, description, projectUrl FROM project ORDER BY createdAt DESC');
        return $stmt->fetchAll();
    }
    public static function update(PDO $pdo, int $id, array $data): bool
    {
        $stmt = $pdo->prepare(
            'UPDATE project SET title = :title, description = :description, projectUrl = :projectUrl WHERE id = :id'
        );

        return $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'description' => $data['description'],
            'projectUrl' => $data['projectUrl'] ?? null
        ]);
    }

    public static function delete(PDO $pdo, int $id): bool
    {
        $stmt = $pdo->prepare('DELETE FROM project WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}