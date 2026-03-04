<div class="auth-wrapper">
  <div class="auth-card slide-up" style="max-width:560px;">

    <div class="auth-card__header">
      <div class="auth-card__icon">🚀</div>
      <h1 class="auth-card__title">Créer un compte</h1>
      <p class="auth-card__subtitle">Rejoignez la communauté ChallengeHub dès maintenant</p>
    </div>

    <?php $formData = $_SESSION['form_data'] ?? []; unset($_SESSION['form_data']); ?>

    <form class="auth-card__form" method="POST" action="<?= BASE_URL ?>/index.php?page=register" id="register-form" enctype="multipart/form-data" novalidate>
      <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= htmlspecialchars($csrf) ?>">

      <div class="form-group">
        <label class="form-label" for="reg-username">Nom d'utilisateur</label>
        <input
          type="text"
          id="reg-username"
          name="username"
          class="form-control"
          placeholder="john_doe"
          required
          minlength="3"
          maxlength="50"
          value="<?= htmlspecialchars($formData['username'] ?? '') ?>"
        >
      </div>

      <div class="form-group">
        <label class="form-label" for="reg-email">Adresse email</label>
        <input
          type="email"
          id="reg-email"
          name="email"
          class="form-control"
          placeholder="vous@exemple.com"
          required
          autocomplete="email"
          value="<?= htmlspecialchars($formData['email'] ?? '') ?>"
        >
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="reg-password">Mot de passe</label>
          <input
            type="password"
            id="reg-password"
            name="password"
            class="form-control"
            placeholder="••••••••"
            required
            minlength="8"
            autocomplete="new-password"
          >
        </div>
        <div class="form-group">
          <label class="form-label" for="reg-password2">Confirmer</label>
          <input
            type="password"
            id="reg-password2"
            name="password2"
            class="form-control"
            placeholder="••••••••"
            required
            autocomplete="new-password"
          >
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="reg-bio">Bio (optionnel)</label>
        <textarea id="reg-bio" name="bio" class="form-control" placeholder="Parlez-nous de vous..." rows="3" maxlength="300"></textarea>
      </div>

      <div class="form-group">
        <label class="form-label">Photo de profil (optionnel)</label>
        <div class="upload-area" id="avatar-upload-area">
          <input type="file" name="avatar" id="reg-avatar" accept="image/*">
          <div id="avatar-upload-text">
            <p style="font-size:2rem; margin-bottom:.5rem">📷</p>
            <p style="font-size:.85rem; color:var(--clr-text-muted)">Cliquez pour choisir une image</p>
            <p style="font-size:.75rem; color:var(--clr-text-dim); margin-top:.25rem">JPG, PNG, GIF, WebP · max 5 Mo</p>
          </div>
          <img id="avatar-preview" style="display:none; max-height:100px; border-radius:50%; margin:0 auto;" alt="Aperçu">
        </div>
      </div>

      <button type="submit" class="btn btn-primary" id="register-submit" style="width:100%; justify-content:center; padding:.85rem;">
        Créer mon compte
      </button>

      <p class="text-center" style="font-size:.875rem; color:var(--clr-text-muted);">
        Déjà membre ?
        <a href="<?= BASE_URL ?>/index.php?page=login" style="color:var(--clr-primary-l); font-weight:600;" id="register-login-link">
          Se connecter →
        </a>
      </p>
    </form>

  </div>
</div>
