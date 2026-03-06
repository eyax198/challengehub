<!-- Page d'accueil du site -->

<!-- Hero (Section principale) -->
<section class="hero">
  <!-- Effets de lumière décoratifs -->
  <div class="hero__glow hero__glow--1"></div>
  <div class="hero__glow hero__glow--2"></div>

  <div class="hero__eyebrow">
    ⚡ Plateforme de défis créatifs
  </div>

  <h1 class="hero__title">
    Relevez des défis.<br>
    <span class="gradient-text">Montrez votre talent.</span>
  </h1>

  <p class="hero__subtitle">
    Rejoignez une communauté de créateurs passionnés. Publiez vos défis, 
    participez, commentez et votez pour les meilleures réalisations.
  </p>

  <div class="hero__actions">
    <a href="index.php?page=challenges" class="btn btn-primary btn-lg">
      🔍 Explorer les défis
    </a>
    <?php if (!isset($_SESSION['user_id'])): ?>
      <a href="index.php?page=register" class="btn btn-ghost btn-lg">
        Créer un compte
      </a>
    <?php else: ?>
      <a href="index.php?page=challenge-create" class="btn btn-ghost btn-lg">
        + Initier un défi
      </a>
    <?php endif; ?>
  </div>

  <!-- Quelques chiffres sur la plateforme -->
  <div class="hero__stats">
    <div class="hero__stat">
      <div class="hero__stat-value"><?= (int)$totalUsers ?></div>
      <div class="hero__stat-label">Utilisateurs</div>
    </div>
    <div class="hero__stat">
      <div class="hero__stat-value"><?= count($recentChallenges) ?></div>
      <div class="hero__stat-label">Défis publiés</div>
    </div>
    <div class="hero__stat">
      <div class="hero__stat-value"><?= count($topSubmissions) ?></div>
      <div class="hero__stat-label">Participations</div>
    </div>
  </div>
</section>

<!-- SECTION : Comment ça marche ? -->
<section class="section">
  <div class="container text-center">
    <h2 class="section-heading">Comment ça marche ?</h2>
    <p class="text-muted">Trois étapes simples pour rejoindre la communauté</p>
    
    <div class="grid grid-3" style="margin-top:2rem;">
      <div class="card" style="padding:2rem;">
        <div style="font-size:3rem;">🚀</div>
        <h3>Inscrivez-vous</h3>
        <p class="text-muted">Créez votre profil en quelques secondes.</p>
      </div>
      <div class="card" style="padding:2rem;">
        <div style="font-size:3rem;">🏗️</div>
        <h3>Relevez des défis</h3>
        <p class="text-muted">Participez aux challenges ou proposez les vôtres.</p>
      </div>
      <div class="card" style="padding:2rem;">
        <div style="font-size:3rem;">🏆</div>
        <h3>Gagnez des votes</h3>
        <p class="text-muted">Obtenez un maximum de points pour monter au classement.</p>
      </div>
    </div>
  </div>
</section>

<!-- On affiche les derniers défis ici -->
<?php if (!empty($recentChallenges)): ?>
<section class="section">
  <div class="container">
    <div class="section-heading flex justify-between items-center">
      <h2>🆕 Derniers défis</h2>
      <a href="index.php?page=challenges" class="btn btn-ghost btn-sm">Voir tout</a>
    </div>

    <div class="grid grid-3">
      <?php foreach ($recentChallenges as $ch): ?>
      <article class="card">
        <?php if (!empty($ch['image'])): ?>
          <img src="public/images/uploads/<?= htmlspecialchars($ch['image']) ?>" alt="Défi" class="card__media">
        <?php else: ?>
          <div class="card__media-placeholder">⚡</div>
        <?php endif; ?>

        <div class="card__body">
          <span class="card__category"><?= htmlspecialchars($ch['category']) ?></span>
          <h3 class="card__title">
            <a href="index.php?page=challenge-show&id=<?= $ch['id'] ?>">
              <?= htmlspecialchars($ch['title']) ?>
            </a>
          </h3>
          <p class="card__desc"><?= htmlspecialchars($ch['description']) ?></p>
        </div>

        <div class="card__footer">
          <div class="card__meta">
             👤 <?= htmlspecialchars($ch['username']) ?>
          </div>
          <a href="index.php?page=challenge-show&id=<?= $ch['id'] ?>" class="btn btn-primary btn-sm">Voir →</a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
