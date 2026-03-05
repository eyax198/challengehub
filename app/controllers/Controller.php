<?php

/**
 * Le contrôleur de base (Classe parente)
 * Contient les fonctions partagées par tous les contrôleurs.
 */
abstract class Controller {

    /**
     * Affiche une vue complète (avec header et footer)
     * @param string $view Nom du fichier dans app/views/
     * @param array $data Tableau de variables à passer à la vue
     */
    protected function render($view, $data = []) {
        // Transforme les clés du tableau en variables utilisables dans la vue
        // Ex: ['titre' => 'Accueil'] crée une variable $titre
        extract($data);

        // On remplace les points par des slashs si besoin (ex: 'auth.login' => 'auth/login')
        $view = str_replace('.', '/', $view);
        $viewFile = ROOT_PATH . '/app/views/' . $view . '.php';

        if (file_exists($viewFile)) {
            require ROOT_PATH . '/app/views/layout/header.php';
            require $viewFile;
            require ROOT_PATH . '/app/views/layout/footer.php';
        } else {
            die("Erreur : La vue '{$view}' n'existe pas.");
        }
    }

    /**
     * Affiche juste un morceau de vue (utilisé pour l'AJAX ou les petits composants)
     */
    protected function renderPartial($view, $data = []) {
        extract($data);
        $view = str_replace('.', '/', $view);
        $viewFile = ROOT_PATH . '/app/views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        }
    }

    /**
     * Redirige vers une autre page
     */
    protected function redirect($url) {
        header('Location: ' . $url);
        exit();
    }

    /**
     * Vérifie si l'utilisateur est connecté via la session
     */
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Interdit l'accès aux utilisateurs non connectés
     */
    protected function requireLogin() {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            $this->redirect('index.php?page=login');
        }
    }

    /**
     * Récupère l'ID de l'utilisateur en session
     */
    protected function currentUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Enregistre un message flash (pour afficher une notification après une action)
     */
    protected function setFlash($type, $message) {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    /**
     * Nettoie une chaîne de caractères contre les failles XSS
     */
    protected function sanitize($value) {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Génère un jeton de sécurité CSRF pour les formulaires
     */
    protected function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = md5(uniqid(rand(), true));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Vérifie le jeton de sécurité CSRF lors de l'envoi d'un formulaire
     */
    protected function verifyCsrfToken() {
        $token = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            $this->setFlash('error', 'Erreur de sécurité (CSRF).');
            $this->redirect('index.php');
        }
    }

    /**
     * Gère l'upload d'un fichier (image)
     * @param string $inputName Nom du champ <input type="file">
     * @return string|null Nom du fichier enregistré ou null
     */
    protected function handleUpload($inputName) {
        if (empty($_FILES[$inputName]['tmp_name'])) {
            return null;
        }

        $file = $_FILES[$inputName];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        // Vérification simplifiée de l'extension
        if (!in_array($ext, $allowed)) {
            $this->setFlash('error', 'Format d\'image non autorisé.');
            return null;
        }

        // Création d'un nom unique (timestamp + nom d'origine nettoyé)
        $filename = time() . '_' . preg_replace('/[^a-z0-9.]/', '_', strtolower($file['name']));
        $destination = ROOT_PATH . '/public/images/uploads/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $filename;
        }

        return null;
    }
}
