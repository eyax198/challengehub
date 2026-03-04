<?php $isOwnProfile = isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)$profileUser['id']; ?>

<div class="container section">

  <!-- Profile Header -->
  <div class="profile-header" id="profile-header">
    <?php if (!empty($profileUser['avatar'])): ?>
      <img src="<?= UPLOAD_URL . htmlspecialchars($profileUser['avatar']) ?>" alt="Avatar" class="avatar-lg" style="border:3px solid var(--clr-primary);">
    <?php else: ?>
      <div class="avatar-placeholder lg"><?= strtoupper(substr($profileUser['username'], 0, 1)) ?></div>
    <?php endif; ?>

    <div class="profile-header__info">
      <h1 class="profile-header__name"><?= htmlspecialchars($profileUser['username']) ?></h1>
      <?php if (!empty($profileUser['bio'])): ?>
        <p class="text-muted" style="margin-top:.35rem; font-size:.9rem;"><?= htmlspecialchars($profileUser['bio']) ?></p>
      <?php endif; ?>
      <p class="text-dim" style="font-size:.78rem; margin-top:.5rem;">Membre depuis <?= (new DateTime($profileUser['created_at']))->format('F Y') ?></p>

      <div class="profile-header__stats">
        <div class="profile-stat">
          <div class="profile-stat__value"><?= $stats['challenges'] ?></div>
          <div class="profile-stat__label">Défis</div>
        </div>
        <div class="profile-stat">
          <div class="profile-stat__value"><?= $stats['submissions'] ?></div>
          <div class="profile-stat__label">Participations</div>
        </div>
        <div class="profile-stat">
          <div class="profile-stat__value"><?= $stats['votes_received'] ?></div>
          <div class="profile-stat__label">Votes reçus</div>
        </div>
      </div>
    </div>

    <?php if ($isOwnProfile): ?>
    <div class="flex flex-col gap-1" style="flex-shrink:0;">
      <a href="<?= BASE_URL ?>/index.php?page=edit-profile" class="btn btn-ghost btn-sm" id="btn-edit-profile">✏️ Modifier le profil</a>
      <a href="<?= BASE_URL ?>/index.php?page=challenge-create" class="btn btn-primary btn-sm" id="btn-create-from-profile">+ Créer un défi</a>
    </div>
    <?php endif; ?>
  </div>

  <!-- Tabs -->
  <div class="tabs" id="profile-tabs">
    <button class="tab active" id="tab-challenges" onclick="switchTab('challenges', this)">
      ⚡ Défis créés (<?= count($challenges) ?>)
    </button>
    <button class="tab" id="tab-submissions" onclick="switchTab('submissions', this)">
      🎨 Participations (<?= count($submissions) ?>)
    </button>
  </div>

  <!-- Challenges Tab -->
  <div id="section-challenges">
    <?php if (empty($challenges)): ?>
      <div class="empty-state">
        <div class="empty-state__icon">⚡</div>
        <h3>Aucun défi créé</h3>
        <?php if ($isOwnProfile): ?>
          <p><a href="<?= BASE_URL ?>/index.php?page=challenge-create" style="color:var(--clr-primary-l)">Créez votre premier défi !</a></p>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <div class="grid grid-3 gap-md">
        <?php foreach ($challenges as $ch): ?>
        <article class="card" id="profile-challenge-<?= $ch['id'] ?>">
          <div class="card__body">
            <span class="card__category"><?= htmlspecialchars($ch['category']) ?></span>
            <h3 class="card__title">
              <a href="<?= BASE_URL ?>/index.php?page=challenge-show&id=<?= $ch['id'] ?>" style="color:inherit;">
                <?= htmlspecialchars($ch['title']) ?>
              </a>
            </h3>
            <div class="flex gap-1 mt-2">
              <span class="badge badge-success"><?= (int)$ch['submissions_count'] ?> participation<?= $ch['submissions_count'] > 1 ? 's' : '' ?></span>
            </div>
          </div>
          <div class="card__footer">
            <span class="text-dim" style="font-size:.75rem;"><?= (new DateTime($ch['created_at']))->format('d/m/Y') ?></span>
            <a href="<?= BASE_URL ?>/index.php?page=challenge-show&id=<?= $ch['id'] ?>" class="btn btn-ghost btn-sm">Voir →</a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Submissions Tab (hidden by default) -->
  <div id="section-submissions" style="display:none;">
    <?php if (empty($submissions)): ?>
      <div class="empty-state">
        <div class="empty-state__icon">🎨</div>
        <h3>Aucune participation</h3>
        <p><a href="<?= BASE_URL ?>/index.php?page=challenges" style="color:var(--clr-primary-l)">Explorez les défis →</a></p>
      </div>
    <?php else: ?>
      <div class="grid grid-3 gap-md">
        <?php foreach ($submissions as $sub): ?>
        <article class="submission-card" id="profile-submission-<?= $sub['id'] ?>">
          <div class="card__body">
            <p class="text-dim" style="font-size:.75rem; margin-bottom:.4rem;">
              Pour : <a href="<?= BASE_URL ?>/index.php?page=challenge-show&id=<?= $sub['challenge_id'] ?>" style="color:var(--clr-primary-l);"><?= htmlspecialchars($sub['challenge_title']) ?></a>
            </p>
            <p style="font-size:.875rem; color:var(--clr-text-muted); line-height:1.55;">
              <?= htmlspecialchars(mb_substr($sub['description'], 0, 120)) ?>...
            </p>
            <div class="flex gap-1 mt-2">
              <span class="badge badge-primary">⭐ <?= (int)$sub['vote_count'] ?></span>
            </div>
          </div>
          <div class="card__footer">
            <span class="text-dim" style="font-size:.75rem;"><?= (new DateTime($sub['created_at']))->format('d/m/Y') ?></span>
            <a href="<?= BASE_URL ?>/index.php?page=submission-show&id=<?= $sub['id'] ?>" class="btn btn-ghost btn-sm">Voir →</a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

</div>

<script>
function switchTab(name, btn) {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');

    document.getElementById('section-challenges').style.display  = name === 'challenges'  ? 'block' : 'none';
    document.getElementById('section-submissions').style.display = name === 'submissions' ? 'block' : 'none';
}
</script>
