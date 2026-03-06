<!-- Page pour modifier un défi existant -->

<div class="page-header">
  <div class="page-header__inner">
    <h1>✏️ Modifier le défi : <?= htmlspecialchars($challenge['title']) ?></h1>
    <p>Mettez à jour les informations de votre challenge</p>
  </div>
</div>

<div class="container section">
  <div class="auth-card" style="max-width:700px; margin:0 auto; padding:2rem;">
    
    <form action="index.php?page=challenge-edit" method="POST" enctype="multipart/form-data">
        <!-- Sécurité CSRF -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
        <input type="hidden" name="id" value="<?= $challenge['id'] ?>">

        <!-- Titre du défi -->
        <div class="form-group">
            <label class="form-label" for="title">Titre du défi</label>
            <input type="text" id="title" name="title" class="form-control" 
                   value="<?= htmlspecialchars($challenge['title']) ?>" required>
        </div>

        <!-- Catégorie -->
        <div class="form-group">
            <label class="form-label" for="category">Catégorie</label>
            <input type="text" id="category" name="category" class="form-control" 
                   value="<?= htmlspecialchars($challenge['category']) ?>" required>
        </div>

        <!-- Description du défi -->
        <div class="form-group">
            <label class="form-label" for="description">Description détaillée</label>
            <textarea id="description" name="description" class="form-control" rows="6" required><?= htmlspecialchars($challenge['description']) ?></textarea>
        </div>

        <!-- Date limite -->
        <div class="form-group">
            <label class="form-label" for="deadline">Date limite actuelle : <?= date('d/m/Y', strtotime($challenge['deadline'])) ?></label>
            <input type="date" id="deadline" name="deadline" class="form-control" 
                   value="<?= date('Y-m-d', strtotime($challenge['deadline'])) ?>" required>
        </div>

        <!-- Image illustrative -->
        <div class="form-group">
            <label class="form-label" for="image">Image du défi (Laissez vide pour garder l'actuelle)</label>
            <?php if (!empty($challenge['image'])): ?>
                <p style="font-size:.8rem; color:var(--clr-text-dim); margin-bottom:.5rem;">Image actuelle : <?= htmlspecialchars($challenge['image']) ?></p>
            <?php endif; ?>
            <input type="file" id="image" name="image" class="form-control" style="font-size:.85rem; padding:.5rem;">
        </div>

        <!-- Boutons d'action -->
        <div class="flex gap-1" style="margin-top:2.5rem; border-top:1px solid var(--clr-border); padding-top:2rem;">
            <a href="index.php?page=challenge-show&id=<?= $challenge['id'] ?>" class="btn btn-ghost" style="flex:1; justify-content:center;">Annuler</a>
            <button type="submit" class="btn btn-primary btn-lg" style="flex:2; justify-content:center;">✅ Mettre à jour le défi</button>
        </div>
    </form>

  </div>
</div>
