<?php

namespace App\Controllers;

use App\Models\Skill;
use App\Models\SkillCategory;
use Doctrine\DBAL\Connection;
use Rakit\Validation\Validator;
use Twig\Environment;

class SkillAdminController extends AdminController
{
    // Affiche la liste des compétences
    public function list(): void
    {
        $this->checkAuth();
        $skills = Skill::fetchAll($this->db);
        echo $this->twig->render('admin/skills/list.html.twig', ['skills' => $skills]);
    }
    
    public function showCreateForm(): void
    {
        $this->checkAuth();
        
        // On récupère toutes les catégories disponibles
        $categories = SkillCategory::fetchAll($this->db);
        
        echo $this->twig->render('admin/skills/form.html.twig', [
            'form_title' => 'Ajouter une compétence',
            'categories' => $categories // <-- On les passe au template
        ]);
    }

    // Gère la création
    public function handleCreateForm(): void
    {
        $this->checkAuth();
        $validator = new Validator;
        $validation = $validator->validate($_POST, [
            'name'     => 'required',
            'category' => 'required'
        ]);

        if ($validation->fails()) {
            echo $this->twig->render('admin/skills/form.html.twig', [
                'form_title' => 'Ajouter une compétence',
                'errors' => $validation->errors()->firstOfAll(),
                'old' => $_POST
            ]);
            return;
        }
        
        Skill::create($this->db, $validation->getValidatedData());
        $_SESSION['flash_message'] = 'Compétence ajoutée avec succès !';
        header('Location: /admin/skills');
        exit();
    }
}