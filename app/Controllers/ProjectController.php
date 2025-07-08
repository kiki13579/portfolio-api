<?php
// app/Controllers/ProjectController.php
namespace App\Controllers;

use App\Models\Project;
use App\Views\JsonView;
use Doctrine\DBAL\Connection;
use Rakit\Validation\Validator;

class ProjectController
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function list(): void
    {
        $projects = Project::fetchAll($this->db);
        JsonView::render($projects);
    }

    public function show(array $vars): void
    {
        $id = (int)$vars['id'];
        $project = Project::fetchOne($this->db, $id);

        if (!$project) {
            JsonView::render(['error' => 'Project not found'], 404);
        } else {
            JsonView::render($project);
        }
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
            JsonView::render(['errors' => $errors->firstOfAll()], 400);
            return;
        }

        $newProjectId = Project::create($this->db, $input);

        if ($newProjectId) {
            JsonView::render(['message' => 'Project created successfully', 'id' => $newProjectId], 201);
        } else {
            JsonView::render(['error' => 'Failed to create project'], 500);
        }
    }

    public function update(array $vars): void
    {
        $id = (int)$vars['id'];
        $input = (array) json_decode(file_get_contents('php://input'), true);

        $validator = new Validator;
        $validation = $validator->validate($input, [
            'title'       => 'required|min:5',
            'description' => 'required|min:10',
            'projectUrl'  => 'url'
        ]);

        if ($validation->fails()) {
            JsonView::render(['errors' => $validation->errors()->firstOfAll()], 400);
            return;
        }

        if (!Project::fetchOne($this->db, $id)) {
            JsonView::render(['error' => 'Project not found'], 404);
            return;
        }

        Project::update($this->db, $id, $input);
        JsonView::render(['message' => 'Project updated successfully']);
    }

    public function delete(array $vars): void
    {
        $id = (int)$vars['id'];

        if (!Project::fetchOne($this->db, $id)) {
            JsonView::render(['error' => 'Project not found'], 404);
            return;
        }
        
        Project::delete($this->db, $id);
        JsonView::render(null, 204);
    }
}