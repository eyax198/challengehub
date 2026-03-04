<?php
$isOwner       = isset($_SESSION['user_id']) && (int)$challenge['user_id'] === (int)$_SESSION['user_id'];
$deadline      = new DateTime($challenge['deadline']);
$now           = new DateTime();
$isExpired     = $deadline < $now;
$daysRemaining = $now->diff($deadline)->days;
?>

<div class="page-header">
  <div class="page-header__inner">
    <div class="flex items-center gap-2 flex-wrap" style="margin-bottom:.75rem">
      <span class="badge badge-primary"><?= htmlspecialchars($challenge['category']) ?></span>
      <?php if ($isExpired): ?>
        <span class="badge badge-danger">⏰ Expiré</span>
      <?php elseif ($daysRemaining <= 3): ?>
        <span class="badge badge-warning">⚡ J-<?= $daysRemaining ?></span>
      <?php else: ?>
        <span class="badge badge-success">✅ Actif — J-<?= $daysRemaining ?></span>
      <?php endif; ?>
    </div>
    <h1><?= htmlspecialchars($challenge['title']) ?></h1>
    <div class="flex items-center gap-2" style="margin-top:.75rem; flex-wrap:wrap">
      <div class="card__meta">
        <?php if (!empty($challenge['avatar'])): ?>
          <img src="<?= UPLOAD_URL . htmlspecialchars($challenge['avatar']) ?>" class="avatar-sm" alt="">
        <?php else: ?>
          <div class="avatar-placeholder sm"><?= strtoupper(substr($challenge['username'], 0, 1)) ?></div>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/index.php?page=profile&id=<?= $challenge['user_id'] ?>" style="color:var(--clr-primary-l)">
          <?= htmlspecialchars($challenge['username']) ?>
        </a>
      </div>
      <span class="text-dim" style="font-size:.8rem">
        · Publié le <?= (new DateTime($challenge['created_at']))->format('d/m/Y') ?>
        · Date limite : <?= $deadline->format('d/m/Y') ?>
      </span>
    </div>
  </div>
</div>

