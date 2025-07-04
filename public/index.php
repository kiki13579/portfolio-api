<?php

declare(strict_types=1);

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\ProjectController;
use App\Views\JsonView;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

set_exception_handler(function ($e) {
    // Crée un nouveau logger qui écrit dans le fichier logs/app.log
    $log = new Logger('API');
    $log->pushHandler(new StreamHandler(__DIR__ . '/../logs/app.log', Logger::ERROR));

    // Enregistre l'erreur détaillée dans le fichier de log
    $log->error($e->getMessage(), [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
    
    // Affiche un message générique à l'utilisateur
    App\Views\JsonView::render(['error' => 'An internal server error occurred'], 500);
});


$pdo = require __DIR__ . '/../config/database.php';


App\Middleware\ApiKeyMiddleware::handle($pdo);


$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    // Route pour lister tous les projets
    $r->addRoute('GET', '/api/projects', [ProjectController::class, 'list']);

    // Route pour créer un nouveau projet
    $r->addRoute('POST', '/api/projects', [ProjectController::class, 'create']);

    // Route pour récupérer un seul projet par son ID
    // {id:\d+} signifie que l'id doit être un nombre entier
    $r->addRoute('GET', '/api/projects/{id:\d+}', [ProjectController::class, 'show']);

    // Route pour mettre à jour un projet par son ID
    $r->addRoute('PUT', '/api/projects/{id:\d+}', [ProjectController::class, 'update']);

    // Route pour supprimer un projet par son ID
    $r->addRoute('DELETE', '/api/projects/{id:\d+}', [ProjectController::class, 'delete']);
});

// --- Lancement du routeur ---
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = strtok($_SERVER['REQUEST_URI'], '?');

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        JsonView::render(['error' => 'Not Found'], 404);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        JsonView::render(['error' => 'Method Not Allowed'], 405);
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2]; // Contient les paramètres de l'URL, comme l'ID

        // On crée une instance du contrôleur et on appelle la méthode
        // en passant les paramètres de l'URL
        (new $handler[0]($pdo))->{$handler[1]}($vars);
        break;
}