<?php

require_once __DIR__ . '/Controller.php';
require_once ROOT_PATH . '/app/models/Challenge.php';
require_once ROOT_PATH . '/app/models/Submission.php';
require_once ROOT_PATH . '/app/models/User.php';

class HomeController extends Controller {

    public function index(): void {
        $challengeModel  = new Challenge();
        $submissionModel = new Submission();
        $userModel       = new User();

        $recentChallenges = $challengeModel->getRecent(6);
        $topSubmissions   = $submissionModel->getTopSubmissions(3);
        $allUsers         = $userModel->getAll();

        $this->render('home.index', [
            'pageTitle'        => 'Accueil — ' . APP_NAME,
            'recentChallenges' => $recentChallenges,
            'topSubmissions'   => $topSubmissions,
            'totalUsers'       => count($allUsers),
        ]);
    }
}
