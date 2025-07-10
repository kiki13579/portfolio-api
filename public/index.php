<?php
// public/index.php
declare(strict_types=1);

// Démarre la session pour l'authentification et les messages flash
session_start();

require __DIR__ . '/../vendor/autoload.php';

// On importe toutes les classes de contrôleurs que l'on va utiliser
use App\Controllers\AdminController;
use App\Controllers\ApiKeyAdminController;
use App\Controllers\HomeController;
use App\Controllers\ProjectAdminController;
use App\Controllers\ProjectController;
use App\Controllers\SkillAdminController;
use App\Controllers\SkillController;
use App\Views\JsonView;
use Doctrine\DBAL\Connection;
use Monolog\Logger;
use Twig\Environment;

// --- Conteneur et Gestion des Erreurs ---
$container = require __DIR__ . '/../config/container.php';

set_exception_handler(function (Throwable $e) use ($container) {
    $container->get(Logger::class)->error($e->getMessage(), ['exception' => $e]);
    JsonView::render(['error' => 'Internal Server Error', 'message' => 'Une erreur est survenue.'], 500);
});

// --- Middleware de Sécurité pour l'API ---
if (str_starts_with($_SERVER['REQUEST_URI'], '/api/')) {
    App\Middleware\ApiKeyMiddleware::handle($container->get(Connection::class));
}

// --- Configuration de Twig ---
/** @var Environment $twig */
$twig = $container->get(Environment::class);
$twig->addGlobal('app', ['session' => $_SESSION]);


// --- Définition des Routes ---
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    // Route Publique
    $r->addRoute('GET', '/', [HomeController::class, 'index']);

    // Routes de l'API JSON
    $r->addRoute('GET', '/api/projects', [ProjectController::class, 'list']);
    $r->addRoute('GET', '/api/projects/{id:\d+}', [ProjectController::class, 'show']);

    $r->addRoute('GET', '/api/skills', [SkillController::class, 'list']);

    // Routes du Back-Office (Général)
    $r->addRoute('GET', '/admin', [AdminController::class, 'dashboard']);
    $r->addRoute('GET', '/admin/login', [AdminController::class, 'showLogin']);
    $r->addRoute('POST', '/admin/login', [AdminController::class, 'handleLogin']);
    $r->addRoute('GET', '/admin/logout', [AdminController::class, 'logout']);

    // Routes du Back-Office (CRUD Projets)
    $r->addRoute('GET', '/admin/projects', [ProjectAdminController::class, 'list']);
    $r->addRoute('GET', '/admin/projects/new', [ProjectAdminController::class, 'showForm']);
    $r->addRoute('POST', '/admin/projects/new', [ProjectAdminController::class, 'saveForm']);
    $r->addRoute('GET', '/admin/projects/edit/{id:\d+}', [ProjectAdminController::class, 'showForm']);
    $r->addRoute('POST', '/admin/projects/edit/{id:\d+}', [ProjectAdminController::class, 'saveForm']);
    $r->addRoute('POST', '/admin/projects/delete/{id:\d+}', [ProjectAdminController::class, 'delete']);

    // Routes du Back-Office (CRUD Compétences)
    $r->addRoute('GET', '/admin/skills', [SkillAdminController::class, 'list']);
    $r->addRoute('GET', '/admin/skills/new', [SkillAdminController::class, 'showCreateForm']);
    $r->addRoute('POST', '/admin/skills/new', [SkillAdminController::class, 'handleCreateForm']);
    $r->addRoute('GET', '/admin/skills/edit/{id:\d+}', [SkillAdminController::class, 'showForm']);

    // Routes du Back-Office (API Keys)
    $r->addRoute('GET', '/admin/api-keys', [ApiKeyAdminController::class, 'list']);
    $r->addRoute('GET', '/admin/api-keys/new', [ApiKeyAdminController::class, 'showCreateForm']);
    $r->addRoute('POST', '/admin/api-keys/new', [ApiKeyAdminController::class, 'handleCreateForm']);
});
    
// --- Lancement du Routeur ---
$routeInfo = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], strtok($_SERVER['REQUEST_URI'], '?'));
    
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::FOUND:
        [$controllerClass, $method] = $routeInfo[1];
        $vars = $routeInfo[2];

        $controller = $container->get($controllerClass);
        $controller->$method($vars);
        break;
    
    // ... gestion des erreurs 404 et 405 ...
    default:
        JsonView::render(['error' => 'Not Found'], 404);
        break;
}