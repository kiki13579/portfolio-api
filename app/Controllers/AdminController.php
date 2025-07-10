<?php

namespace App\Controllers;

use Doctrine\DBAL\Connection;
use Twig\Environment;

class AdminController
{
    /**
     * Le constructeur reçoit les services dont tous les contrôleurs admin auront besoin.
     * Ils sont 'protected' pour que les classes enfants (comme ProjectAdminController) puissent y accéder.
     */
    public function __construct(
        protected Environment $twig,
        protected Connection $db
    ) {
        // On s'assure que $_ENV est chargé pour lire le mot de passe admin
        if (empty($_ENV['ADMIN_PASSWORD'])) {
            $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
            $dotenv->load();
        }
    }

    /**
     * Affiche la page d'accueil du back-office.
     */
    public function dashboard(): void
    {
        $this->checkAuth();
        echo $this->twig->render('admin/dashboard.html.twig');
    }

    /**
     * Affiche le formulaire de connexion.
     */
    public function showLogin(): void
    {
        echo $this->twig->render('admin/login.html.twig');
    }

    /**
     * Traite la soumission du formulaire de connexion.
     */
    public function handleLogin(): void
    {
        if (!empty($_POST['password']) && $_POST['password'] === $_ENV['ADMIN_PASSWORD']) {
            $_SESSION['is_admin'] = true;
            header('Location: /admin');
            exit();
        }

        echo $this->twig->render('admin/login.html.twig', ['error' => 'Mot de passe incorrect.']);
    }

    /**
     * Gère la déconnexion de l'administrateur.
     */
    public function logout(): void
    {
        session_destroy();
        header('Location: /admin/login');
        exit();
    }

    /**
     * Vérifie si l'utilisateur est bien authentifié comme admin.
     * Cette méthode est 'protected' pour être utilisable par les contrôleurs enfants.
     */
    protected function checkAuth(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            header('Location: /admin/login');
            exit();
        }
    }
}