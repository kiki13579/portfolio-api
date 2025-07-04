----------------------------------------- API de Portfolio - PHP Pur MVC -----------------------------------------

Il s'agit d'une API RESTful légère, construite en PHP pur sans framework majeur, en suivant une architecture de type Modèle-Vue-Contrôleur (MVC). Elle est conçue pour servir de backend à un portfolio frontend Dynamique (HTML, CSS, JS).


✨ Les fonctionnalités :
    - CRUD complet pour la gestion des ressources "Projet".
    - Architecture MVC pour une meilleure organisation et maintenabilité du code.
    - Sécurité par Clé d'API = Tous les points d'accès sont protégés et nécessitent une clé valide dans l'en-tête X-API-KEY.
    - Routage Propre = Utilisation de la bibliothèque nikic/fast-route pour gérer les URL de manière efficace et claire.
    - Validation des Données = Les données entrantes (POST, PUT) sont validées avec la bibliothèque rakit/validation pour garantir leur intégrité.
    - Gestion des Erreurs et Logging = Les erreurs critiques sont capturées et enregistrées dans un fichier de log privé via monolog/monolog, sans exposer d'informations sensibles à l'utilisateur final.
    - Configuration par Environnement = Utilisation de fichiers .env pour gérer les secrets (identifiants de base de données, clés) grâce à vlucas/phpdotenv, séparant la configuration du code.


🛠️ Les Technologies Utilisées :
    - PHP
    - MySQL / MariaDB
    - Composer pour la gestion des dépendances
    - Bibliothèques PHP clés :
        - nikic/fast-route = Pour le routage.
        - rakit/validation = Pour la validation.
        - monolog/monolog = Pour les logs.
        - vlucas/phpdotenv = Pour les variables d'environnement.


🚀 Installation et Lancement Local :
    - Cloner le projet :
        - git clone [URL_DU_REPO] api-mvc
        - cd api-mvc
    - Installer les dépendances PHP :
        - composer install
    - Configurer l'environnement :
        - Créez un fichier .env à la racine du projet en copiant le fichier .env.example (si fourni) ou en utilisant le modèle ci-dessous.
        - Remplissez le fichier .env avec vos informations de base de données locale.
    - Contenu du fichier .env à créer :
        - DB_HOST=127.0.0.1
        - DB_PORT=3306
        - DB_NAME=portfolio_api_db
        - DB_USER=portfolio_user
        - DB_PASS=votre_mot_de_passe
    - Préparer la base de données :
        - Assurez-vous que votre serveur de base de données est lancé.
        - Créez la base de données (portfolio_api_db) et l'utilisateur (portfolio_user).
        - Exécutez les scripts SQL pour créer les tables project et api_keys.
        - Insérez au moins une clé d'API valide dans la table api_keys.
    - Lancer le serveur :
        - Le point d'entrée du serveur est le dossier public.
        - php -S localhost:8080 -t public
        - L'API est maintenant accessible à l'adresse http://localhost:8080.


📖 Endpoints de l'API :
    - Toutes les requêtes doivent inclure un en-tête X-API-KEY avec une clé d'API valide.
    