<!-- Formulaire d'inscription -->

<div class="auth-wrapper">
  <div class="auth-card slide-up">

    <div class="auth-card__header">
      <div class="auth-card__icon">🚀</div>
      <h1 class="auth-card__title">Rejoignez-nous !</h1>
      <p class="auth-card__subtitle">Inscrivez-vous et montrez votre talent.</p>
    </div>

    <!-- Formulaire d'Inscription -->
    <form class="auth-card__form" method="POST" action="index.php?page=register" enctype="multipart/form-data">
      <!-- Token CSRF pour la sécurité -->
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

      <!-- Nom d'utilisateur -->
      <div class="form-group">
        <label class="form-label" for="reg-username">Nom d'utilisateur</label>
        <input type="text" id="reg-username" name="username" class="form-control" placeholder="Pseudo" required>
      </div>

      <!-- Email -->
      <div class="form-group">
        <label class="form-label" for="reg-email">Adresse email</label>
        <input type="email" id="reg-email" name="email" class="form-control" placeholder="votre@email.com" required>
      </div>

      <!-- Mot de passe -->
      <div class="form-group">
        <label class="form-label" for="reg-password">Mot de passe</label>
        <input type="password" id="reg-password" name="password" class="form-control" placeholder="8 caractères min." required>
      </div>

      <!-- Confirmation du mot de passe -->
      <div class="form-group">
        <label class="form-label" for="reg-password-confirm">Confirmez le mot de passe</label>
        <input type="password" id="reg-password-confirm" name="password_confirm" class="form-control" placeholder="Retapez le mot de passe" required>
      </div>

      <!-- Avatar (Photo de profil) -->
      <div class="form-group">
        <label class="form-label">Photo de profil (Optionnelle)</label>
        <input type="file" name="avatar" class="form-control" style="font-size:.8rem; padding:.5rem;">
      </div>

      <!-- Bouton d'inscription -->
      <button type="submit" class="btn btn-primary" style="width:100%; padding:.85rem; justify-content:center;">
        Créer mon compte
      </button>

      <div class="auth-divider">ou</div>

      <p class="text-center" style="font-size:.875rem; color:var(--clr-text-muted);">
        Vous avez déjà un compte ?
        <a href="index.php?page=login" style="color:var(--clr-primary-l); font-weight:600;">
          Connectez-vous →
        </a>
      </p>
    </form>

  </div>
</div>
