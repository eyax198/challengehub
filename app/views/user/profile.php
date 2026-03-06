<!-- Profil de l'utilisateur -->

<div class="page-header">
  <div class="page-header__inner">
    <div class="flex items-center gap-2 mb-1.5">
       <span style="font-size:3rem;">👤</span>
       <div>
         <h1 style="margin-bottom:0;"><?= htmlspecialchars($profileUser['username']) ?></h1>
         <p class="text-dim">Membre depuis le : <?= date('d/m/Y', strtotime($profileUser['created_at'])) ?></p>
       </div>
    </div>
    
    <!-- Stats rapides : défis et participations -->
    <div class="flex items-center gap-1.5 mt-2 flex-wrap">
       <span class="badge badge-primary">⚡ <?= count($challenges) ?> défis lancés</span>
       <span class="badge badge-accent">🚀 <?= count($submissions) ?> participations envoyées</span>
    </div>
  </div>
</div>

<div class="container section">
  <div style="display:grid; grid-template-columns:300px 1fr; gap:2.5rem; align-items:start;">

    <!-- Infos et actions de compte -->
    <aside>
      <div class="glass-panel" style="padding:1.75rem; margin-bottom:1.5rem;">
        <h3 style="font-size:.9rem; text-transform:uppercase; color:var(--clr-text-dim); margin-bottom:1rem;">Bio / Description</h3>
        <p style="font-size:.9rem; line-height:1.6; color:var(--clr-text-muted); font-style:italic;">
           <?= !empty($profileUser['bio']) ? htmlspecialchars($profileUser['bio']) : "Cet utilisateur n'a pas encore rédigé sa bio. 🍃" ?>
        </p>

        <?php if (isset($_SESSION['user_id']) && (int)$profileUser['id'] === (int)$_SESSION['user_id']): ?>
          <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px solid var(--clr-border);">
            <p style="font-size:.8rem; color:var(--clr-text-dim); text-transform:uppercase; margin-bottom:1rem;">Ma gestion</p>
            <div class="flex flex-col gap-1">
              <a href="index.php?page=edit-profile" class="btn btn-ghost btn-sm text-center">✏️ Modifier mon profil</a>
              <a href="index.php?page=logout" class="btn btn-danger btn-sm text-center">🚪 Me déconnecter</a>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </aside>

    <!-- PARTIE PRINCIPALE : LES LISTES -->
    <div>
      
      <!-- Liste des défis qu'il a lancé -->
      <section style="margin-bottom:3rem;">
        <h2 style="font-size:1.3rem; font-weight:700; margin-bottom:1.5rem;">🏗️ Défis lancés par <?= htmlspecialchars($profileUser['username']) ?></h2>
        
        <?php if (empty($challenges)): ?>
          <p class="text-muted" style="font-style:italic;">Aucun défi lancé pour le moment.</p>
        <?php else: ?>
          <div class="grid grid-2 gap-md">
            <?php foreach ($challenges as $ch): ?>
              <article class="card">
                <div class="card__body">
                   <span class="card__category"><?= htmlspecialchars($ch['category']) ?></span>
                   <h4 class="card__title">
                      <a href="index.php?page=challenge-show&id=<?= $ch['id'] ?>"><?= htmlspecialchars($ch['title']) ?></a>
                   </h4>
                   <p class="card__desc" style="font-size:.85rem; line-height:1.4; color:var(--clr-text-dim);">
                      <?= (int)$ch['submissions_count'] ?> participation(s) reçue(s)
                   </p>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>

      <!-- Liste des projets qu'il a envoyé -->
      <section>
        <h2 style="font-size:1.3rem; font-weight:700; margin-bottom:1.5rem;">🎉 Participations aux défis</h2>
        
        <?php if (empty($submissions)): ?>
          <p class="text-muted" style="font-style:italic;">Aucune participation pour l'instant.</p>
        <?php else: ?>
          <div class="grid grid-2 gap-md">
            <?php foreach ($submissions as $sub): ?>
              <article class="card">
                <div class="card__body">
                   <h4 class="card__title">
                      <a href="index.php?page=submission-show&id=<?= $sub['id'] ?>">Participation au défi : <?= htmlspecialchars($sub['challenge_title']) ?></a>
                   </h4>
                   <p class="card__desc" style="font-size:.85rem; color:var(--clr-text-dim);">
                      🏆 <?= (int)$sub['vote_count'] ?> votes récoltés
                   </p>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>

    </div>

  </div>
</div>
