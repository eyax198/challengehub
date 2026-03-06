<?php

require_once ROOT_PATH . '/app/models/Challenge.php';
require_once ROOT_PATH . '/app/models/Submission.php';
require_once ROOT_PATH . '/app/models/User.php';

// CONTRÔLEUR ACCUEIL
class HomeController extends Controller {

    public function index() {
        // Chargement des modèles nécessaires pour l'accueil
        $challengeModel  = new Challenge();
        $submissionModel = new Submission();
        $userModel       = new User();

        // On va chercher ce qu'on veut montrer sur l'accueil
        $recentChallenges = $challengeModel->getRecent(6); // 6 derniers défis
        $topSubmissions   = $submissionModel->getTopSubmissions(3); // Top 3 des projets
        
        // On compte les membres pour faire une petite stat sympa
        $allUsers = $userModel->getAll();

        $this->render('home/index', [
            'pageTitle'        => 'Accueil — ChallengeHub',
            'recentChallenges' => $recentChallenges,
            'topSubmissions'   => $topSubmissions,
            'totalUsers'       => count($allUsers),
        ]);
    }
}
