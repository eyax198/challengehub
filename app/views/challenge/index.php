<?php
// Helper to compute deadline status
function deadlineStatus(string $deadline): string {
    $diff = (new DateTime($deadline))->getTimestamp() - time();
    if ($diff < 0) return 'expired';
    if ($diff < 3 * 86400) return 'soon';
    return 'ok';
}
function deadlineLabel(string $deadline): string {
    $diff = (new DateTime($deadline))->getTimestamp() - time();
    if ($diff < 0) return 'Expiré';
    $days = (int)($diff / 86400);
    if ($days === 0) return 'Expire aujourd\'hui';
    if ($days === 1) return 'Expire demain';
    return "J-{$days}";
}
?>

<div class="page-header">
  <div class="page-header__inner">
    <h1>⚡ Tous les défis</h1>
    <p>Découvrez et participez aux défis publiés par la communauté • <strong><?= $total ?></strong> défi<?= $total > 1 ? 's' : '' ?></p>
  </div>
</div>

<div class="container section">

  <!-- Filters -->
  <form class="filters" method="GET" action="<?= BASE_URL ?>/index.php" id="filters-form">
    <input type="hidden" name="page" value="challenges">

    <div class="filters__search">
      <svg class="filters__search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input
        type="text"
        name="keyword"
        id="filter-keyword"
        class="form-control"
        placeholder="Rechercher un défi..."
        value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>"
        style="padding-left:2.75rem"
      >
    </div>

    <select name="category" id="filter-category" class="form-control" style="min-width:160px;">
      <option value="">Toutes les catégories</option>
      <?php foreach ($categories as $cat): ?>
        <option value="<?= htmlspecialchars($cat) ?>" <?= ($filters['category'] ?? '') === $cat ? 'selected' : '' ?>>
          <?= htmlspecialchars($cat) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <select name="sort" id="filter-sort" class="form-control" style="min-width:140px;">
      <option value="newest" <?= ($filters['sort'] ?? '') === 'newest'  ? 'selected' : '' ?>>Plus récents</option>
      <option value="popular" <?= ($filters['sort'] ?? '') === 'popular' ? 'selected' : '' ?>>Plus populaires</option>
      <option value="oldest"  <?= ($filters['sort'] ?? '') === 'oldest'  ? 'selected' : '' ?>>Plus anciens</option>
    </select>

    <button type="submit" class="btn btn-primary" id="filter-submit">Filtrer</button>
    <a href="<?= BASE_URL ?>/index.php?page=challenges" class="btn btn-ghost" id="filter-reset">Réinitialiser</a>
  </form>

  <!-- Challenge Grid -->
  <?php if (empty($challenges)): ?>
    <div class="empty-state" id="challenges-empty">
      <div class="empty-state__icon">🔍</div>
      <h3>Aucun défi trouvé</h3>
      <p>Essayez d'autres filtres ou <a href="<?= BASE_URL ?>/index.php?page=challenge-create" style="color:var(--clr-primary-l)">créez le premier défi !</a></p>
    </div>
  <?php else: ?>
    <div class="grid grid-auto gap-md" id="challenges-grid">
      <?php foreach ($challenges as $ch): ?>
        <?php
        $status = deadlineStatus($ch['deadline']);
        $dlLabel = deadlineLabel($ch['deadline']);
        ?>
      <article class="card fade-in" id="challenge-<?= $ch['id'] ?>">

        <?php if (!empty($ch['image'])): ?>
          <img src="<?= UPLOAD_URL . htmlspecialchars($ch['image']) ?>" alt="<?= htmlspecialchars($ch['title']) ?>" class="card__media">
        <?php else: ?>
          <div class="card__media-placeholder">⚡</div>
        <?php endif; ?>

        <div class="card__body">
          <span class="card__category"><?= htmlspecialchars($ch['category']) ?></span>
          <h2 class="card__title">
            <a href="<?= BASE_URL ?>/index.php?page=challenge-show&id=<?= $ch['id'] ?>" style="color:inherit">
              <?= htmlspecialchars($ch['title']) ?>
            </a>
          </h2>
          <p class="card__desc"><?= htmlspecialchars($ch['description']) ?></p>

          <div class="flex gap-1 flex-wrap" style="margin-top:.75rem">
            <span class="badge badge-primary">
              <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
              <?= htmlspecialchars($dlLabel) ?>
            </span>
            <span class="badge badge-<?= $ch['submissions_count'] > 0 ? 'success' : 'warning' ?>">
              <?= (int)$ch['submissions_count'] ?> participation<?= $ch['submissions_count'] > 1 ? 's' : '' ?>
            </span>
          </div>
        </div>

        <div class="card__footer">
          <div class="card__meta">
            <?php if (!empty($ch['avatar'])): ?>
              <img src="<?= UPLOAD_URL . htmlspecialchars($ch['avatar']) ?>" alt="" class="avatar-sm">
            <?php else: ?>
              <div class="avatar-placeholder sm"><?= strtoupper(substr($ch['username'], 0, 1)) ?></div>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/index.php?page=profile&id=<?= $ch['user_id'] ?>" style="color:inherit">
              <?= htmlspecialchars($ch['username']) ?>
            </a>
          </div>
          <a href="<?= BASE_URL ?>/index.php?page=challenge-show&id=<?= $ch['id'] ?>" class="btn btn-primary btn-sm">
            Voir →
          </a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <nav class="pagination" aria-label="Pagination">
      <?php if ($page > 1): ?>
        <a href="?page=challenges&keyword=<?= urlencode($filters['keyword'] ?? '') ?>&category=<?= urlencode($filters['category'] ?? '') ?>&sort=<?= $filters['sort'] ?>&page=<?= $page - 1 ?>" aria-label="Page précédente">‹</a>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <?php if ($i === $page): ?>
          <span class="active" aria-current="page"><?= $i ?></span>
        <?php else: ?>
          <a href="?page=challenges&keyword=<?= urlencode($filters['keyword'] ?? '') ?>&category=<?= urlencode($filters['category'] ?? '') ?>&sort=<?= $filters['sort'] ?>&page=<?= $i ?>"><?= $i ?></a>
        <?php endif; ?>
      <?php endfor; ?>

      <?php if ($page < $totalPages): ?>
        <a href="?page=challenges&keyword=<?= urlencode($filters['keyword'] ?? '') ?>&category=<?= urlencode($filters['category'] ?? '') ?>&sort=<?= $filters['sort'] ?>&page=<?= $page + 1 ?>" aria-label="Page suivante">›</a>
      <?php endif; ?>
    </nav>
    <?php endif; ?>
  <?php endif; ?>

</div>
