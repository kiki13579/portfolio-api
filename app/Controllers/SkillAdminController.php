<?php
// app/Controllers/SkillAdminController.php
namespace App\Controllers;

use App\Models\Skill;
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

    // Affiche le formulaire de création
    public function showCreateForm(): void
    {
        $this->checkAuth();
        echo $this->twig->render('admin/skills/form.html.twig', [
            'form_title' => 'Ajouter une compétence'
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
    
    // Les méthodes pour modifier et supprimer seront ajoutées ici...
}