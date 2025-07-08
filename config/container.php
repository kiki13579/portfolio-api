<?php
// config/container.php

use DI\ContainerBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Psr\Container\ContainerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions([
    // On apprend au conteneur comment créer une connexion DBAL
    Connection::class => function (ContainerInterface $c) {
        $connectionParams = [
            'dbname'   => $_ENV['DB_NAME'],
            'user'     => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASS'],
            'host'     => $_ENV['DB_HOST'],
            'driver'   => 'pdo_mysql',
            'charset'  => 'utf8mb4'
        ];
        return DriverManager::getConnection($connectionParams);
    },
    CacheItemPoolInterface::class => function () {
        // Le cache sera stocké dans des fichiers dans un dossier var/cache
        return new FilesystemAdapter(
            namespace: 'app',
            defaultLifetime: 7200, // Durée de vie du cache en secondes (ici, 1 heure)
            directory: __DIR__ . '/../var/cache'
        );
    }
]);

return $containerBuilder->build();