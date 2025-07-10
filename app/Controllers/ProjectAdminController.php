<?php
// app/Controllers/ProjectAdminController.php
namespace App\Controllers;

use App\Models\Project;
use Rakit\Validation\Validator;

class ProjectAdminController extends AdminController // <-- Il hérite de AdminController
{
    /**
     * Affiche la liste des projets.
     */
    public function list(): void
    {
        $this->checkAuth();
        $projects = Project::fetchAll($this->db);
        echo $this->twig->render('admin/projects/list.html.twig', [
            'projects' => $projects
        ]);
    }

    /**
     * Affiche le formulaire (soit vide pour créer, soit pré-rempli pour modifier).
     */
    public function showForm(array $vars = []): void
    {
        $this->checkAuth();
        $id = $vars['id'] ?? null;
        $projectData = [];

        if ($id) {
            $projectData = Project::fetchOne($this->db, (int)$id);
            if (!$projectData) {
                // Si le projet n'existe pas, on redirige
                header('Location: /admin/projects');
                exit();
            }
        }

        echo $this->twig->render('admin/projects/form.html.twig', [
            'project' => $projectData,
            'form_title' => $id ? 'Modifier le projet' : 'Créer un nouveau projet'
        ]);
    }

    /**
     * Traite la soumission du formulaire (création et modification).
     */
    public function saveForm(array $vars = []): void
    {
        $this->checkAuth();
        $id = $vars['id'] ?? null;

        $validator = new Validator;
        $validation = $validator->validate($_POST, [
            'title'       => 'required|min:5',
            'description' => 'required|min:10',
            'projectUrl'  => 'url'
        ]);

        if ($validation->fails()) {
            // Si la validation échoue, on ré-affiche le formulaire avec les erreurs
            echo $this->twig->render('admin/projects/form.html.twig', [
                'errors' => $validation->errors()->firstOfAll(),
                'old' => $_POST,
                'form_title' => $id ? 'Modifier le projet' : 'Créer un nouveau projet'
            ]);
            return;
        }

        $validatedData = $validation->getValidatedData();

        if ($id) {
            // Mise à jour
            Project::update($this->db, (int)$id, $validatedData);
            $_SESSION['flash_message'] = 'Le projet a été mis à jour avec succès !';
        } else {
            // Création
            Project::create($this->db, $validatedData);
            $_SESSION['flash_message'] = 'Le projet a été créé avec succès !';
        }

        header('Location: /admin/projects');
        exit();
    }

    /**
     * Gère la suppression d'un projet.
     */
    public function delete(array $vars): void
    {
        $this->checkAuth();
        $id = (int)$vars['id'];
        
        Project::delete($this->db, $id);

        $_SESSION['flash_message'] = 'Le projet a été supprimé avec succès.';
        header('Location: /admin/projects');
        exit();
    }
}