<?php

require_once ROOT_PATH . '/app/models/User.php';

// CONTRÔLEUR USER
// Gestion des profils et modifs de compte
class UserController extends Controller {

    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // Voir un profil
    public function showProfile() {
        // Si aucun ID n'est passé en URL, on affiche le profil de l'utilisateur connecté
        $id = isset($_GET['id']) ? (int)$_GET['id'] : $this->currentUserId();
        
        $user = $this->userModel->findById($id);

        if (!$user) {
            $this->setFlash('error', 'Utilisateur introuvable.');
            $this->redirect('index.php?page=challenges');
        }

        // On charge les modèles pour voir ce qu'il a fait
        require_once ROOT_PATH . '/app/models/Challenge.php';
        require_once ROOT_PATH . '/app/models/Submission.php';

        $challengeModel  = new Challenge();
        $submissionModel = new Submission();

        $challenges  = $challengeModel->getByUser($id);
        $submissions = $submissionModel->getByUser($id);
        
        // Récupération des statistiques (nombre de votes reçus, etc.)
        $stats = $this->userModel->getStats($id);

        $this->render('user/profile', [
            'pageTitle'   => $user['username'] . ' — Profil',
            'profileUser' => $user,
            'challenges'  => $challenges,
            'submissions' => $submissions,
            'stats'       => $stats,
            'csrf'        => $this->generateCsrfToken() // Pour la suppression de compte
        ]);
    }

    // Formulaire de modif de profil
    public function showEditProfile() {
        $this->requireLogin();
        
        $user = $this->userModel->findById($this->currentUserId());
        
        $this->render('user/edit_profile', [
            'pageTitle' => 'Paramètres du compte',
            'user'      => $user,
            'csrf'      => $this->generateCsrfToken()
        ]);
    }

    /**
     * Traite la mise à jour (POST)
     */
    public function updateProfile() {
        $this->requireLogin();
        $this->verifyCsrfToken();

        $userId = $this->currentUserId();
        
        // Préparation des données
        $data = [
            'username' => $this->sanitize($_POST['username'] ?? ''),
            'email'    => $this->sanitize($_POST['email'] ?? ''),
            'bio'      => $this->sanitize($_POST['bio'] ?? '')
        ];

        // On gère l'avatar si une nouvelle image est envoyée
        $avatar = $this->handleUpload('avatar');
        if ($avatar) {
            $data['avatar'] = $avatar;
        }

        // On gère le changement de mot de passe (si rempli)
        if (!empty($_POST['password'])) {
            $data['password'] = $_POST['password'];
        }

        // Mise à jour en base de données
        if ($this->userModel->update($userId, $data)) {
            // Mise à jour des informations en session pour que le changement soit immédiat
            $_SESSION['username'] = $data['username'];
            if (isset($data['avatar'])) $_SESSION['user']['avatar'] = $data['avatar'];
            
            $this->setFlash('success', 'Profil mis à jour.');
            $this->redirect('index.php?page=profile');
        } else {
            $this->setFlash('error', 'Aucun changement effectué.');
            $this->redirect('index.php?page=edit-profile');
        }
    }

    // Supprimer son propre compte (Action grave !)
    public function deleteAccount() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrfToken();
            
            $userId = $this->currentUserId();
            $password = $_POST['password'] ?? '';
            $user = $this->userModel->findById($userId);

            // Pour supprimer, l'utilisateur doit confirmer avec son mot de passe actuel
            if (password_verify($password, $user['password'])) {
                $this->userModel->delete($userId);
                
                // Déconnexion forcée
                session_unset();
                session_destroy();
                
                session_start();
                $this->setFlash('success', 'Votre compte a été définitivement supprimé.');
                $this->redirect('index.php');
            } else {
                $this->setFlash('error', 'Mot de passe incorrect.');
                $this->redirect('index.php?page=edit-profile');
            }
        }
    }
}
