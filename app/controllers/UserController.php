<?php

require_once __DIR__ . '/Controller.php';
require_once ROOT_PATH . '/app/models/User.php';

class UserController extends Controller {

    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // ── GET /profile?id=X ────────────────────────────────────
    public function showProfile(): void {
        $id   = (int) ($_GET['id'] ?? $this->currentUserId());
        $user = $this->userModel->findById('users', $id);

        if (!$user) {
            $this->setFlash('error', 'Utilisateur introuvable.');
            $this->redirect(BASE_URL . '/index.php?page=challenges');
            return;
        }

        // Load user's challenges & submissions
        require_once ROOT_PATH . '/app/models/Challenge.php';
        require_once ROOT_PATH . '/app/models/Submission.php';

        $challengeModel  = new Challenge();
        $submissionModel = new Submission();

        $challenges  = $challengeModel->getByUser($id);
        $submissions = $submissionModel->getByUser($id);
        $stats       = $this->userModel->getStats($id);

        $this->render('user.profile', [
            'pageTitle'   => htmlspecialchars($user['username']) . ' — Profil',
            'profileUser' => $user,
            'challenges'  => $challenges,
            'submissions' => $submissions,
            'stats'       => $stats,
            'csrf'        => $this->generateCsrfToken(),
        ]);
    }

    // ── GET /profile/edit ─────────────────────────────────────
    public function showEditProfile(): void {
        $this->requireLogin();
        $user = $this->userModel->findById('users', $this->currentUserId());
        $csrf = $this->generateCsrfToken();
        $this->render('user.edit_profile', [
            'pageTitle' => 'Modifier le profil',
            'user'      => $user,
            'csrf'      => $csrf,
        ]);
    }

    // ── POST /profile/edit ────────────────────────────────────
    public function updateProfile(): void {
        $this->requireLogin();
        $this->verifyCsrfToken();

        $userId   = $this->currentUserId();
        $username = $this->post('username');
        $email    = $this->post('email');
        $bio      = $this->post('bio');
        $password = $_POST['password'] ?? '';

        $errors = [];

        if (strlen($username) < 3) {
            $errors[] = 'Le nom d\'utilisateur est trop court.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalide.';
        }

        // Check uniqueness only for OTHER users
        $byEmail    = $this->userModel->findByEmail($email);
        $byUsername = $this->userModel->findByUsername($username);

        if ($byEmail && (int)$byEmail['id'] !== $userId) {
            $errors[] = 'Cet email est déjà utilisé.';
        }
        if ($byUsername && (int)$byUsername['id'] !== $userId) {
            $errors[] = 'Ce nom d\'utilisateur est déjà pris.';
        }

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $this->redirect(BASE_URL . '/index.php?page=edit-profile');
            return;
        }

        $data = compact('username', 'email', 'bio');

        if (!empty($password)) {
            if (strlen($password) < 8) {
                $this->setFlash('error', 'Le mot de passe doit comporter au moins 8 caractères.');
                $this->redirect(BASE_URL . '/index.php?page=edit-profile');
                return;
            }
            $data['password'] = $password;
        }

        $avatar = $this->handleUpload('avatar', 'avatars');
        if ($avatar) $data['avatar'] = $avatar;

        $this->userModel->update($userId, $data);

        // Update session
        $_SESSION['user']['username'] = $username;
        $_SESSION['user']['email']    = $email;
        if ($avatar) $_SESSION['user']['avatar'] = $avatar;

        $this->setFlash('success', 'Profil mis à jour avec succès.');
        $this->redirect(BASE_URL . '/index.php?page=profile');
    }

    // ── POST /profile/delete ──────────────────────────────────
    public function deleteAccount(): void {
        $this->requireLogin();
        $this->verifyCsrfToken();

        $userId   = $this->currentUserId();
        $password = $_POST['password'] ?? '';
        $user     = $this->userModel->findById('users', $userId);

        if (!password_verify($password, $user['password'])) {
            $this->setFlash('error', 'Mot de passe incorrect. Suppression annulée.');
            $this->redirect(BASE_URL . '/index.php?page=edit-profile');
            return;
        }

        $this->userModel->delete($userId);

        session_unset();
        session_destroy();

        $this->setFlash('success', 'Votre compte a été supprimé.');
        $this->redirect(BASE_URL . '/index.php?page=home');
    }
}