<div class="container section">
  <div style="display:grid; grid-template-columns:1fr 340px; gap:2rem; align-items:start;">

    <!-- Main Content -->
    <div>

      <?php if (!empty($challenge['image'])): ?>
        <img src="<?= UPLOAD_URL . htmlspecialchars($challenge['image']) ?>" alt="Image du défi" style="width:100%; border-radius:var(--radius-lg); margin-bottom:1.5rem; max-height:400px; object-fit:cover;">
      <?php endif; ?>

      <div class="glass-panel" style="padding:1.75rem; margin-bottom:2rem;">
        <h2 style="font-size:1.1rem; font-weight:700; margin-bottom:1rem;">📋 Description du défi</h2>
        <p style="color:var(--clr-text-muted); line-height:1.75; white-space:pre-wrap;"><?= htmlspecialchars($challenge['description']) ?></p>
      </div>

      <!-- Submissions -->
      <div style="margin-bottom:1.5rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
        <h2 style="font-size:1.4rem; font-weight:800;">💡 Participations (<?= count($submissions) ?>)</h2>
        <div class="flex gap-1">
          <?php foreach (['newest' => 'Récentes', 'oldest' => 'Anciennes', 'top' => 'Meilleures'] as $key => $label): ?>
            <a href="?page=challenge-show&id=<?= $challenge['id'] ?>&sort=<?= $key ?>"
               class="btn btn-sm <?= $sort === $key ? 'btn-primary' : 'btn-ghost' ?>">
              <?= $label ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

      <?php if (empty($submissions)): ?>
        <div class="empty-state">
          <div class="empty-state__icon">🎨</div>
          <h3>Aucune participation pour l'instant</h3>
          <p>Soyez le premier à relever ce défi !</p>
        </div>
      <?php else: ?>
        <div class="grid grid-1 gap-md" id="submissions-list">
          <?php foreach ($submissions as $sub): ?>
          <div class="submission-card" id="submission-<?= $sub['id'] ?>">
            <div style="display:flex; gap:1rem; padding:1.25rem; flex-wrap:wrap;">

              <?php if (!empty($sub['image'])): ?>
                <img src="<?= UPLOAD_URL . htmlspecialchars($sub['image']) ?>" alt="" style="width:160px; height:120px; object-fit:cover; border-radius:var(--radius-md); flex-shrink:0;">
              <?php endif; ?>

              <div style="flex:1; min-width:200px;">
                <div class="flex items-center gap-1 mb-2">
                  <?php if (!empty($sub['avatar'])): ?>
                    <img src="<?= UPLOAD_URL . htmlspecialchars($sub['avatar']) ?>" class="avatar-sm" alt="">
                  <?php else: ?>
                    <div class="avatar-placeholder sm"><?= strtoupper(substr($sub['username'], 0, 1)) ?></div>
                  <?php endif; ?>
                  <a href="<?= BASE_URL ?>/index.php?page=profile&id=<?= $sub['user_id'] ?>" style="font-weight:600; font-size:.875rem; color:var(--clr-primary-l)">
                    <?= htmlspecialchars($sub['username']) ?>
                  </a>
                  <span class="text-dim" style="font-size:.75rem">· <?= (new DateTime($sub['created_at']))->format('d/m/Y') ?></span>
                </div>

                <p style="font-size:.9rem; color:var(--clr-text-muted); margin-bottom:.75rem; line-height:1.55;">
                  <?= htmlspecialchars(mb_substr($sub['description'], 0, 200)) ?><?= strlen($sub['description']) > 200 ? '...' : '' ?>
                </p>

                <?php if (!empty($sub['link'])): ?>
                  <a href="<?= htmlspecialchars($sub['link']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-ghost btn-sm" style="margin-bottom:.75rem;">
                    🔗 Voir le projet
                  </a>
                <?php endif; ?>

                <div class="flex items-center gap-2 flex-wrap">
                  <!-- Vote Button -->
                  <?php if (isset($_SESSION['user_id'])): ?>
                    <button
                      class="vote-btn <?= ($userVotes[$sub['id']] ?? false) ? 'voted' : '' ?>"
                      id="vote-btn-<?= $sub['id'] ?>"
                      data-submission-id="<?= $sub['id'] ?>"
                      data-csrf="<?= htmlspecialchars($csrf) ?>"
                      onclick="castVote(this)"
                    >
                      <svg width="14" height="14" fill="<?= ($userVotes[$sub['id']] ?? false) ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                      <span id="vote-count-<?= $sub['id'] ?>"><?= (int)$sub['vote_count'] ?></span>
                    </button>
                  <?php else: ?>
                    <span class="badge badge-primary">
                      ⭐ <?= (int)$sub['vote_count'] ?> votes
                    </span>
                  <?php endif; ?>

                  <span class="badge badge-accent">
                    💬 <?= (int)$sub['comment_count'] ?> commentaire<?= $sub['comment_count'] > 1 ? 's' : '' ?>
                  </span>

                  <a href="<?= BASE_URL ?>/index.php?page=submission-show&id=<?= $sub['id'] ?>" class="btn btn-ghost btn-sm">
                    Voir en détail →
                  </a>

                  <?php if (isset($_SESSION['user_id']) && (int)$sub['user_id'] === (int)$_SESSION['user_id']): ?>
                    <a href="<?= BASE_URL ?>/index.php?page=submission-edit&id=<?= $sub['id'] ?>" class="btn btn-ghost btn-sm">✏️</a>
                    <form method="POST" action="<?= BASE_URL ?>/index.php?page=submission-delete" style="display:inline;" onsubmit="return confirm('Supprimer cette participation ?')">
                      <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= htmlspecialchars($csrf) ?>">
                      <input type="hidden" name="id" value="<?= $sub['id'] ?>">
                      <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
                    </form>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </div>

    <!-- Sidebar -->
    <aside>

      <!-- Actions -->
      <div class="glass-panel" style="padding:1.5rem; margin-bottom:1.5rem;">
        <h3 style="font-size:1rem; font-weight:700; margin-bottom:1rem;">Actions</h3>

        <?php if ($isOwner): ?>
          <div class="flex flex-col gap-1">
            <a href="<?= BASE_URL ?>/index.php?page=challenge-edit&id=<?= $challenge['id'] ?>" class="btn btn-ghost btn-sm" id="btn-edit-challenge">✏️ Modifier le défi</a>
            <form method="POST" action="<?= BASE_URL ?>/index.php?page=challenge-delete" onsubmit="return confirm('Supprimer ce défi et toutes ses participations ?')">
              <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= htmlspecialchars($csrf) ?>">
              <input type="hidden" name="id" value="<?= $challenge['id'] ?>">
              <button type="submit" class="btn btn-danger btn-sm" style="width:100%;" id="btn-delete-challenge">🗑️ Supprimer le défi</button>
            </form>
          </div>
        <?php elseif (!isset($_SESSION['user_id'])): ?>
          <p class="text-muted" style="font-size:.875rem; margin-bottom:.75rem">Connectez-vous pour participer.</p>
          <a href="<?= BASE_URL ?>/index.php?page=login" class="btn btn-primary btn-sm" style="width:100%;" id="btn-login-to-participate">Se connecter</a>
        <?php elseif ($alreadySubmitted): ?>
          <div class="alert alert-info" style="font-size:.85rem;">✅ Vous avez déjà participé à ce défi.</div>
        <?php elseif ($isExpired): ?>
          <div class="alert alert-warning" style="font-size:.85rem;">⏰ Ce défi est expiré.</div>
        <?php else: ?>
          <!-- Submit form -->
          <form method="POST" action="<?= BASE_URL ?>/index.php?page=submission-create" enctype="multipart/form-data" id="submit-form">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="challenge_id" value="<?= $challenge['id'] ?>">

            <div class="form-group mb-2">
              <label class="form-label" for="sub-description">Ma participation</label>
              <textarea id="sub-description" name="description" class="form-control" placeholder="Décrivez votre participation..." rows="4" required minlength="10"></textarea>
            </div>

            <div class="form-group mb-2">
              <label class="form-label" for="sub-link">Lien externe (optionnel)</label>
              <input type="url" id="sub-link" name="link" class="form-control" placeholder="https://...">
            </div>

            <div class="form-group mb-3">
              <label class="form-label" for="sub-image">Image (optionnel)</label>
              <input type="file" id="sub-image" name="image" class="form-control" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;" id="btn-submit-participation">
              🚀 Soumettre ma participation
            </button>
          </form>
        <?php endif; ?>
      </div>

      <!-- Stats -->
      <div class="glass-panel" style="padding:1.5rem;">
        <h3 style="font-size:1rem; font-weight:700; margin-bottom:1rem;">📊 Statistiques</h3>
        <div class="flex flex-col gap-1">
          <div class="flex justify-between" style="font-size:.875rem;">
            <span class="text-muted">Participations</span>
            <span class="font-bold"><?= count($submissions) ?></span>
          </div>
          <div class="flex justify-between" style="font-size:.875rem;">
            <span class="text-muted">Total votes</span>
            <span class="font-bold"><?= array_sum(array_column($submissions, 'vote_count')) ?></span>
          </div>
          <div class="flex justify-between" style="font-size:.875rem;">
            <span class="text-muted">Date limite</span>
            <span class="font-bold"><?= $deadline->format('d/m/Y') ?></span>
          </div>
        </div>
      </div>

    </aside>
  </div>
</div>

<script>
function castVote(btn) {
    const subId  = btn.dataset.submissionId;
    const csrf   = btn.dataset.csrf;
    const countEl = document.getElementById('vote-count-' + subId);
    const star   = btn.querySelector('svg');

    btn.disabled = true;

    fetch('<?= BASE_URL ?>/index.php?page=vote', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `submission_id=${subId}&<?= CSRF_TOKEN_NAME ?>=${csrf}`
    })
    .then(r => r.json())
    .then(data => {
        countEl.textContent = data.count;
        if (data.action === 'added') {
            btn.classList.add('voted');
            star.setAttribute('fill', 'currentColor');
        } else {
            btn.classList.remove('voted');
            star.setAttribute('fill', 'none');
        }
        btn.disabled = false;
    })
    .catch(() => { btn.disabled = false; });
}
</script>

<style>
@media (max-width: 900px) {
  .container > div[style*="grid-template-columns"] {
    grid-template-columns: 1fr !important;
  }
}
</style>
