<!-- Détails d'un projet soumis -->

<div class="page-header">
  <div class="page-header__inner">
    <div style="margin-bottom:.5rem">
      <a href="index.php?page=challenge-show&id=<?= $submission['challenge_id'] ?>" style="color:var(--clr-primary-l); font-size:.875rem;">← Retour au défi : <?= htmlspecialchars($submission['challenge_title']) ?></a>
    </div>
    <h1>Participation de <?= htmlspecialchars($submission['username']) ?></h1>
    <div class="flex items-center gap-2" style="margin-top:.5rem; flex-wrap:wrap;">
      <span class="badge badge-primary">🏆 <?= (int)$submission['vote_count'] ?> votes</span>
      <span class="badge badge-accent">💬 <?= count($comments) ?> commentaires</span>
    </div>
  </div>
</div>

<div class="container section">
  <div style="display:grid; grid-template-columns:1fr 320px; gap:2.5rem; align-items:start;">

    <!-- PARTIE GAUCHE : IMAGE ET DESCRIPTION -->
    <div>
      <?php if (!empty($submission['image'])): ?>
        <img src="public/images/uploads/<?= htmlspecialchars($submission['image']) ?>" alt="Participation" 
             style="width:100%; max-height:450px; object-fit:cover; border-radius:var(--radius-lg); margin-bottom:1.5rem;">
      <?php endif; ?>

      <div class="glass-panel" style="padding:1.75rem; margin-bottom:2rem;">
        <p style="color:var(--clr-text-muted); line-height:1.75; white-space:pre-wrap;"><?= htmlspecialchars($submission['description']) ?></p>
        
        <?php if (!empty($submission['link'])): ?>
          <div style="margin-top:1.5rem; padding-top:1.5rem; border-top:1px solid var(--clr-border);">
            <a href="<?= htmlspecialchars($submission['link']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-ghost btn-sm">🔗 Voir le projet externe</a>
          </div>
        <?php endif; ?>
      </div>

      <!-- COMMENTAIRES -->
      <section class="glass-panel" style="padding:1.75rem;">
        <h2 style="font-size:1.15rem; font-weight:700; margin-bottom:1.5rem;">💬 Commentaires (<?= count($comments) ?>)</h2>

        <!-- Liste des commentaires -->
        <div id="comments-list">
          <?php if (empty($comments)): ?>
            <p class="text-muted" style="font-size:.875rem;" id="no-comments-msg">Aucun commentaire pour l'instant. Soyez le premier !</p>
          <?php else: ?>
            <?php foreach ($comments as $c): ?>
            <div class="comment" style="margin-bottom:1.5rem; padding-bottom:1.5rem; border-bottom:1px solid var(--clr-border);">
              <div class="flex items-center gap-1 mb-0.75">
                <span class="comment__author">👤 <strong><?= htmlspecialchars($c['username']) ?></strong></span>
                <span class="comment__date" style="font-size:.75rem; color:var(--clr-text-dim);"> • <?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></span>
              </div>
              <p class="comment__text" style="font-size:.9rem; line-height:1.5;"><?= htmlspecialchars($c['content']) ?></p>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <!-- Bloc pour ajouter un commentaire (en Ajax) -->
        <?php if (isset($_SESSION['user_id'])): ?>
          <div style="margin-top:2rem; padding-top:1.5rem; border-top:2px solid var(--clr-border);">
            <div class="form-group">
              <label class="form-label">Ajouter un commentaire</label>
              <textarea id="comment-input" class="form-control" placeholder="Votre commentaire..." rows="3"></textarea>
            </div>
            <button class="btn btn-primary btn-sm" id="btn-post-comment" 
                    data-submission-id="<?= $submission['id'] ?>" 
                    data-csrf="<?= htmlspecialchars($csrf) ?>"
                    onclick="postComment(this)">
              Poster le commentaire
            </button>
          </div>
        <?php else: ?>
          <p style="font-size:.875rem; margin-top:2rem; padding-top:1.5rem; border-top:1px solid var(--clr-border); text-align:center;">
             <a href="index.php?page=login" style="color:var(--clr-primary-l); font-weight:700;">Connectez-vous</a> pour commenter.
          </p>
        <?php endif; ?>
      </section>
    </div>

    <!-- PARTIE DROITE : ACTIONS ET VOTE -->
    <aside>
      <div class="glass-panel" style="padding:1.5rem; margin-bottom:1.5rem;">
        <h3 style="font-size:.9rem; text-transform:uppercase; color:var(--clr-text-dim); margin-bottom:1rem;">Auteur</h3>
        <div>
          👤 <strong><?= htmlspecialchars($submission['username']) ?></strong>
          <p class="text-dim" style="font-size:.75rem;">Publié le : <?= date('d/m/Y', strtotime($submission['created_at'])) ?></p>
        </div>

        <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px solid var(--clr-border);">
          <!-- Le bouton de vote (Ajax) -->
          <?php if (isset($_SESSION['user_id']) && (int)$submission['user_id'] !== (int)$_SESSION['user_id']): ?>
            <button class="btn <?= $hasVoted ? 'btn-primary' : 'btn-outline' ?>" 
                    id="vote-btn" 
                    data-submission-id="<?= $submission['id'] ?>" 
                    data-csrf="<?= htmlspecialchars($csrf) ?>"
                    onclick="castVote(this)"
                    style="width:100%; justify-content:center;">
              <?= $hasVoted ? '⭐ Mon vote est ajouté' : '⚡ Voter pour ce projet' ?>
              (<span id="vote-count"><?= (int)$submission['vote_count'] ?></span>)
            </button>
          <?php else: ?>
            <div class="badge badge-primary text-center" style="width:100%; justify-content:center; padding:.75rem;">
               🏆 Score actuel : <?= (int)$submission['vote_count'] ?> votes
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Menu pour l'auteur de la participation -->
      <?php if (isset($_SESSION['user_id']) && (int)$submission['user_id'] === (int)$_SESSION['user_id']): ?>
        <div class="glass-panel" style="padding:1.5rem;">
          <h3 style="font-size:.9rem; text-transform:uppercase; color:var(--clr-text-dim); margin-bottom:1rem;">Gestion</h3>
          <div class="flex flex-col gap-1">
            <a href="index.php?page=submission-edit&id=<?= $submission['id'] ?>" class="btn btn-ghost btn-sm text-center">✏️ Modifier mon projet</a>
            <form action="index.php?page=submission-delete" method="POST" onsubmit="return confirm('Confirmer la suppression de votre participation ?')">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                <input type="hidden" name="id" value="<?= $submission['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm" style="width:100%;">🗑️ Supprimer</button>
            </form>
          </div>
        </div>
      <?php endif; ?>
    </aside>

  </div>
