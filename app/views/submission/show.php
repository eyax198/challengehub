<?php $isOwner = isset($_SESSION['user_id']) && (int)$submission['user_id'] === (int)$_SESSION['user_id']; ?>

<div class="page-header">
  <div class="page-header__inner">
    <div style="margin-bottom:.5rem">
      <a href="<?= BASE_URL ?>/index.php?page=challenge-show&id=<?= $submission['challenge_id'] ?>" style="color:var(--clr-primary-l); font-size:.875rem;">
        ← Retour au défi : <?= htmlspecialchars($submission['challenge_title']) ?>
      </a>
    </div>
    <h1>Participation de <?= htmlspecialchars($submission['username']) ?></h1>
    <div class="flex items-center gap-2" style="margin-top:.5rem; flex-wrap:wrap;">
      <span class="badge badge-primary">⭐ <?= (int)$submission['vote_count'] ?> votes</span>
      <span class="badge badge-accent">💬 <?= count($comments) ?> commentaires</span>
    </div>
  </div>
</div>

<div class="container section">
  <div style="display:grid; grid-template-columns:1fr 320px; gap:2rem; align-items:start;">

    <!-- Main -->
    <div>

      <?php if (!empty($submission['image'])): ?>
        <img src="<?= UPLOAD_URL . htmlspecialchars($submission['image']) ?>" alt="Image de la participation"
          style="width:100%; max-height:450px; object-fit:cover; border-radius:var(--radius-lg); margin-bottom:1.5rem;">
      <?php endif; ?>

      <div class="glass-panel" style="padding:1.75rem; margin-bottom:2rem;">
        <p style="color:var(--clr-text-muted); line-height:1.75; white-space:pre-wrap;"><?= htmlspecialchars($submission['description']) ?></p>

        <?php if (!empty($submission['link'])): ?>
          <div style="margin-top:1.25rem;">
            <a href="<?= htmlspecialchars($submission['link']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-ghost btn-sm">
              🔗 Voir le projet externe
            </a>
          </div>
        <?php endif; ?>
      </div>

      <!-- Comments -->
      <div class="glass-panel" style="padding:1.75rem;">
        <h2 style="font-size:1.15rem; font-weight:700; margin-bottom:1.25rem;">💬 Commentaires (<?= count($comments) ?>)</h2>

        <div id="comments-list">
          <?php if (empty($comments)): ?>
            <p class="text-muted" style="font-size:.875rem;" id="no-comments-msg">Aucun commentaire pour l'instant. Soyez le premier !</p>
          <?php else: ?>
            <?php foreach ($comments as $c): ?>
            <div class="comment">
              <div>
                <?php if (!empty($c['avatar'])): ?>
                  <img src="<?= UPLOAD_URL . htmlspecialchars($c['avatar']) ?>" class="avatar-sm" alt="">
                <?php else: ?>
                  <div class="avatar-placeholder sm"><?= strtoupper(substr($c['username'], 0, 1)) ?></div>
                <?php endif; ?>
              </div>
              <div class="comment__body">
                <div class="comment__header">
                  <span class="comment__author"><?= htmlspecialchars($c['username']) ?></span>
                  <span class="comment__date"><?= (new DateTime($c['created_at']))->format('d/m/Y H:i') ?></span>
                </div>
                <p class="comment__text"><?= htmlspecialchars($c['content']) ?></p>
              </div>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <!-- Add Comment -->
        <?php if (isset($_SESSION['user_id'])): ?>
        <div style="margin-top:1.5rem; padding-top:1.5rem; border-top:1px solid var(--clr-border);">
          <div class="form-group">
            <label class="form-label" for="comment-input">Ajouter un commentaire</label>
            <textarea id="comment-input" class="form-control" placeholder="Votre commentaire..." rows="3"></textarea>
          </div>
          <button
            class="btn btn-primary btn-sm"
            id="btn-post-comment"
            style="margin-top:.75rem;"
            data-submission-id="<?= $submission['id'] ?>"
            data-csrf="<?= htmlspecialchars($csrf) ?>"
            onclick="postComment(this)"
          >
            Poster le commentaire
          </button>
        </div>
        <?php else: ?>
          <p class="text-muted" style="font-size:.875rem; margin-top:1.5rem; padding-top:1.5rem; border-top:1px solid var(--clr-border);">
            <a href="<?= BASE_URL ?>/index.php?page=login" style="color:var(--clr-primary-l)">Connectez-vous</a> pour commenter.
          </p>
        <?php endif; ?>
      </div>

    </div>

    <!-- Sidebar -->
    <aside>
      <div class="glass-panel" style="padding:1.5rem; margin-bottom:1.5rem;">
        <h3 style="font-size:.9rem; font-weight:700; margin-bottom:1rem; text-transform:uppercase; letter-spacing:.05em; color:var(--clr-text-dim);">Auteur</h3>
        <div class="flex items-center gap-2" style="margin-bottom:1rem;">
          <?php if (!empty($submission['avatar'])): ?>
            <img src="<?= UPLOAD_URL . htmlspecialchars($submission['avatar']) ?>" class="avatar-md" alt="">
          <?php else: ?>
            <div class="avatar-placeholder md"><?= strtoupper(substr($submission['username'], 0, 1)) ?></div>
          <?php endif; ?>
          <div>
            <a href="<?= BASE_URL ?>/index.php?page=profile&id=<?= $submission['user_id'] ?>" style="font-weight:700; color:var(--clr-primary-l);">
              <?= htmlspecialchars($submission['username']) ?>
            </a>
            <p class="text-dim" style="font-size:.75rem;"><?= (new DateTime($submission['created_at']))->format('d/m/Y') ?></p>
          </div>
        </div>

        <!-- Vote Button -->
        <?php if (isset($_SESSION['user_id']) && !$isOwner): ?>
          <button
            class="vote-btn <?= $hasVoted ? 'voted' : '' ?>"
            id="vote-btn-<?= $submission['id'] ?>"
            data-submission-id="<?= $submission['id'] ?>"
            data-csrf="<?= htmlspecialchars($csrf) ?>"
            onclick="castVote(this)"
            style="width:100%; justify-content:center;"
          >
            <svg width="14" height="14" fill="<?= $hasVoted ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            <?= $hasVoted ? 'Retirer mon vote' : 'Voter pour cette participation' ?>
            (<span id="vote-count-<?= $submission['id'] ?>"><?= (int)$submission['vote_count'] ?></span>)
          </button>
        <?php endif; ?>
      </div>

      <?php if ($isOwner): ?>
      <div class="glass-panel" style="padding:1.5rem;">
        <h3 style="font-size:.9rem; font-weight:700; margin-bottom:1rem; text-transform:uppercase; letter-spacing:.05em; color:var(--clr-text-dim);">Gérer</h3>
        <div class="flex flex-col gap-1">
          <a href="<?= BASE_URL ?>/index.php?page=submission-edit&id=<?= $submission['id'] ?>" class="btn btn-ghost btn-sm" id="btn-edit-submission">✏️ Modifier</a>
          <form method="POST" action="<?= BASE_URL ?>/index.php?page=submission-delete" onsubmit="return confirm('Supprimer cette participation ?')">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="id" value="<?= $submission['id'] ?>">
            <button type="submit" class="btn btn-danger btn-sm" style="width:100%;" id="btn-delete-submission">🗑️ Supprimer</button>
          </form>
        </div>
      </div>
      <?php endif; ?>
    </aside>

  </div>
