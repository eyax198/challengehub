<div class="page-header">
  <div class="page-header__inner">
    <h1>⚙️ Modifier mon profil</h1>
    <p>Mettez à jour vos informations personnelles</p>
  </div>
</div>

<div class="container section">
  <div class="container-md" style="margin:0 auto; display:flex; flex-direction:column; gap:2rem;">

    <!-- Edit Profile -->
    <form method="POST" action="<?= BASE_URL ?>/index.php?page=edit-profile" enctype="multipart/form-data" id="edit-profile-form" novalidate>
      <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= htmlspecialchars($csrf) ?>">

      <div class="glass-panel" style="padding:2rem; display:flex; flex-direction:column; gap:1.5rem;">
        <h2 style="font-size:1.15rem; font-weight:700;">Informations du compte</h2>

        <!-- Avatar -->
        <div class="form-group">
          <label class="form-label">Photo de profil</label>
          <div class="flex items-center gap-2" style="flex-wrap:wrap;">
            <?php if (!empty($user['avatar'])): ?>
              <img src="<?= UPLOAD_URL . htmlspecialchars($user['avatar']) ?>" alt="Avatar actuel" style="width:70px; height:70px; border-radius:50%; object-fit:cover; border:3px solid var(--clr-primary);">
            <?php else: ?>
              <div class="avatar-placeholder lg"><?= strtoupper(substr($user['username'], 0, 1)) ?></div>
            <?php endif; ?>
            <div>
              <input type="file" name="avatar" id="edit-avatar" class="form-control" accept="image/*" style="max-width:300px;">
              <p class="form-hint">JPG, PNG, GIF, WebP · max 5 Mo</p>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="edit-username">Nom d'utilisateur</label>
            <input type="text" id="edit-username" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required minlength="3" maxlength="50">
          </div>
          <div class="form-group">
            <label class="form-label" for="edit-email">Email</label>
            <input type="email" id="edit-email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="edit-bio">Bio</label>
          <textarea id="edit-bio" name="bio" class="form-control" rows="3" maxlength="300" placeholder="Parlez-nous de vous..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="edit-password">Nouveau mot de passe</label>
            <input type="password" id="edit-password" name="password" class="form-control" placeholder="Laissez vide pour ne pas changer" minlength="8">
          </div>
          <div class="form-group">
            <label class="form-label" for="edit-password2">Confirmer</label>
            <input type="password" id="edit-password2" class="form-control" placeholder="••••••••">
          </div>
        </div>

        <div class="flex justify-between items-center flex-wrap gap-2">
          <a href="<?= BASE_URL ?>/index.php?page=profile" class="btn btn-ghost">Annuler</a>
          <button type="submit" class="btn btn-primary" id="btn-save-profile">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
            Sauvegarder
          </button>
        </div>
      </div>
    </form>

    <!-- Danger Zone -->
    <div class="glass-panel" style="padding:2rem; border-color:rgba(239,68,68,.2);">
      <h2 style="font-size:1rem; font-weight:700; color:var(--clr-danger); margin-bottom:.75rem;">⚠️ Zone de danger</h2>
      <p class="text-muted" style="font-size:.875rem; margin-bottom:1.25rem;">
        La suppression de votre compte est irréversible. Toutes vos données (défis, participations, commentaires) seront définitivement supprimées.
      </p>

      <button
        class="btn btn-danger btn-sm"
        id="btn-show-delete-modal"
        onclick="document.getElementById('delete-modal').style.display='flex'"
      >
        🗑️ Supprimer mon compte
      </button>
    </div>

  </div>
</div>

<!-- Delete Account Modal -->
<div id="delete-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.7); z-index:9999; align-items:center; justify-content:center; padding:1rem;">
  <div class="auth-card" style="max-width:440px; animation:slideUp .3s ease;">
    <h2 style="font-size:1.25rem; font-weight:800; color:var(--clr-danger); margin-bottom:.75rem;">Confirmer la suppression</h2>
    <p class="text-muted" style="font-size:.875rem; margin-bottom:1.5rem;">Entrez votre mot de passe pour confirmer la suppression définitive de votre compte.</p>

    <form method="POST" action="<?= BASE_URL ?>/index.php?page=delete-account" id="delete-account-form">
      <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= htmlspecialchars($csrf) ?>">
      <div class="form-group" style="margin-bottom:1rem;">
        <label class="form-label" for="delete-password">Mot de passe</label>
        <input type="password" id="delete-password" name="password" class="form-control" required placeholder="••••••••">
      </div>
      <div class="flex gap-2" style="justify-content:flex-end;">
        <button type="button" class="btn btn-ghost" onclick="document.getElementById('delete-modal').style.display='none'" id="btn-cancel-delete">Annuler</button>
        <button type="submit" class="btn btn-danger" id="btn-confirm-delete">Supprimer définitivement</button>
      </div>
    </form>
  </div>
</div>