</div>

<!-- JavaScript pour les votes et les commentaires (Ajax) -->
<script>
function castVote(btn) {
    const subId   = btn.dataset.submissionId;
    const csrf    = btn.dataset.csrf;
    const countEl = document.getElementById('vote-count');

    btn.disabled = true;

    fetch('index.php?page=vote', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'submission_id=' + subId + '&csrf_token=' + csrf
    })
    .then(r => r.json())
    .then(data => {
        countEl.textContent = data.count;
        if (data.action === 'added') {
            btn.classList.add('btn-primary'); btn.classList.remove('btn-outline');
            btn.innerHTML = '⭐ Mon vote est ajouté (' + data.count + ')';
        } else {
            btn.classList.remove('btn-primary'); btn.classList.add('btn-outline');
            btn.innerHTML = '⚡ Voter pour ce projet (' + data.count + ')';
        }
        btn.disabled = false;
    });
}

function postComment(btn) {
    const textarea = document.getElementById('comment-input');
    const content  = textarea.value.trim();
    const subId    = btn.dataset.submissionId;
    const csrf     = btn.dataset.csrf;
    const list     = document.getElementById('comments-list');

    if (content.length < 2) { alert('Commentaire trop court.'); return; }

    btn.disabled = true;

    fetch('index.php?page=add-comment', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'submission_id=' + subId + '&content=' + encodeURIComponent(content) + '&csrf_token=' + csrf
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { alert(data.error); }
        else {
            const noMsg = document.getElementById('no-comments-msg');
            if (noMsg) noMsg.remove();

            const html = '<div class="comment fade-in" style="margin-bottom:1.5rem; padding-bottom:1.5rem; border-bottom:1px solid var(--clr-border);"><div>👤 <strong>' + data.username + '</strong> <span style="font-size:.75rem; color:var(--clr-text-dim);"> • ' + data.date + '</span></div><p style="font-size:.9rem; margin-top:.5rem;">' + data.content + '</p></div>';
            list.insertAdjacentHTML('beforeend', html);
            textarea.value = '';
        }
        btn.disabled = false;
    });
}
</script>
