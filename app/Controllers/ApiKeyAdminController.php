<?php
// app/Controllers/ApiKeyAdminController.php
namespace App\Controllers;

use App\Models\ApiKey;
use Rakit\Validation\Validator;

class ApiKeyAdminController extends AdminController
{
    public function list(): void
    {
        $this->checkAuth();
        $keys = ApiKey::fetchAll($this->db);
        echo $this->twig->render('admin/api_keys/list.html.twig', ['api_keys' => $keys]);
    }

    public function showCreateForm(): void
    {
        $this->checkAuth();
        echo $this->twig->render('admin/api_keys/form.html.twig');
    }

    public function handleCreateForm(): void
    {
        $this->checkAuth();
        
        // Validation des données du formulaire
        $validator = new Validator;
        $validation = $validator->validate($_POST, ['name' => 'required', 'master_key' => 'required']);
        if ($validation->fails()) {
            echo $this->twig->render('admin/api_keys/form.html.twig', ['errors' => $validation->errors()->firstOfAll()]);
            return;
        }

        // Vérification de la clé originelle
        $masterKey = $_POST['master_key'];
        if (!ApiKey::isMasterKey($masterKey, $this->db)) {
            echo $this->twig->render('admin/api_keys/form.html.twig', ['error' => 'La clé originelle fournie est invalide.']);
            return;
        }

        // Si tout est bon, on crée la nouvelle clé
        $newKeyData = [
            'name' => $_POST['name'],
            'api_key' => bin2hex(random_bytes(16)) // Génère une nouvelle clé aléatoire
        ];
        ApiKey::create($this->db, $newKeyData);

        $_SESSION['flash_message'] = "La nouvelle clé '{$newKeyData['name']}' a été créée avec succès !";
        header('Location: /admin/api-keys');
        exit();
    }
}