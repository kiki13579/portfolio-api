<?php
// app/Models/ApiKey.php
namespace App\Models;

use Doctrine\DBAL\Connection; // <-- On importe la bonne classe

class ApiKey
{
    // On change le type de $pdo ici aussi, de PDO à Connection
    public static function isValid(string $key, Connection $db): bool
    {
        // La logique reste la même, mais on utilise les méthodes de DBAL
        $stmt = $db->prepare('SELECT 1 FROM api_keys WHERE api_key = :api_key');
        $stmt->bindValue('api_key', $key);
        $result = $stmt->executeQuery();
        
        return $result->fetchOne() !== false;
    }
}