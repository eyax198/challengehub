<?php

/**
 * INDEX.PHP - Le Front Controller
 * C'est le point d'entrée unique de l'application.
 * Il initialise la session, charge les fichiers et gère les routes.
 */

// 1. Démarrage de la session (pour gérer la connexion utilisateur)
session_start();

// 2. Définition du chemin racine du projet
define('ROOT_PATH', __DIR__);

// 3. Inclusion des fichiers de configuration
require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/config/database.php';

// 4. Chargement de tous les contrôleurs
require_once ROOT_PATH . '/app/controllers/Controller.php';
require_once ROOT_PATH . '/app/controllers/HomeController.php';
require_once ROOT_PATH . '/app/controllers/AuthController.php';
require_once ROOT_PATH . '/app/controllers/UserController.php';
require_once ROOT_PATH . '/app/controllers/ChallengeController.php';
require_once ROOT_PATH . '/app/controllers/SubmissionController.php';

// 5. Instanciation des contrôleurs
$authCtrl       = new AuthController();
$userCtrl       = new UserController();
$challengeCtrl  = new ChallengeController();
$submissionCtrl = new SubmissionController();
$homeCtrl       = new HomeController();

// 6. Récupération de la page demandée depuis l'URL (ex: ?page=login)
// Si aucune page n'est précisée, on va sur 'home'
$page = $_GET['page'] ?? 'home';

// 7. ROUTEUR : On utilise un switch/case pour diriger l'utilisateur au bon endroit
switch ($page) {
    
    // --- ACCUEIL ---
    case 'home':
        $homeCtrl->index();
        break;

    // --- AUTHENTIFICATION ---
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authCtrl->login(); // Traitement du formulaire
        } else {
            $authCtrl->showLogin(); // Affichage du formulaire
        }
        break;

    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authCtrl->register();
        } else {
            $authCtrl->showRegister();
        }
        break;

    case 'logout':
        $authCtrl->logout();
        break;

    // --- DÉFIS (CHALLENGES) ---
    case 'challenges':
        $challengeCtrl->index();
        break;

    case 'challenge-show':
        $challengeCtrl->show();
        break;

    case 'challenge-create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $challengeCtrl->create();
        } else {
            $challengeCtrl->showCreate();
        }
        break;

    case 'challenge-edit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $challengeCtrl->update();
        } else {
            $challengeCtrl->showEdit();
        }
        break;

    case 'challenge-delete':
        $challengeCtrl->delete();
        break;

    // --- PARTICIPATIONS (SUBMISSIONS) ---
    case 'submission-show':
        $submissionCtrl->show();
        break;

    case 'submission-create':
        $submissionCtrl->create();
        break;

    case 'submission-edit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $submissionCtrl->update();
        } else {
            $submissionCtrl->showEdit();
        }
        break;

    case 'submission-delete':
        $submissionCtrl->delete();
        break;

    case 'leaderboard':
        $submissionCtrl->leaderboard();
        break;

    // --- ACTIONS AJAX (VOTES / COMMENTAIRES) ---
    case 'vote':
        $submissionCtrl->vote();
        break;

    case 'add-comment':
        $submissionCtrl->addComment();
        break;

    // --- UTILISATEUR / PROFIL ---
    case 'profile':
        $userCtrl->showProfile();
        break;

    case 'edit-profile':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userCtrl->updateProfile();
        } else {
            $userCtrl->showEditProfile();
        }
        break;

    case 'delete-account':
        $userCtrl->deleteAccount();
        break;

    // --- ERREUR 404 ---
    default:
        http_response_code(404);
        echo "<div style='text-align:center; padding:50px; font-family:sans-serif;'>
                <h1>404 - Page non trouvée</h1>
                <p>La page que vous recherchez n'existe pas.</p>
                <p><a href='index.php'>Retour à l'accueil</a></p>
              </div>";
        break;
}