</div>

<script>
function castVote(btn) {
    const subId   = btn.dataset.submissionId;
    const csrf    = btn.dataset.csrf;
    const countEl = document.getElementById('vote-count-' + subId);

    btn.disabled = true;

    fetch('<?= BASE_URL ?>/index.php?page=vote', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `submission_id=${subId}&<?= CSRF_TOKEN_NAME ?>=${csrf}`
    })
    .then(r => r.json())
    .then(data => {
        countEl.textContent = data.count;
        const star = btn.querySelector('svg');
        if (data.action === 'added') {
            btn.classList.add('voted');
            star.setAttribute('fill', 'currentColor');
            btn.innerHTML = btn.innerHTML.replace('Voter', 'Retirer mon vote');
        } else {
            btn.classList.remove('voted');
            star.setAttribute('fill', 'none');
            btn.innerHTML = btn.innerHTML.replace('Retirer mon vote', 'Voter');
        }
        btn.disabled = false;
    })
    .catch(() => { btn.disabled = false; });
}

function postComment(btn) {
    const textarea   = document.getElementById('comment-input');
    const content    = textarea.value.trim();
    const subId      = btn.dataset.submissionId;
    const csrf       = btn.dataset.csrf;
    const list       = document.getElementById('comments-list');
    const noMsg      = document.getElementById('no-comments-msg');

    if (content.length < 2) {
        alert('Le commentaire est trop court.');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span>';

    fetch('<?= BASE_URL ?>/index.php?page=add-comment', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `submission_id=${subId}&content=${encodeURIComponent(content)}&<?= CSRF_TOKEN_NAME ?>=${csrf}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { alert(data.error); return; }

        if (noMsg) noMsg.remove();

        const initial = data.username.charAt(0).toUpperCase();
        const avatarHtml = data.avatar
            ? `<img src="<?= UPLOAD_URL ?>${data.avatar}" class="avatar-sm" alt="">`
            : `<div class="avatar-placeholder sm">${initial}</div>`;

        const html = `
        <div class="comment fade-in">
          <div>${avatarHtml}</div>
          <div class="comment__body">
            <div class="comment__header">
              <span class="comment__author">${data.username}</span>
              <span class="comment__date">${data.date}</span>
            </div>
            <p class="comment__text">${data.content}</p>
          </div>
        </div>`;

        list.insertAdjacentHTML('beforeend', html);
        textarea.value = '';
        btn.disabled = false;
        btn.innerHTML = 'Poster le commentaire';
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = 'Poster le commentaire';
    });
}
</script>

<style>
@media (max-width: 900px) {
  .container > div[style*="grid-template-columns"] {
    grid-template-columns: 1fr !important;
  }
  aside { order: -1; }
}
</style>
