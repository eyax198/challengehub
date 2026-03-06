<!-- Page du classement général -->

<div class="page-header">
  <div class="page-header__inner">
    <h1>🏆 Classement des projets</h1>
    <p>Découvrez les participations les plus votées par la communauté !</p>
  </div>
</div>

<div class="container section">
  <?php if (empty($topSubmissions)): ?>
    <div class="card" style="text-align:center; padding:4rem;">
      <div style="font-size:3.5rem;">🔎</div>
      <h2 style="font-size:1.3rem; margin-top:1rem;">Le classement est encore vide. Soyez le premier à participer !</h2>
      <a href="index.php" class="btn btn-primary" style="margin-top:1.5rem;">Découvrir les défis</a>
    </div>
  <?php else: ?>
    
    <div class="grid grid-3 gap-md">
      <?php foreach ($topSubmissions as $index => $sub): ?>
        <article class="card fade-in" style="position:relative;">
          
          <!-- Le numéro du rang -->
          <div style="position:absolute; top:-1rem; left:-1rem; width:2.5rem; height:2.5rem; border-radius:100%; display:flex; align-items:center; justify-content:center; font-weight:700; background:var(--clr-primary-l); color:white; border:4px solid var(--clr-bg); z-index:10;">
             <?= $index + 1 ?>
          </div>

          <!-- Image de la participation -->
          <?php if (!empty($sub['image'])): ?>
            <img src="public/images/uploads/<?= htmlspecialchars($sub['image']) ?>" alt="Projet" class="card__media">
          <?php else: ?>
            <div class="card__media-placeholder">🎨</div>
          <?php endif; ?>

          <div class="card__body">
            <div class="flex items-center gap-1 mb-1">
               <span class="badge badge-primary">⭐ <?= (int)$sub['vote_count'] ?> votes</span>
            </div>
            
            <h3 class="card__title" style="font-size:1rem; margin-bottom:.5rem;">
               <a href="index.php?page=submission-show&id=<?= $sub['id'] ?>">Participation de <?= htmlspecialchars($sub['username']) ?></a>
            </h3>
            
            <p class="text-dim" style="font-size:.8rem; margin-top:.5rem;">
               Défi : <a href="index.php?page=challenge-show&id=<?= $sub['challenge_id'] ?>" style="color:var(--clr-primary-l); font-weight:600;"><?= htmlspecialchars($sub['challenge_title']) ?></a>
            </p>
          </div>

          <div class="card__footer">
             <a href="index.php?page=submission-show&id=<?= $sub['id'] ?>" class="btn btn-ghost btn-sm" style="width:100%; justify-content:center;">Voir ce projet →</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

  <?php endif; ?>
</div>
