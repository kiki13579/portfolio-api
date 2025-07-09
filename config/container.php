<?php 
use DI\ContainerBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Container\ContainerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    Connection::class => function () {
        return DriverManager::getConnection([
            'dbname'   => $_ENV['DB_NAME'], 'user' => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASS'], 'host' => $_ENV['DB_HOST'],
            'driver'   => 'pdo_mysql',
        ]);
    },
    CacheItemPoolInterface::class => function () {
        return new FilesystemAdapter('app', 3600, __DIR__ . '/../var/cache');
    },
    Logger::class => function () {
        $log = new Logger('API');
        $log->pushHandler(new StreamHandler(__DIR__ . '/../logs/app.log'));
        return $log;
    },
    Environment::class => function () {
        $loader = new FilesystemLoader(__DIR__ . '/../templates');
        
        return new Environment($loader, [
            'cache' => false, 
        ]);
    },
]);
return $containerBuilder->build();

// Explication : Ce fichier utilise les informations de votre .env pour créer et configurer des services (connexion DB, cache, logger). Le conteneur d'injection de dépendances (php-di) saura ensuite les fournir automatiquement aux classes qui en ont besoin.