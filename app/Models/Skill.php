<?php

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
    public static function create(Connection $db, array $data): bool
    {
        return $db->insert('skills', [
            'name' => $data['name'],
            'category' => $data['category']
        ]) > 0;
    }
}