<!-- Fiche détaillée d'un défi -->

<div class="page-header">
  <div class="page-header__inner">
    <div style="margin-bottom:.5rem">
      <a href="index.php?page=challenges" style="color:var(--clr-primary-l); font-size:.875rem;">← Retour aux défis</a>
    </div>
    <h1><?= htmlspecialchars($challenge['title']) ?></h1>
    <div class="flex items-center gap-2" style="margin-top:.5rem; flex-wrap:wrap;">
      <span class="badge badge-primary">🏷️ <?= htmlspecialchars($challenge['category']) ?></span>
      <span class="badge badge-accent">📅 Expire le : <?= date('d/m/Y', strtotime($challenge['deadline'])) ?></span>
    </div>
  </div>
</div>

<div class="container section">
  <div style="display:grid; grid-template-columns:1fr 340px; gap:2.5rem; align-items:start;">

    <!-- PARTIE GAUCHE : DESCRIPTION ET PARTICIPATIONS -->
    <div>
      <article class="glass-panel" style="padding:2rem; margin-bottom:2.5rem;">
        <?php if (!empty($challenge['image'])): ?>
          <img src="public/images/uploads/<?= htmlspecialchars($challenge['image']) ?>" alt="Affiche du défi" 
               style="width:100%; border-radius:var(--radius-lg); margin-bottom:1.5rem; max-height:400px; object-fit:cover;">
        <?php endif; ?>

        <p class="text-muted" style="font-size:1.05rem; line-height:1.75; white-space:pre-wrap;"><?= htmlspecialchars($challenge['description']) ?></p>
      </article>

      <!-- LISTE DES PARTICIPATIONS -->
      <section>
        <div class="flex justify-between items-center mb-1.5">
          <h2 style="font-weight:700; font-size:1.3rem;">🚀 Participations (<?= count($submissions) ?>)</h2>
          
          </form>
        </div>

        <?php if (empty($submissions)): ?>
          <div class="card" style="text-align:center; padding:4rem;">
            <div style="font-size:3.5rem;">📝</div>
            <h2 style="font-size:1.2rem; margin-top:1rem; opacity:.7;">Personne n'a encore relevé ce défi.</h2>
            <p style="margin-top:.5rem;">Soyez le premier à participer !</p>
          </div>
        <?php else: ?>
          <div class="grid grid-2 gap-md">
            <?php foreach ($submissions as $sub): ?>
            <article class="card fade-in">
              <?php if (!empty($sub['image'])): ?>
                <img src="public/images/uploads/<?= htmlspecialchars($sub['image']) ?>" alt="Participation" class="card__media">
              <?php else: ?>
                <div class="card__media-placeholder">🎨</div>
              <?php endif; ?>

              <div class="card__body">
                <div class="flex items-center gap-1 mb-1">
                  <span class="badge badge-primary">🏆 <?= (int)$sub['vote_count'] ?> votes</span>
                </div>
                <p class="card__desc"><?= htmlspecialchars($sub['description']) ?></p>
              </div>

              <div class="card__footer">
                <div class="card__meta">
                  👤 <span><?= htmlspecialchars($sub['username']) ?></span>
                </div>
                <a href="index.php?page=submission-show&id=<?= $sub['id'] ?>" class="btn btn-ghost btn-sm">Voir en grand →</a>
              </div>
            </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>
    </div>

    <!-- PARTIE DROITE : ACTIONS ET AUTEUR -->
    <aside>
      <div class="glass-panel" style="padding:1.75rem; position:sticky; top:2rem;">
        <h3 style="font-size:.9rem; text-transform:uppercase; color:var(--clr-text-dim); margin-bottom:1rem;">Auteur</h3>
        <div class="flex items-center gap-2 mb-2">
            👤 <strong><?= htmlspecialchars($challenge['username']) ?></strong>
        </div>

        <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px solid var(--clr-border);">
          
          <?php if (!isset($_SESSION['user_id'])): ?>
            <p style="font-size:.875rem; text-align:center;">
              <a href="index.php?page=login" style="color:var(--clr-primary-l); font-weight:700;">Connectez-vous</a> pour participer !
            </p>
          <?php elseif ($alreadySubmitted): ?>
            <div class="alert alert-success" style="font-size:.8rem; padding:.75rem;">
               ✅ Vous avez déjà participé à ce défi.
            </div>
            <a href="index.php?page=profile" class="btn btn-outline btn-sm" style="width:100%; justify-content:center; margin-top:1rem;">Voir ma participation</a>
          <?php else: ?>
            <a href="#participer" class="btn btn-primary btn-lg" style="width:100%; justify-content:center;" onclick="document.getElementById('form-participation').style.display='block'; this.style.display='none';">
              ⚡ Participer au défi
            </a>

            <!-- Formulaire pour participer au challenge -->
            <div id="form-participation" style="display:none; margin-top:1rem;" class="fade-in">
                <h4 style="margin-bottom:1rem; font-size:1.1rem;">Envoyer ma création</h4>
                <form action="index.php?page=submission-create" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                    <input type="hidden" name="challenge_id" value="<?= $challenge['id'] ?>">

                    <div class="form-group">
                        <label class="form-label" style="font-size:.8rem;">Description du projet</label>
                        <textarea name="description" class="form-control" rows="3" required placeholder="Expliquez brièvement votre travail..."></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="font-size:.8rem;">Lien externe (Optionnel)</label>
                        <input type="url" name="link" class="form-control" placeholder="Lien vers votre projet...">
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="font-size:.8rem;">Image illustrative</label>
                        <input type="file" name="image" class="form-control" style="font-size:.8rem;">
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm" style="width:100%; justify-content:center;">Poster ma participation</button>
                    <button type="button" class="btn btn-ghost btn-xs text-center" style="width:100%; margin-top:.5rem;" onclick="document.getElementById('form-participation').style.display='none'; document.querySelector('.btn-primary.btn-lg').style.display='flex';">
                        Annuler
                    </button>
                </form>
            </div>
          <?php endif; ?>

          <!-- Menu pour l'auteur du défi -->
          <?php if (isset($_SESSION['user_id']) && (int)$challenge['user_id'] === (int)$_SESSION['user_id']): ?>
            <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px solid var(--clr-border);">
              <p style="font-size:.8rem; margin-bottom:1rem; text-transform:uppercase; color:var(--clr-text-dim);">Gestion du défi</p>
              <div class="flex flex-col gap-1">
                <a href="index.php?page=challenge-edit&id=<?= $challenge['id'] ?>" class="btn btn-ghost btn-sm text-center">✏️ Modifier mon défi</a>
                
                <form action="index.php?page=challenge-delete" method="POST" onsubmit="return confirm('Confirmer la suppression ? Tous les projets liés seront perdus !')">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                    <input type="hidden" name="id" value="<?= $challenge['id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm" style="width:100%;">🗑️ Supprimer mon défi</button>
                </form>
              </div>
            </div>
          <?php endif; ?>

        </div>
      </div>
    </aside>

  </div>
</div>
