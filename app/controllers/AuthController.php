<?php

require_once __DIR__ . '/Controller.php';
require_once ROOT_PATH . '/app/models/User.php';

class AuthController extends Controller {

    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // ── GET /login ────────────────────────────────────────────
    public function showLogin(): void {
        if ($this->isLoggedIn()) {
            $this->redirect(BASE_URL . '/index.php?page=challenges');
        }
        $csrf = $this->generateCsrfToken();
        $this->render('auth.login', ['csrf' => $csrf, 'pageTitle' => 'Connexion']);
    }

    // ── POST /login ───────────────────────────────────────────
    public function login(): void {
        $this->verifyCsrfToken();

        $email    = $this->post('email');
        $password = $_POST['password'] ?? ''; // do NOT sanitize raw password

        if (empty($email) || empty($password)) {
            $this->setFlash('error', 'Veuillez remplir tous les champs.');
            $this->redirect(BASE_URL . '/index.php?page=login');
            return;
        }

        $user = $this->userModel->authenticate($email, $password);

        if (!$user) {
            $this->setFlash('error', 'Email ou mot de passe incorrect.');
            $this->redirect(BASE_URL . '/index.php?page=login');
            return;
        }

        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user']    = [
            'id'       => $user['id'],
            'username' => $user['username'],
            'email'    => $user['email'],
            'avatar'   => $user['avatar'],
        ];

        $this->setFlash('success', 'Bienvenue, ' . htmlspecialchars($user['username']) . ' !');
        $this->redirect(BASE_URL . '/index.php?page=challenges');
    }

    // ── GET /register ─────────────────────────────────────────
    public function showRegister(): void {
        if ($this->isLoggedIn()) {
            $this->redirect(BASE_URL . '/index.php?page=challenges');
        }
        $csrf = $this->generateCsrfToken();
        $this->render('auth.register', ['csrf' => $csrf, 'pageTitle' => 'Inscription']);
    }

    // ── POST /register ────────────────────────────────────────
    public function register(): void {
        $this->verifyCsrfToken();

        $username  = $this->post('username');
        $email     = $this->post('email');
        $password  = $_POST['password']  ?? '';
        $password2 = $_POST['password2'] ?? '';

        // Validations
        $errors = [];
        if (strlen($username) < 3 || strlen($username) > 50) {
            $errors[] = 'Le nom d\'utilisateur doit comporter entre 3 et 50 caractères.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Adresse email invalide.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Le mot de passe doit comporter au moins 8 caractères.';
        }
        if ($password !== $password2) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }
        if ($this->userModel->findByEmail($email)) {
            $errors[] = 'Cet email est déjà utilisé.';
        }
        if ($this->userModel->findByUsername($username)) {
            $errors[] = 'Ce nom d\'utilisateur est déjà pris.';
        }

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data']   = compact('username', 'email');
            $this->redirect(BASE_URL . '/index.php?page=register');
            return;
        }

        $avatar = $this->handleUpload('avatar', 'avatars');
        $bio    = $this->post('bio');

        $userId = $this->userModel->create([
            'username' => $username,
            'email'    => $email,
            'password' => $password,
            'avatar'   => $avatar,
            'bio'      => $bio,
        ]);

        if ($userId) {
            $this->setFlash('success', 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.');
            $this->redirect(BASE_URL . '/index.php?page=login');
        } else {
            $this->setFlash('error', 'Erreur lors de la création du compte.');
            $this->redirect(BASE_URL . '/index.php?page=register');
        }
    }

    // ── Logout ────────────────────────────────────────────────
    public function logout(): void {
        session_unset();
        session_destroy();
        $this->redirect(BASE_URL . '/index.php?page=home');
    }
}
