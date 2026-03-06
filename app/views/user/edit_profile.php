<!-- Page de réglages du profil -->

<div class="page-header">
  <div class="page-header__inner">
    <h1>⚙️ Paramètres du compte</h1>
    <p>Personnalisez votre profil et vos informations</p>
  </div>
</div>

<div class="container section">
  <div class="auth-card" style="max-width:700px; margin:0 auto; padding:2rem;">
    
    <form action="index.php?page=edit-profile" method="POST" enctype="multipart/form-data">
        <!-- Sécurité CSRF -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

        <!-- Nom d'utilisateur -->
        <div class="form-group">
            <label class="form-label" for="username">Nom d'utilisateur</label>
            <input type="text" id="username" name="username" class="form-control" 
                   value="<?= htmlspecialchars($user['username']) ?>" required>
        </div>

        <!-- Email -->
        <div class="form-group">
            <label class="form-label" for="email">Adresse email</label>
            <input type="email" id="email" name="email" class="form-control" 
                   value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>

        <!-- Biographie -->
        <div class="form-group">
            <label class="form-label" for="bio">Bio / À propos de moi</label>
            <textarea id="bio" name="bio" class="form-control" rows="4" 
                      placeholder="Parlez de vous en quelques lignes..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
        </div>

        <!-- Photo de profil -->
        <div class="form-group">
            <label class="form-label" for="avatar">Photo de profil (Laissez vide pour garder l'actuelle)</label>
            <input type="file" id="avatar" name="avatar" class="form-control" style="font-size:.85rem; padding:.5rem;">
        </div>

        <!-- Mot de passe -->
        <div class="form-group" style="margin-top:2rem; padding-top:2rem; border-top:1px solid var(--clr-border);">
            <label class="form-label" for="password">Nouveau mot de passe (Laissez vide pour ne pas changer)</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="••••••••">
        </div>

        <!-- Boutons d'action -->
        <div class="flex gap-1" style="margin-top:2.5rem;">
            <a href="index.php?page=profile" class="btn btn-ghost" style="flex:1; justify-content:center;">Annuler</a>
            <button type="submit" class="btn btn-primary" style="flex:2; justify-content:center;">✅ Sauvegarder les modifications</button>
        </div>
    </form>

    <!-- Suppression du compte -->
    <div style="margin-top:4rem; padding-top:2rem; border-top:2px solid var(--clr-border);">
        <h4 style="color:var(--clr-danger); margin-bottom:1rem;">⚠️ Zone de danger</h4>
        <p style="font-size:.85rem; color:var(--clr-text-dim); margin-bottom:1.5rem;">La suppression de votre compte est définitive. Toutes vos données seront perdues.</p>
        
        <form action="index.php?page=delete-account" method="POST" onsubmit="return confirm('Êtes-vous SÛR de vouloir supprimer votre compte définitivement ?')">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Entrez votre mot de passe pour confirmer..." required>
            </div>
            <button type="submit" class="btn btn-danger btn-sm">🗑️ Supprimer mon compte</button>
        </form>
    </div>

  </div>
</div>
