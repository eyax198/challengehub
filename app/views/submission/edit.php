<!-- Page pour modifier un projet déjà envoyé -->

<div class="page-header">
  <div class="page-header__inner">
    <h1>✏️ Modifier ma participation</h1>
    <p>Mettez à jour votre projet pour le défi : <strong><?= htmlspecialchars($submission['challenge_title'] ?? '') ?></strong></p>
  </div>
</div>

<div class="container section">
  <div class="auth-card" style="max-width:700px; margin:0 auto; padding:2rem;">
    
    <form action="index.php?page=submission-edit" method="POST" enctype="multipart/form-data">
        <!-- Sécurité CSRF -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
        <input type="hidden" name="id" value="<?= $submission['id'] ?>">

        <!-- Description du projet -->
        <div class="form-group">
            <label class="form-label" for="description">Description détaillée du projet</label>
            <textarea id="description" name="description" class="form-control" rows="6" 
                      placeholder="Expliquez brièvement votre travail..." required><?= htmlspecialchars($submission['description']) ?></textarea>
        </div>

        <!-- Lien externe -->
        <div class="form-group">
            <label class="form-label" for="link">Lien vers votre projet (Optionnel)</label>
            <input type="url" id="link" name="link" class="form-control" 
                   value="<?= htmlspecialchars($submission['link'] ?? '') ?>" placeholder="https://votre-projet.com">
        </div>

        <!-- Image illustrative -->
        <div class="form-group">
            <label class="form-label" for="image">Image illustrative (Laissez vide pour garder l'actuelle)</label>
            <?php if (!empty($submission['image'])): ?>
                <p style="font-size:.8rem; color:var(--clr-text-dim); margin-bottom:.5rem;">Image actuelle : <?= htmlspecialchars($submission['image']) ?></p>
            <?php endif; ?>
            <input type="file" id="image" name="image" class="form-control" style="font-size:.85rem; padding:.5rem;">
        </div>

        <!-- Boutons d'action -->
        <div class="flex gap-1" style="margin-top:2.5rem; border-top:1px solid var(--clr-border); padding-top:2rem;">
            <a href="index.php?page=submission-show&id=<?= $submission['id'] ?>" class="btn btn-ghost" style="flex:1; justify-content:center;">Annuler</a>
            <button type="submit" class="btn btn-primary btn-lg" style="flex:2; justify-content:center;">✅ Sauvegarder les modifications</button>
        </div>
    </form>

  </div>
</div>
