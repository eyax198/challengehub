<!-- Affichage de tous les défis -->

<div class="page-header">
  <div class="page-header__inner">
    <h1>Tous les défis créatifs</h1>
    <p>Explorez les défis de la communauté et participez !</p>
  </div>
</div>

<div class="container section">
  <div style="display:grid; grid-template-columns:280px 1fr; gap:2rem; align-items:start;">

    <!-- Les filtres à gauche -->
    <aside class="glass-panel" style="padding:1.5rem; position:sticky; top:2rem;">
      <h3 style="font-size:1.1rem; font-weight:700; margin-bottom:1.5rem;">🔍 Filtres</h3>

      <form action="index.php" method="GET" class="flex flex-col gap-1">
        <input type="hidden" name="page" value="challenges">

        <!-- Recherche par mot-clé -->
        <div class="form-group">
          <label class="form-label" for="keyword">Mots-clés</label>
          <input type="text" id="keyword" name="keyword" class="form-control form-control--sm" 
                 placeholder="Rechercher..." value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>">
        </div>

        <!-- Catégories -->
        <div class="form-group">
          <label class="form-label" for="category">Catégorie</label>
          <select id="category" name="category" class="form-control form-control--sm">
            <option value="">Toutes les catégories</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= htmlspecialchars($cat) ?>" <?= ($filters['category'] ?? '') === $cat ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Tri -->
        <div class="form-group">
          <label class="form-label" for="sort">Trier par</label>
          <select id="sort" name="sort" class="form-control form-control--sm">
            <option value="newest" <?= ($filters['sort'] ?? 'newest') === 'newest' ? 'selected' : '' ?>>Plus récents</option>
            <option value="popular" <?= ($filters['sort'] ?? '') === 'popular' ? 'selected' : '' ?>>Plus populaires</option>
            <option value="oldest" <?= ($filters['sort'] ?? '') === 'oldest' ? 'selected' : '' ?>>Plus anciens</option>
          </select>
        </div>

        <button type="submit" class="btn btn-primary btn-sm" style="margin-top:1rem; width:100%; justify-content:center;">Appliquer</button>
        <a href="index.php?page=challenges" class="btn btn-ghost btn-xs text-center">Réinitialiser</a>
      </form>
    </aside>

    <!-- Ici on liste les résultats -->
    <div>
      <div class="flex justify-between items-center mb-2">
        <p class="text-dim"><?= count($challenges) ?> défis trouvés</p>
      </div>

      <?php if (empty($challenges)): ?>
        <div class="card" style="text-align:center; padding:4rem;">
          <div style="font-size:3.5rem;">🔎</div>
          <h2 style="font-size:1.3rem; margin-top:1rem;">Aucun défi ne correspond à votre recherche.</h2>
          <a href="index.php?page=challenges" class="btn btn-primary" style="margin-top:1.5rem;">Voir tous les défis</a>
        </div>
      <?php else: ?>
        <div class="grid grid-2 gap-md">
          <?php foreach ($challenges as $ch): ?>
          <article class="card fade-in">
            <?php if (!empty($ch['image'])): ?>
              <img src="public/images/uploads/<?= htmlspecialchars($ch['image']) ?>" alt="Challenge" class="card__media">
            <?php else: ?>
              <div class="card__media-placeholder">⚡</div>
            <?php endif; ?>

            <div class="card__body">
              <span class="card__category"><?= htmlspecialchars($ch['category']) ?></span>
              <h3 class="card__title">
                <a href="index.php?page=challenge-show&id=<?= $ch['id'] ?>"><?= htmlspecialchars($ch['title']) ?></a>
              </h3>
              <p class="card__desc"><?= htmlspecialchars($ch['description']) ?></p>
            </div>

            <div class="card__footer">
              <div class="card__meta">
                👤 <span><?= htmlspecialchars($ch['username']) ?></span>
              </div>
              <a href="index.php?page=challenge-show&id=<?= $ch['id'] ?>" class="btn btn-primary btn-sm">Voir →</a>
            </div>
          </article>
          <?php endforeach; ?>
        </div>

        <!-- Barre de pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="flex justify-center gap-1" style="margin-top:3rem;">
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="index.php?page=challenges&page=<?= $i ?>&keyword=<?= urlencode($filters['keyword'] ?? '') ?>&category=<?= urlencode($filters['category'] ?? '') ?>&sort=<?= $filters['sort'] ?? 'newest' ?>" 
               class="btn <?= $page === $i ? 'btn-primary' : 'btn-ghost' ?> btn-xs">
               <?= $i ?>
            </a>
          <?php endfor; ?>
        </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>

  </div>
</div>
