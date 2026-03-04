<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="ChallengeHub — Plateforme collaborative de défis créatifs. Créez, participez et votez pour les meilleures créations.">
  <title><?= htmlspecialchars($pageTitle ?? APP_NAME) ?></title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Syne:wght@700;800&display=swap" rel="stylesheet">

  <!-- Styles -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">

  <!-- Favicon (emoji as SVG) -->
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚡</text></svg>">
</head>
<body>

<!-- ── Navigation ─────────────────────────────────────────── -->
<nav class="navbar" id="navbar" role="navigation" aria-label="Navigation principale">
  <div class="navbar__inner">

    <a href="<?= BASE_URL ?>/index.php?page=home" class="navbar__logo" id="nav-logo">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" fill="url(#bolt)" stroke="none"/>
        <defs>
          <linearGradient id="bolt" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#7c3aed"/>
            <stop offset="100%" style="stop-color:#e879f9"/>
          </linearGradient>
        </defs>
      </svg>
      ChallengeHub
    </a>

    <div class="navbar__links" id="nav-links">
      <a href="<?= BASE_URL ?>/index.php?page=home" class="navbar__link" id="nav-home">Accueil</a>
      <a href="<?= BASE_URL ?>/index.php?page=challenges" class="navbar__link" id="nav-challenges">Défis</a>
      <a href="<?= BASE_URL ?>/index.php?page=leaderboard" class="navbar__link" id="nav-leaderboard">Classement</a>
    </div>

    <div class="navbar__actions">
      <?php if (isset($_SESSION['user_id'])): ?>
        <?php $navUser = $_SESSION['user']; ?>
        <a href="<?= BASE_URL ?>/index.php?page=challenge-create" class="btn btn-primary btn-sm" id="nav-create-challenge">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Créer un défi
        </a>
        <a href="<?= BASE_URL ?>/index.php?page=profile" class="flex items-center gap-1" id="nav-profile" title="Mon profil">
          <?php if (!empty($navUser['avatar'])): ?>
            <img src="<?= UPLOAD_URL . htmlspecialchars($navUser['avatar']) ?>" alt="Avatar" class="navbar__avatar">
          <?php else: ?>
            <div class="navbar__avatar-placeholder"><?= strtoupper(substr($navUser['username'], 0, 1)) ?></div>
          <?php endif; ?>
        </a>
        <a href="<?= BASE_URL ?>/index.php?page=logout" class="btn btn-ghost btn-sm" id="nav-logout">Déconnexion</a>
      <?php else: ?>
        <a href="<?= BASE_URL ?>/index.php?page=login"    class="btn btn-ghost btn-sm"   id="nav-login">Connexion</a>
        <a href="<?= BASE_URL ?>/index.php?page=register" class="btn btn-primary btn-sm" id="nav-register">Inscription</a>
      <?php endif; ?>
    </div>

    <button class="navbar__hamburger" id="nav-hamburger" aria-label="Ouvrir le menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

<!-- ── Flash Messages ─────────────────────────────────────── -->
<?php if (!empty($_SESSION['flash'])): ?>
  <?php $flash = $_SESSION['flash']; unset($_SESSION['flash']); ?>
  <div class="container" style="padding-top: 1rem;">
    <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>" role="alert" id="flash-message">
      <span style="font-size:1.2rem"><?= $flash['type'] === 'success' ? '✅' : ($flash['type'] === 'error' ? '❌' : '⚠️') ?></span>
      <?= htmlspecialchars($flash['message']) ?>
    </div>
  </div>
<?php endif; ?>

<!-- ── Form Errors ───────────────────────────────────────── -->
<?php if (!empty($_SESSION['form_errors'])): ?>
  <?php $formErrors = $_SESSION['form_errors']; unset($_SESSION['form_errors']); ?>
  <div class="container" style="padding-top: 1rem;">
    <div class="alert alert-error" role="alert" id="form-errors">
      <div>
        <strong>Erreurs dans le formulaire :</strong>
        <ul style="margin-top:.4rem; padding-left:1.2rem; list-style:disc;">
          <?php foreach ($formErrors as $err): ?>
            <li><?= htmlspecialchars($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
<?php endif; ?>

<main id="main-content">
