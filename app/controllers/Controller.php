<?php

abstract class Controller {

    // ─── View Rendering ──────────────────────────────────────
    protected function render(string $view, array $data = []): void {
        extract($data, EXTR_SKIP);
        $viewFile = ROOT_PATH . '/app/views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewFile)) {
            die("View not found: {$viewFile}");
        }

        require ROOT_PATH . '/app/views/layout/header.php';
        require $viewFile;
        require ROOT_PATH . '/app/views/layout/footer.php';
    }

    protected function renderPartial(string $view, array $data = []): void {
        extract($data, EXTR_SKIP);
        $viewFile = ROOT_PATH . '/app/views/' . str_replace('.', '/', $view) . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        }
    }

    // ─── Redirect ─────────────────────────────────────────────
    protected function redirect(string $url): void {
        header('Location: ' . $url);
        exit;
    }

    // ─── Session Helpers ─────────────────────────────────────
    protected function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    protected function requireLogin(): void {
        if (!$this->isLoggedIn()) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Vous devez être connecté pour accéder à cette page.'];
            $this->redirect(BASE_URL . '/index.php?page=login');
        }
    }

    protected function currentUserId(): ?int {
        return $_SESSION['user_id'] ?? null;
    }

    protected function currentUser(): ?array {
        return $_SESSION['user'] ?? null;
    }

    // ─── Flash Messages ────────────────────────────────────────
    protected function setFlash(string $type, string $message): void {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    // ─── CSRF ────────────────────────────────────────────────
    protected function generateCsrfToken(): string {
        if (empty($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }

    protected function verifyCsrfToken(): void {
        $token = $_POST[CSRF_TOKEN_NAME] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!hash_equals($_SESSION[CSRF_TOKEN_NAME] ?? '', $token)) {
            http_response_code(403);
            $this->setFlash('error', 'Requête invalide (CSRF).');
            $this->redirect($_SERVER['HTTP_REFERER'] ?? BASE_URL . '/index.php');
        }
    }

    // ─── Input Sanitization ───────────────────────────────────
    protected function sanitize(string $value): string {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
    }

    protected function post(string $key, string $default = ''): string {
        return isset($_POST[$key]) ? $this->sanitize($_POST[$key]) : $default;
    }

    protected function get(string $key, string $default = ''): string {
        return isset($_GET[$key]) ? $this->sanitize($_GET[$key]) : $default;
    }

    // ─── JSON Response ────────────────────────────────────────
    protected function json(array $data, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // ─── File Upload ─────────────────────────────────────────
    protected function handleUpload(string $inputName, string $subfolder = ''): string|null {
        if (empty($_FILES[$inputName]['tmp_name'])) return null;

        $file     = $_FILES[$inputName];
        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, ALLOWED_TYPES)) {
            $this->setFlash('error', 'Type de fichier non autorisé.');
            return null;
        }

        if ($file['size'] > MAX_FILE_SIZE) {
            $this->setFlash('error', 'Fichier trop volumineux (max 5 Mo).');
            return null;
        }

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('img_', true) . '.' . strtolower($ext);
        $dir      = UPLOAD_DIR . ($subfolder ? rtrim($subfolder, '/') . '/' : '');

        if (!is_dir($dir)) mkdir($dir, 0755, true);

        if (move_uploaded_file($file['tmp_name'], $dir . $filename)) {
            return ($subfolder ? rtrim($subfolder, '/') . '/' : '') . $filename;
        }

        return null;
    }
}
