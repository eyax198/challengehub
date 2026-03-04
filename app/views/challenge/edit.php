<?php $categories = ['Art & Design', 'Photographie', 'Écriture', 'Musique', 'Programmation', 'Vidéo', 'Jeux', 'Autre']; ?>

<div class="page-header">
  <div class="page-header__inner">
    <h1>✏️ Modifier le défi</h1>
    <p>Mettez à jour les informations de votre défi</p>
  </div>
</div>

<div class="container section">
  <div class="container-md" style="margin:0 auto;">

    <form method="POST" action="<?= BASE_URL ?>/index.php?page=challenge-edit" enctype="multipart/form-data" id="edit-challenge-form" novalidate>
      <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= htmlspecialchars($csrf) ?>">
      <input type="hidden" name="id" value="<?= $challenge['id'] ?>">

      <div class="glass-panel" style="padding:2rem; display:flex; flex-direction:column; gap:1.5rem;">

        <div class="form-group">
          <label class="form-label" for="ch-title">Titre du défi *</label>
          <input type="text" id="ch-title" name="title" class="form-control" value="<?= htmlspecialchars($challenge['title']) ?>" required minlength="5" maxlength="150">
        </div>

        <div class="form-group">
          <label class="form-label" for="ch-description">Description *</label>
          <textarea id="ch-description" name="description" class="form-control" rows="6" required minlength="20"><?= htmlspecialchars($challenge['description']) ?></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="ch-category">Catégorie *</label>
            <select id="ch-category" name="category" class="form-control" required>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>" <?= $challenge['category'] === $cat ? 'selected' : '' ?>>
                  <?= htmlspecialchars($cat) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label" for="ch-deadline">Date limite *</label>
            <input type="date" id="ch-deadline" name="deadline" class="form-control" value="<?= htmlspecialchars(substr($challenge['deadline'], 0, 10)) ?>" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Nouvelle image (optionnel — laissez vide pour garder l'actuelle)</label>
          <?php if (!empty($challenge['image'])): ?>
            <img src="<?= UPLOAD_URL . htmlspecialchars($challenge['image']) ?>" alt="Image actuelle" style="max-height:150px; border-radius:var(--radius-md); margin-bottom:.75rem;">
          <?php endif; ?>
          <input type="file" name="image" id="ch-image" class="form-control" accept="image/*">
        </div>

        <div class="flex gap-2" style="justify-content:flex-end;">
          <a href="<?= BASE_URL ?>/index.php?page=challenge-show&id=<?= $challenge['id'] ?>" class="btn btn-ghost">Annuler</a>
          <button type="submit" class="btn btn-primary" id="btn-edit-submit">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
            Enregistrer les modifications
          </button>
        </div>

      </div>
    </form>

  </div>
</div>
