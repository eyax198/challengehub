<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? "ChallengeHub") ?></title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- Styles CSS -->
  <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<!-- ── Navigation ─────────────────────────────────────────── -->
<nav class="navbar">
  <div class="navbar__inner">

    <!-- Logo -->
    <a href="index.php?page=home" class="navbar__logo">
      ⚡ ChallengeHub
    </a>

    <!-- Menu Principal -->
    <div class="navbar__links">
      <a href="index.php?page=home" class="navbar__link">Accueil</a>
      <a href="index.php?page=challenges" class="navbar__link">Défis</a>
      <a href="index.php?page=leaderboard" class="navbar__link">Classement</a>
    </div>

    <!-- Actions Utilisateur -->
    <div class="navbar__actions">
      <?php if (isset($_SESSION['user_id'])): ?>
        
        <a href="index.php?page=challenge-create" class="btn btn-primary btn-sm">
          + Créer un défi
        </a>
        
        <a href="index.php?page=profile" class="navbar__link">
          Profil (<?= htmlspecialchars($_SESSION['username']) ?>)
        </a>
        
        <a href="index.php?page=logout" class="btn btn-ghost btn-sm">Déconnexion</a>
        
      <?php else: ?>
        <a href="index.php?page=login" class="btn btn-ghost btn-sm">Connexion</a>
        <a href="index.php?page=register" class="btn btn-primary btn-sm">Inscription</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- On affiche ici les notifications (succès, erreur) si il y en a -->
<?php if (isset($_SESSION['flash'])): ?>
  <div class="container" style="padding-top: 1rem;">
    <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?>">
      <?= htmlspecialchars($_SESSION['flash']['message']) ?>
    </div>
  </div>
  <?php unset($_SESSION['flash']); // On supprime le message pour pas qu'il revienne au prochain refresh ?>
<?php endif; ?>

<main id="main-content">
