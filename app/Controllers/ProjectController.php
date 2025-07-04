<?php
namespace App\Controllers;

use Rakit\Validation\Validator;
use App\Models\Project;
use App\Views\JsonView;
use PDO;

class ProjectController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    public function create(): void
    {
        $input = (array) json_decode(file_get_contents('php://input'), true);

        $validator = new Validator;
        $validation = $validator->validate($input, [
            'title'       => 'required|min:5',
            'description' => 'required|min:10',
            'projectUrl'  => 'url'
        ]);

        if ($validation->fails()) {
            $errors = $validation->errors();
            JsonView::render(['errors' => $errors->firstOfAll()], 400); // 400 Bad Request
            return;
        }

        $newProjectId = Project::create($this->pdo, $input);

        if ($newProjectId) {
            JsonView::render(['message' => 'Project created successfully', 'id' => $newProjectId], 201); // 201 Created
        } else {
            JsonView::render(['error' => 'Failed to create project'], 500);
        }
    }
    public function show(array $vars): void
    {
        $id = (int)$vars['id'];
        $project = Project::fetchOne($this->pdo, $id);

        if (!$project) {
            JsonView::render(['error' => 'Project not found'], 404);
        } else {
            JsonView::render($project);
        }
    }
    public function list(): void
    {
        $projects = Project::fetchAll($this->pdo);
        JsonView::render($projects);
    }
    public function update(array $vars): void
    {
        $id = (int)$vars['id'];
        $input = (array) json_decode(file_get_contents('php://input'), true);

        $validator = new \Rakit\Validation\Validator;
        $validation = $validator->validate($input, [
            'title'       => 'required|min:5',
            'description' => 'required|min:10',
            'projectUrl'  => 'url'
        ]);

        if ($validation->fails()) {
            JsonView::render(['errors' => $validation->errors()->firstOfAll()], 400);
            return;
        }

        if (!Project::fetchOne($this->pdo, $id)) {
            JsonView::render(['error' => 'Project not found'], 404);
            return;
        }

        Project::update($this->pdo, $id, $input);
        JsonView::render(['message' => 'Project updated successfully']);
    }
    public function delete(array $vars): void
    {
        $id = (int)$vars['id'];

        if (!Project::fetchOne($this->pdo, $id)) {
            JsonView::render(['error' => 'Project not found'], 404);
            return;
        }

        Project::delete($this->pdo, $id);
        JsonView::render(null, 204); 
    }
}