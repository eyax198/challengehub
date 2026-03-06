<?php

// NOTRE CONTRÔLEUR PARENT
// Il contient les outils qu'on utilise dans tous les contrôleurs
abstract class Controller {

    // On affiche une vue (avec header et footer autour)
    protected function render($view, $data = []) {
        // On transforme le tableau $data en variables indépendantes
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

    // Affiche juste un bout de vue (sans header/footer)
    protected function renderPartial($view, $data = []) {
        extract($data);
        $view = str_replace('.', '/', $view);
        $viewFile = ROOT_PATH . '/app/views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        }
    }

    // Petite fonction pour rediriger rapidement
    protected function redirect($url) {
        header('Location: ' . $url);
        exit();
    }

    // Est-ce que l'utilisateur est logué ?
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    // On bloque l'accès si on n'est pas connecté
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

    // Pour mettre un message de succès ou d'erreur temporaire
    protected function setFlash($type, $message) {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    // PROTECTION XSS : On nettoie toutes les entrées de texte
    protected function sanitize($value) {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
    }

    // GÉNÉRATION TOKEN CSRF : Contre les attaques de formulaires forcés
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

    // On gère l'envoi d'images ici
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
