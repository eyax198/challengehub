<!-- Formulaire pour créer un nouveau défi -->

<div class="page-header">
  <div class="page-header__inner text-center">
    <h1>🏗️ Lancer un nouveau défi</h1>
    <p>Définissez les règles et invitez la communauté à participer !</p>
  </div>
</div>

<div class="container section">
  <div class="auth-card" style="max-width:700px; margin:0 auto; padding:2rem;">
    
    <form action="index.php?page=challenge-create" method="POST" enctype="multipart/form-data">
        <!-- Sécurité du formulaire -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

        <!-- Titre du défi -->
        <div class="form-group">
            <label class="form-label" for="title">Titre du défi</label>
            <input type="text" id="title" name="title" class="form-control" placeholder="Donnez un nom accrocheur..." required>
        </div>

        <!-- Catégorie -->
        <div class="form-group">
            <label class="form-label" for="category">Catégorie</label>
            <input type="text" id="category" name="category" class="form-control" placeholder="Design, Code, Photo, ..." required>
        </div>

        <!-- Description du défi -->
        <div class="form-group">
            <label class="form-label" for="description">Description détaillée</label>
            <textarea id="description" name="description" class="form-control" rows="5" placeholder="Quelles sont les règles ? Qu'est-ce qu'on attend des participants ?" required></textarea>
        </div>

        <!-- Date limite -->
        <div class="form-group">
            <label class="form-label" for="deadline">Date limite pour participer</label>
            <input type="date" id="deadline" name="deadline" class="form-control" required min="<?= date('Y-m-d') ?>">
        </div>

        <!-- Image illustrative -->
        <div class="form-group">
            <label class="form-label" for="image">Image du défi (Optionnelle)</label>
            <input type="file" id="image" name="image" class="form-control" style="font-size:.85rem; padding:.5rem;">
        </div>

        <!-- Bouton final -->
        <div class="flex gap-1" style="margin-top:2.5rem; border-top:1px solid var(--clr-border); padding-top:2rem;">
            <a href="index.php?page=challenges" class="btn btn-ghost" style="flex:1; justify-content:center;">Annuler</a>
            <button type="submit" class="btn btn-primary btn-lg" style="flex:2; justify-content:center;">⚡ Publier le défi</button>
        </div>
    </form>

  </div>
</div>
