<?php

require_once ROOT_PATH . '/app/models/User.php';

// CONTRÔLEUR AUTH
// On gère ici tout ce qui est connexion et inscription
class AuthController extends Controller {

    private $userModel;

    public function __construct() {
        // Chargement du modèle User pour interagir avec la table users
        $this->userModel = new User();
    }

    // Affiche le formulaire de login
    public function showLogin() {
        // Si l'utilisateur est déjà connecté, on le renvoie à l'accueil
        if ($this->isLoggedIn()) {
            $this->redirect('index.php');
        }

        // Token de sécurité pour le formulaire
        $csrf = $this->generateCsrfToken();

        $this->render('auth/login', [
            'pageTitle' => 'Connexion',
            'csrf'      => $csrf
        ]);
    }

    // Traitement du login (POST)
    public function login() {
        // Vérification de sécurité (CSRF)
        $this->verifyCsrfToken();

        // Récupération et nettoyage des données du formulaire
        $email    = $this->sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // On demande au modèle d'authentifier l'utilisateur
        $user = $this->userModel->authenticate($email, $password);

        if ($user) {
            // Succès : On remplit la session avec les infos de l'utilisateur
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // On peut stocker tout le tableau utilisateur pour y accéder facilement
            $_SESSION['user']     = $user;

            $this->setFlash('success', 'Bienvenue ' . $user['username'] . ' !');
            $this->redirect('index.php');
        } else {
            // Échec : Identifiants incorrects
            $this->setFlash('error', 'Email ou mot de passe incorrect.');
            $this->redirect('index.php?page=login');
        }
    }

    // Affiche le formulaire d'inscription
    public function showRegister() {
        if ($this->isLoggedIn()) {
            $this->redirect('index.php');
        }

        $csrf = $this->generateCsrfToken();

        $this->render('auth/register', [
            'pageTitle' => 'Créer un compte',
            'csrf'      => $csrf
        ]);
    }

    // Traitement de l'inscription (POST)
    public function register() {
        $this->verifyCsrfToken();

        // Récupération des données du formulaire
        $username = $this->sanitize($_POST['username'] ?? '');
        $email    = $this->sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        // Validations basiques (On pourrait en faire plus, mais restons simples)
        if ($password !== $confirm) {
            $this->setFlash('error', 'Les mots de passe ne correspondent pas.');
            $this->redirect('index.php?page=register');
        }

        // On vérifie si l'email ou le pseudo existe déjà
        if ($this->userModel->findByEmail($email)) {
            $this->setFlash('error', 'Cet email est déjà utilisé.');
            $this->redirect('index.php?page=register');
        }

        // Gestion de l'avatar (image de profil) via l'outil hérité du parent
        $avatar = $this->handleUpload('avatar');

        // Création de l'utilisateur en base de données
        $userId = $this->userModel->create([
            'username' => $username,
            'email'    => $email,
            'password' => $password,
            'avatar'   => $avatar,
            'bio'      => ''
        ]);

        if ($userId) {
            $this->setFlash('success', 'Votre compte a été créé avec succès ! Connectez-vous.');
            $this->redirect('index.php?page=login');
        } else {
            $this->setFlash('error', 'Une erreur est survenue lors de l\'inscription.');
            $this->redirect('index.php?page=register');
        }
    }

    // Déconnexion
    public function logout() {
        // On vide toutes les variables de session
        session_unset();
        
        // On détruit la session actuelle
        session_destroy();

        // On repart sur une nouvelle session propre pour le message de confirmation
        session_start();
        $this->setFlash('success', 'Vous avez été déconnecté.');
        $this->redirect('index.php');
    }
}
