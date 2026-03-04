<!-- ═══════════════════════════════════════════════════
     HOME — Landing Page
     ═══════════════════════════════════════════════════ -->

<!-- Hero -->
<section class="hero" id="hero">
  <div class="hero__glow hero__glow--1"></div>
  <div class="hero__glow hero__glow--2"></div>
  <div class="hero__glow hero__glow--3"></div>

  <div class="hero__eyebrow" style="position:relative;">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
    Plateforme de défis créatifs
  </div>

  <h1 class="hero__title" style="position:relative;">
    Relevez des défis.<br>
    <span class="gradient-text">Montrez votre talent.</span>
  </h1>

  <p class="hero__subtitle" style="position:relative;">
    Rejoignez une communauté de créateurs passionnés. Publiez vos défis, 
    participez, commentez et votez pour les meilleures réalisations.
  </p>

  <div class="hero__actions" style="position:relative;">
    <a href="<?= BASE_URL ?>/index.php?page=challenges" class="btn btn-primary btn-lg" id="hero-browse">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      Explorer les défis
    </a>
    <?php if (!isset($_SESSION['user_id'])): ?>
    <a href="<?= BASE_URL ?>/index.php?page=register" class="btn btn-ghost btn-lg" id="hero-register">
      Créer un compte
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
    </a>
    <?php else: ?>
    <a href="<?= BASE_URL ?>/index.php?page=challenge-create" class="btn btn-ghost btn-lg" id="hero-create">
      Créer un défi
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    </a>
    <?php endif; ?>
  </div>

  <div class="hero__stats" style="position:relative;">
    <div class="hero__stat">
      <div class="hero__stat-value"><?= $totalUsers ?></div>
      <div class="hero__stat-label">Membres actifs</div>
    </div>
    <div class="hero__stat">
      <div class="hero__stat-value"><?= count($recentChallenges) ?>+</div>
      <div class="hero__stat-label">Défis publiés</div>
    </div>
    <div class="hero__stat">
      <div class="hero__stat-value"><?= count($topSubmissions) ?>+</div>
      <div class="hero__stat-label">Participations</div>
    </div>
  </div>
</section>

<!-- How It Works -->
<section class="section" id="how-it-works">
  <div class="container">
    <div class="section-heading text-center">
      <h2>Comment ça marche ?</h2>
      <p>Trois étapes simples pour rejoindre la communauté</p>
    </div>

    <div class="grid grid-3 gap-md">
      <?php
      $steps = [
        ['🚀', 'Créez un compte', 'Inscrivez-vous en quelques secondes et personnalisez votre profil.'],
        ['⚡', 'Lancez un défi', 'Publiez vos défis créatifs et définissez les règles du jeu.'],
        ['🏆', 'Votez & Gagnez', 'Participez aux défis, commentez et votez pour les meilleurs.'],
      ];
      foreach ($steps as $i => [$icon, $title, $desc]):
      ?>
      <div class="card slide-up" style="text-align:center; padding:2rem; animation-delay:<?= $i * .1 ?>s">
        <div style="font-size:3rem; margin-bottom:1rem"><?= $icon ?></div>
        <h3 style="font-size:1.1rem; font-weight:700; margin-bottom:.5rem"><?= $title ?></h3>
        <p class="text-muted" style="font-size:.9rem"><?= $desc ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Recent Challenges -->
