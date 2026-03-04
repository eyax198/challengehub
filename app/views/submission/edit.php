<div class="page-header">
  <div class="page-header__inner">
    <h1>✏️ Modifier la participation</h1>
    <p>Mettez à jour votre soumission pour le défi <strong><?= htmlspecialchars($submission['challenge_title']) ?></strong></p>
  </div>
</div>

<div class="container section">
  <div class="container-md" style="margin:0 auto;">

    <form method="POST" action="<?= BASE_URL ?>/index.php?page=submission-edit" enctype="multipart/form-data" id="edit-submission-form" novalidate>
      <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= htmlspecialchars($csrf) ?>">
      <input type="hidden" name="id" value="<?= $submission['id'] ?>">

      <div class="glass-panel" style="padding:2rem; display:flex; flex-direction:column; gap:1.5rem;">

        <div class="form-group">
          <label class="form-label" for="sub-description">Description de ma participation *</label>
          <textarea id="sub-description" name="description" class="form-control" rows="6" required minlength="10"><?= htmlspecialchars($submission['description']) ?></textarea>
        </div>

        <div class="form-group">
          <label class="form-label" for="sub-link">Lien externe (optionnel)</label>
          <input type="url" id="sub-link" name="link" class="form-control" value="<?= htmlspecialchars($submission['link'] ?? '') ?>" placeholder="https://...">
        </div>

        <div class="form-group">
          <label class="form-label">Nouvelle image (optionnel — laissez vide pour garder l'actuelle)</label>
          <?php if (!empty($submission['image'])): ?>
            <img src="<?= UPLOAD_URL . htmlspecialchars($submission['image']) ?>" alt="Image actuelle" style="max-height:150px; border-radius:var(--radius-md); margin-bottom:.75rem;">
          <?php endif; ?>
          <input type="file" name="image" id="sub-image" class="form-control" accept="image/*">
        </div>

        <div class="flex gap-2" style="justify-content:flex-end;">
          <a href="<?= BASE_URL ?>/index.php?page=submission-show&id=<?= $submission['id'] ?>" class="btn btn-ghost">Annuler</a>
          <button type="submit" class="btn btn-primary" id="btn-edit-sub-submit">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
            Enregistrer
          </button>
        </div>

      </div>
    </form>

  </div>
</div>
