<?php
namespace App\Controllers;

use App\Models\Project;
use App\Views\JsonView;
use Doctrine\DBAL\Connection;
use Psr\Cache\CacheItemPoolInterface;
use Rakit\Validation\Validator;

class ProjectController
{
    public function __construct(
        private Connection $db,
        private CacheItemPoolInterface $cache
    ) {}

    public function list(): void
    {
        $cacheItem = $this->cache->getItem('projects_list');
        if ($cacheItem->isHit()) {
            JsonView::render($cacheItem->get());
            return;
        }

        $projects = Project::fetchAll($this->db);

        $cacheItem->set($projects);
        $this->cache->save($cacheItem);
        
        JsonView::render($projects);
    }

    public function show(array $vars): void
    {
        $project = Project::fetchOne($this->db, (int)$vars['id']);
        if (!$project) {
            JsonView::render(['error' => 'Project not found'], 404);
        }
        JsonView::render($project);
    }

    public function create(): void
    {
        $input = (array) json_decode(file_get_contents('php://input'), true);

        $validator = new Validator;
        $validation = $validator->validate($input, [
            'title' => 'required|min:5',
            'description' => 'required'
        ]);

        if ($validation->fails()) {
            JsonView::render(['errors' => $validation->errors()->firstOfAll()], 400);
            return;
        }

        $newProjectId = Project::create($this->db, $validation->getValidatedData());

        if ($newProjectId) {
            $this->cache->deleteItem('projects_list');
            JsonView::render(['message' => 'Project created', 'id' => $newProjectId], 201);
        } else {
            JsonView::render(['error' => 'Failed to create project'], 500);
        }
    }
}
// Explication : Le contrôleur reçoit la requête, demande au cache ou au modèle les données, et passe le résultat à la vue. Il ne fait rien d'autre.