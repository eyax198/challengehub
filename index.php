<?php

declare(strict_types=1);

// ─── Bootstrap ───────────────────────────────────────────────
define('ROOT_PATH', __DIR__);

require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/config/database.php';

// Session hardening
ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_samesite', 'Lax');

session_name(SESSION_NAME);
session_set_cookie_params([
    'lifetime' => SESSION_LIFETIME,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// ─── Simple Router ────────────────────────────────────────────
$page   = $_GET['page']   ?? 'home';
$action = $_GET['action'] ?? null;

// Load controllers
require_once ROOT_PATH . '/app/controllers/Controller.php';
require_once ROOT_PATH . '/app/controllers/HomeController.php';
require_once ROOT_PATH . '/app/controllers/AuthController.php';
require_once ROOT_PATH . '/app/controllers/UserController.php';
require_once ROOT_PATH . '/app/controllers/ChallengeController.php';
require_once ROOT_PATH . '/app/controllers/SubmissionController.php';

$authCtrl       = new AuthController();
$userCtrl       = new UserController();
$challengeCtrl  = new ChallengeController();
$submissionCtrl = new SubmissionController();
$homeCtrl       = new HomeController();

// ─── Route Table ─────────────────────────────────────────────
$routes = [
    // Auth
    'login'    => fn() => $_SERVER['REQUEST_METHOD'] === 'POST' ? $authCtrl->login()        : $authCtrl->showLogin(),
    'register' => fn() => $_SERVER['REQUEST_METHOD'] === 'POST' ? $authCtrl->register()     : $authCtrl->showRegister(),
    'logout'   => fn() => $authCtrl->logout(),

    // Home
    'home'     => fn() => $homeCtrl->index(),

    // Challenges
    'challenges'       => fn() => $challengeCtrl->index(),
    'challenge-show'   => fn() => $challengeCtrl->show(),
    'challenge-create' => fn() => $_SERVER['REQUEST_METHOD'] === 'POST' ? $challengeCtrl->create()   : $challengeCtrl->showCreate(),
    'challenge-edit'   => fn() => $_SERVER['REQUEST_METHOD'] === 'POST' ? $challengeCtrl->update()   : $challengeCtrl->showEdit(),
    'challenge-delete' => fn() => $challengeCtrl->delete(),

    // Submissions
    'submission-show'   => fn() => $submissionCtrl->show(),
    'submission-create' => fn() => $submissionCtrl->create(),
    'submission-edit'   => fn() => $_SERVER['REQUEST_METHOD'] === 'POST' ? $submissionCtrl->update() : $submissionCtrl->showEdit(),
    'submission-delete' => fn() => $submissionCtrl->delete(),
    'leaderboard'       => fn() => $submissionCtrl->leaderboard(),

    // AJAX endpoints
    'vote'        => fn() => $submissionCtrl->vote(),
    'add-comment' => fn() => $submissionCtrl->addComment(),

    // User
    'profile'      => fn() => $userCtrl->showProfile(),
    'edit-profile' => fn() => $_SERVER['REQUEST_METHOD'] === 'POST' ? $userCtrl->updateProfile()  : $userCtrl->showEditProfile(),
    'delete-account' => fn() => $userCtrl->deleteAccount(),
];

if (isset($routes[$page])) {
    ($routes[$page])();
} else {
    http_response_code(404);
    require ROOT_PATH . '/app/controllers/Controller.php';
    (new class extends Controller {
        public function notFound(): void {
            $this->render('errors.404', ['pageTitle' => 'Page introuvable']);
        }
    })->notFound();
}
