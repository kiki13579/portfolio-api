<?php

namespace App\Middleware;

use App\Models\ApiKey;
use App\Views\JsonView;
use PDO;

class ApiKeyMiddleware
{
    public static function handle(PDO $pdo): void
    {
        $providedKey = $_SERVER['HTTP_X_API_KEY'] ?? null;

        if ($providedKey === null || !ApiKey::isValid($providedKey, $pdo)) {
            JsonView::render(['error' => 'Unauthorized'], 401);
            exit();
        }
    }
}