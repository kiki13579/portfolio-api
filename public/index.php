<?php
// public/index.php
declare(strict_types=1);

// Démarre la session pour les messages flash et l'authentification admin
session_start(); 

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\AdminController;
use App\Controllers\ProjectController;
use App\Views\JsonView;
use Doctrine\DBAL\Connection;
use Monolog\Logger;
use Twig\Environment;

// --- Conteneur et Gestion des Erreurs ---
$container = require __DIR__ . '/../config/container.php';

set_exception_handler(function (Throwable $e) use ($container) {
    $container->get(Logger::class)->error($e->getMessage(), ['exception' => $e]);
    JsonView::render(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
});

// --- Middleware de Sécurité pour l'API ---
// On vérifie si la route commence par /api/ pour n'appliquer la sécurité que sur l'API
if (str_starts_with($_SERVER['REQUEST_URI'], '/api/')) {
    App\Middleware\ApiKeyMiddleware::handle($container->get(Connection::class));
}

// --- Configuration de Twig ---
/** @var Environment $twig */
$twig = $container->get(Environment::class);
// On donne accès à la session à Twig pour les messages flash
$twig->addGlobal('app', ['session' => $_SESSION]);

// --- Routage ---
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    // API Routes
    $r->addRoute('GET', '/api/projects', [ProjectController::class, 'list']);
    $r->addRoute('POST', '/api/projects', [ProjectController::class, 'create']);
    $r->addRoute('GET', '/api/projects/{id:\d+}', [ProjectController::class, 'show']);
    
    // Admin Routes
    $r->addRoute('GET', '/admin', [AdminController::class, 'dashboard']);
    $r->addRoute('GET', '/admin/login', [AdminController::class, 'showLogin']);
    $r->addRoute('POST', '/admin/login', [AdminController::class, 'handleLogin']);
    $r->addRoute('GET', '/admin/logout', [AdminController::class, 'logout']);
    $r->addRoute('GET', '/admin/projects/new', [AdminController::class, 'showCreateForm']);
    $r->addRoute('POST', '/admin/projects/new', [AdminController::class, 'handleCreateForm']);
    $r->addRoute('GET', '/admin/projects/edit/{id:\d+}', [AdminController::class, 'showEditForm']);
    $r->addRoute('POST', '/admin/projects/edit/{id:\d+}', [AdminController::class, 'handleEditForm']);
    $r->addRoute('POST', '/admin/projects/delete/{id:\d+}', [AdminController::class, 'handleDelete']);
});
    
$routeInfo = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], strtok($_SERVER['REQUEST_URI'], '?'));
    
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        $controller = $container->get($handler[0]);
        $controller->{$handler[1]}($vars);
        break;
    
    case FastRoute\Dispatcher::NOT_FOUND:
        JsonView::render(['error' => 'Not Found'], 404);
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        JsonView::render(['error' => 'Method Not Allowed'], 405);
        break;
}