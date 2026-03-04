<?php $categories = ['Art & Design', 'Photographie', 'Écriture', 'Musique', 'Programmation', 'Vidéo', 'Jeux', 'Autre']; ?>

<div class="page-header">
  <div class="page-header__inner">
    <h1>⚡ Créer un défi</h1>
    <p>Lancez votre défi créatif et inspirez la communauté</p>
  </div>
</div>

<div class="container section">
  <div class="container-md" style="margin:0 auto;">

    <form method="POST" action="<?= BASE_URL ?>/index.php?page=challenge-create" enctype="multipart/form-data" id="create-challenge-form" novalidate>
      <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= htmlspecialchars($csrf) ?>">

      <div class="glass-panel" style="padding:2rem; display:flex; flex-direction:column; gap:1.5rem;">

        <div class="form-group">
          <label class="form-label" for="ch-title">Titre du défi *</label>
          <input type="text" id="ch-title" name="title" class="form-control" placeholder="Ex : Le plus beau coucher de soleil photographié..." required minlength="5" maxlength="150">
          <p class="form-hint">Soyez précis et inspirant — au moins 5 caractères.</p>
        </div>

        <div class="form-group">
          <label class="form-label" for="ch-description">Description *</label>
          <textarea id="ch-description" name="description" class="form-control" placeholder="Décrivez votre défi en détail : règles, critères de sélection, attentes..." rows="6" required minlength="20"></textarea>
          <p class="form-hint">Soyez précis sur les règles et critères d'évaluation.</p>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="ch-category">Catégorie *</label>
            <select id="ch-category" name="category" class="form-control" required>
              <option value="">— Choisir une catégorie —</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label" for="ch-deadline">Date limite *</label>
            <input type="date" id="ch-deadline" name="deadline" class="form-control" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Image de couverture (optionnel)</label>
          <div class="upload-area" id="challenge-upload-area">
            <input type="file" name="image" id="ch-image" accept="image/*">
            <div id="ch-upload-text">
              <p style="font-size:2.5rem; margin-bottom:.5rem">🖼️</p>
              <p style="font-size:.875rem; color:var(--clr-text-muted)">Cliquez ou glissez une image ici</p>
              <p style="font-size:.75rem; color:var(--clr-text-dim); margin-top:.25rem">JPG, PNG, GIF, WebP · max 5 Mo</p>
            </div>
            <img id="ch-preview" style="display:none; max-height:200px; width:100%; object-fit:cover; border-radius:var(--radius-md);" alt="">
          </div>
        </div>

        <div class="flex gap-2" style="justify-content:flex-end;">
          <a href="<?= BASE_URL ?>/index.php?page=challenges" class="btn btn-ghost">Annuler</a>
          <button type="submit" class="btn btn-primary" id="btn-create-submit">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
            Publier le défi
          </button>
        </div>

      </div>
    </form>

  </div>
</div>