<?php if (!empty($recentChallenges)): ?>
<section class="section" id="recent-challenges" style="background: var(--clr-surface); border-top:1px solid var(--clr-border); border-bottom:1px solid var(--clr-border);">
  <div class="container">
    <div class="section-heading flex justify-between items-center flex-wrap gap-2">
      <div>
        <h2>Défis récents</h2>
        <p>Les dernières créations de la communauté</p>
      </div>
      <a href="<?= BASE_URL ?>/index.php?page=challenges" class="btn btn-ghost btn-sm" id="home-see-all">Voir tous →</a>
    </div>

    <div class="grid grid-3 gap-md">
      <?php foreach ($recentChallenges as $ch): ?>
      <article class="card fade-in">
        <?php if (!empty($ch['image'])): ?>
          <img src="<?= UPLOAD_URL . htmlspecialchars($ch['image']) ?>" alt="<?= htmlspecialchars($ch['title']) ?>" class="card__media">
        <?php else: ?>
          <div class="card__media-placeholder">⚡</div>
        <?php endif; ?>

        <div class="card__body">
          <span class="card__category"><?= htmlspecialchars($ch['category']) ?></span>
          <h3 class="card__title">
            <a href="<?= BASE_URL ?>/index.php?page=challenge-show&id=<?= $ch['id'] ?>" style="color:inherit">
              <?= htmlspecialchars($ch['title']) ?>
            </a>
          </h3>
          <p class="card__desc"><?= htmlspecialchars($ch['description']) ?></p>
        </div>

        <div class="card__footer">
          <div class="card__meta">
            <?php if (!empty($ch['avatar'])): ?>
              <img src="<?= UPLOAD_URL . htmlspecialchars($ch['avatar']) ?>" alt="avatar" class="avatar-sm">
            <?php else: ?>
              <div class="avatar-placeholder sm"><?= strtoupper(substr($ch['username'], 0, 1)) ?></div>
            <?php endif; ?>
            <span><?= htmlspecialchars($ch['username']) ?></span>
          </div>
          <a href="<?= BASE_URL ?>/index.php?page=challenge-show&id=<?= $ch['id'] ?>" class="btn btn-primary btn-sm">
            Voir le défi →
          </a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Top Submissions -->
<?php if (!empty($topSubmissions)): ?>
<section class="section" id="top-submissions">
  <div class="container">
    <div class="section-heading flex justify-between items-center flex-wrap gap-2">
      <div>
        <h2>🏆 Meilleures participations</h2>
        <p>Les créations les plus votées par la communauté</p>
      </div>
      <a href="<?= BASE_URL ?>/index.php?page=leaderboard" class="btn btn-ghost btn-sm" id="home-see-leaderboard">Classement complet →</a>
    </div>

    <div class="grid grid-3 gap-md">
      <?php foreach ($topSubmissions as $i => $sub): ?>
      <article class="submission-card fade-in">
        <?php if (!empty($sub['image'])): ?>
          <img src="<?= UPLOAD_URL . htmlspecialchars($sub['image']) ?>" alt="submission" class="card__media">
        <?php else: ?>
          <div class="card__media-placeholder" style="aspect-ratio:16/9">🎨</div>
        <?php endif; ?>

        <div class="card__body">
          <div class="flex items-center gap-1 mb-2">
            <span class="rank-badge rank-<?= $i < 3 ? $i + 1 : 'n' ?>"><?= $i + 1 ?></span>
            <span class="badge badge-primary">
              <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
              <?= (int)$sub['vote_count'] ?> votes
            </span>
          </div>
          <p class="card__desc"><?= htmlspecialchars($sub['description']) ?></p>
          <p class="text-dim" style="font-size:.78rem; margin-top:.5rem">
            Pour : <a href="<?= BASE_URL ?>/index.php?page=challenge-show&id=<?= $sub['challenge_id'] ?>" style="color:var(--clr-primary-l)"><?= htmlspecialchars($sub['challenge_title']) ?></a>
          </p>
        </div>

        <div class="card__footer">
          <div class="card__meta">
            <?php if (!empty($sub['avatar'])): ?>
              <img src="<?= UPLOAD_URL . htmlspecialchars($sub['avatar']) ?>" alt="avatar" class="avatar-sm">
            <?php else: ?>
              <div class="avatar-placeholder sm"><?= strtoupper(substr($sub['username'], 0, 1)) ?></div>
            <?php endif; ?>
            <?= htmlspecialchars($sub['username']) ?>
          </div>
          <a href="<?= BASE_URL ?>/index.php?page=submission-show&id=<?= $sub['id'] ?>" class="btn btn-ghost btn-sm">Voir →</a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Call to Action -->
<?php if (!isset($_SESSION['user_id'])): ?>
<section class="section" style="background:var(--clr-surface); border-top:1px solid var(--clr-border);">
  <div class="container text-center">
    <div style="max-width:600px; margin:0 auto;">
      <h2 class="section-heading h2" style="font-family:var(--font-head); font-size:2.5rem; font-weight:800; margin-bottom:1rem;">
        Prêt à relever le défi ?
      </h2>
      <p class="text-muted" style="margin-bottom:2rem;">
        Rejoignez des centaines de créateurs passionnés et montrez ce dont vous êtes capable.
      </p>
      <a href="<?= BASE_URL ?>/index.php?page=register" class="btn btn-primary btn-lg" id="cta-register">
        Rejoindre ChallengeHub gratuitement →
      </a>
    </div>
  </div>
</section>
<?php endif; ?>
