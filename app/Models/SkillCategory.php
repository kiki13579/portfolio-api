<?php

namespace App\Models;

use Doctrine\DBAL\Connection;

class SkillCategory
{
    public static function fetchAll(Connection $db): array
    {
        return $db->fetchAllAssociative('SELECT * FROM skill_categories ORDER BY name ASC');
    }
}