<?php
// app/Controllers/SkillController.php
namespace App\Controllers;

use App\Models\Skill;
use App\Views\JsonView;
use Doctrine\DBAL\Connection;

class SkillController
{
    public function __construct(private Connection $db) 
    {
    }

    public function list(): void
    {
        $skills = Skill::fetchAll($this->db);
        JsonView::render($skills);
    }
}