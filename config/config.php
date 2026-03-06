<?php

// Paramètres globaux de l'app
define('APP_NAME',    'ChallengeHub');
define('APP_VERSION', '1.0');
define('BASE_URL', 'http://localhost:8000'); // À changer si on utilise un autre port
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

// Réglages des sessions
define('SESSION_NAME',     'challengehub_session');
define('SESSION_LIFETIME', 3600 * 24); 

// Gestion des uploads d'images
define('UPLOAD_DIR',      ROOT_PATH . '/public/images/uploads/');
define('UPLOAD_URL',      BASE_URL  . '/public/images/uploads/');
define('MAX_FILE_SIZE',   5 * 1024 * 1024); // Limite à 5 Mo
define('ALLOWED_TYPES',   ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// ─── Pagination ───────────────────────────────────────────────
define('PER_PAGE', 9);

// ─── Security ────────────────────────────────────────────────
define('CSRF_TOKEN_NAME', 'csrf_token');
