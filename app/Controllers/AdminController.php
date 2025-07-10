<?php
namespace App\Controllers;

use App\Models\Project;
use Doctrine\DBAL\Connection;
use Rakit\Validation\Validator;
use Twig\Environment;

class AdminController
{
    public function __construct(protected Environment $twig, protected Connection $db)
    {
        if (empty($_ENV['ADMIN_PASSWORD'])) {
            $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
            $dotenv->load();
        }
    }

    public function showLogin(): void
    {
        echo $this->twig->render('admin/login.html.twig');
    }

    public function handleLogin(): void
    {
        if (!empty($_POST['password']) && $_POST['password'] === $_ENV['ADMIN_PASSWORD']) {
            $_SESSION['is_admin'] = true;
            header('Location: /admin');
            exit();
        }

        echo $this->twig->render('admin/login.html.twig', ['error' => 'Mot de passe incorrect.']);
    }

    // Gère la 
    public function logout(): void
    {
        session_destroy();
        header('Location: /admin/login');
        exit();
    }

    public function dashboard(): void
    {
        $this->checkAuth();

        $projects = Project::fetchAll($this->db);

        echo $this->twig->render('admin/dashboard.html.twig', [
            'projects' => $projects
        ]);
    }

    protected function checkAuth(): void
    {
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: /admin/login');
            exit();
        }
    }
    public function showCreateForm(): void
    {
        $this->checkAuth();
        echo $this->twig->render('admin/project_form.html.twig', [
            'form_title' => 'Créer un nouveau projet'
        ]);
    }

    public function handleCreateForm(): void
    {
        $this->checkAuth();
        $validator = new Validator;
        
        $validation = $validator->validate($_POST, [
            'title'       => 'required|min:5',
            'description' => 'required|min:10',
            'projectUrl'  => 'url'
        ]);

        if ($validation->fails()) {
            echo $this->twig->render('admin/project_form.html.twig', [
                'form_title' => 'Créer un nouveau projet',
                'errors' => $validation->errors()->firstOfAll(),
                'old' => $_POST
            ]);
            return;
        }

        $validatedData = $validation->getValidatedData();
        Project::create($this->db, $validatedData);

        $_SESSION['flash_message'] = 'Le projet a été créé avec succès !';
        header('Location: /admin');
        exit();
    }
    public function showEditForm(array $vars): void
    {
        $this->checkAuth();
        $id = (int)$vars['id'];
        $project = Project::fetchOne($this->db, $id);

        if (!$project) {
            header('Location: /admin');
            exit();
        }

        echo $this->twig->render('admin/project_form.html.twig', [
            'form_title' => 'Modifier le projet : ' . $project['title'],
            'old' => $project
        ]);
    }

    public function handleEditForm(array $vars): void
    {
        $this->checkAuth();
        $id = (int)$vars['id'];
        
        $validator = new Validator;
        $validation = $validator->validate($_POST, [ /* ... mêmes règles que pour create ... */ ]);

        if ($validation->fails()) {
            echo $this->twig->render('admin/project_form.html.twig', [
                'form_title' => 'Modifier le projet',
                'errors' => $validation->errors()->firstOfAll(),
                'old' => $_POST
            ]);
            return;
        }

        $validatedData = $validation->getValidatedData();
        Project::update($this->db, $id, $validatedData);

        $_SESSION['flash_message'] = 'Le projet a été mis à jour avec succès !';
        header('Location: /admin');
        exit();
    }
    public function handleDelete(array $vars): void
    {
        $this->checkAuth();
        $id = (int)$vars['id'];
        
        Project::delete($this->db, $id);

        $_SESSION['flash_message'] = 'Le projet a été supprimé avec succès.';
        header('Location: /admin');
        exit();
    }
}