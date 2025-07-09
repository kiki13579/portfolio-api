<?php
// seed.php

// On charge toutes nos classes et dépendances
require 'vendor/autoload.php';

// On utilise notre Modèle Project pour interagir avec la DB
use App\Models\Project;
use Doctrine\DBAL\Connection;

// --- On utilise le conteneur pour récupérer nos services ---
$container = require 'config/container.php';

/** @var Connection $db */
$db = $container->get(Connection::class);

// On initialise Faker
$faker = Faker\Factory::create('fr_FR');

try {
    echo "Nettoyage de la table 'project'...\n";
    // On utilise la connexion DBAL pour vider la table
    $db->executeStatement('TRUNCATE TABLE project');

    echo "Démarrage de la création de 10 projets de test...\n\n";

    for ($i = 0; $i < 10; $i++) {
        $projectData = [
            'title'       => rtrim($faker->company(), '.'),
            'description' => $faker->paragraph(3),
            'projectUrl'  => $faker->url(),
        ];
        
        // On passe la connexion DBAL ($db) à notre modèle
        $newId = Project::create($db, $projectData);

        if ($newId) {
            echo "Projet '{$projectData['title']}' créé avec l'ID {$newId}.\n";
        } else {
            echo "Échec de la création du projet '{$projectData['title']}'.\n";
        }
    }

    echo "\nOpération terminée avec succès !\n";

} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}