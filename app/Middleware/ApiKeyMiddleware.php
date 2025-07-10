<?php
// app/Middleware/ApiKeyMiddleware.php
namespace App\Middleware;

use App\Models\ApiKey;
use App\Views\JsonView;
use Doctrine\DBAL\Connection; // <-- On importe la bonne classe

class ApiKeyMiddleware
{
    // On change le type de $pdo ici, de PDO à Connection
    public static function handle(Connection $db): void
    {
        $providedKey = $_SERVER['HTTP_X_API_KEY'] ?? null;

        // On passe la connexion $db à la méthode isValid
        if ($providedKey === null || !ApiKey::isValid($providedKey, $db)) {
            JsonView::render(['error' => 'Unauthorized'], 401);
            exit();
        }
    }
}