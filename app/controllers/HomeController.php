<?php

require_once ROOT_PATH . '/app/models/Challenge.php';
require_once ROOT_PATH . '/app/models/Submission.php';
require_once ROOT_PATH . '/app/models/User.php';

/**
 * Contrôleur Home - Gère l'affichage de la page d'accueil
 */
class HomeController extends Controller {

    public function index() {
        // Chargement des modèles nécessaires pour l'accueil
        $challengeModel  = new Challenge();
        $submissionModel = new Submission();
        $userModel       = new User();

        // On récupère les données à afficher sur l'accueil
        $recentChallenges = $challengeModel->getRecent(6); // 6 derniers défis
        $topSubmissions   = $submissionModel->getTopSubmissions(3); // Top 3 des projets
        
        // On récupère tous les utilisateurs pour afficher une stat (ex: "Déjà 12 membres")
        $allUsers = $userModel->getAll();

        $this->render('home/index', [
            'pageTitle'        => 'Accueil — ChallengeHub',
            'recentChallenges' => $recentChallenges,
            'topSubmissions'   => $topSubmissions,
            'totalUsers'       => count($allUsers),
        ]);
    }
}
