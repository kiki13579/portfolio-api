<?php
// public/index.php -- FICHIER DE DIAGNOSTIC TEMPORAIRE

// Force l'affichage de TOUTES les erreurs possibles
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Diagnostic du Serveur</h1>";

// --- Test 1: Version de PHP ---
echo "<h2>1. Version de PHP</h2>";
echo "<p>Version utilisée : " . phpversion() . "</p>";

// --- Test 2: Connexion à la base de données ---
echo "<h2>2. Connexion Base de Données</h2>";
try {
    // On charge les variables d'environnement manuellement pour ce test
    $dotenv_path = __DIR__ . '/../.env';
    if (!file_exists($dotenv_path)) {
        throw new Exception("Le fichier .env est introuvable.");
    }

    $lines = file($dotenv_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }

    $host = $_ENV['DB_HOST'] ?? null;
    $dbName = $_ENV['DB_NAME'] ?? null;
    $user = $_ENV['DB_USER'] ?? null;
    $pass = $_ENV['DB_PASS'] ?? null;

    if (!$host || !$dbName || !$user) {
        throw new Exception("Une ou plusieurs variables de base de données (DB_HOST, DB_NAME, DB_USER) sont manquantes dans le fichier .env.");
    }

    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8mb4", $user, $pass);
    echo "<p style='color:green; font-weight:bold;'>Connexion à la base de données '{$dbName}' : RÉUSSIE !</p>";

} catch (Throwable $e) {
    echo "<p style='color:red; font-weight:bold;'>Connexion à la base de données : ÉCHEC.</p>";
    echo "<p><strong>Message d'erreur :</strong> " . $e->getMessage() . "</p>";
}

// --- Test 3: Extensions PHP requises ---
echo "<h2>3. Extensions PHP</h2>";
$required_extensions = ['pdo_mysql', 'json', 'ctype'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p style='color:green;'>Extension '{$ext}' : OK</p>";
    } else {
        echo "<p style='color:red; font-weight:bold;'>Extension '{$ext}' : MANQUANTE !</p>";
    }
}

// Fin du script de diagnostic
exit();