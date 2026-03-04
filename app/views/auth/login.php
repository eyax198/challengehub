<div class="auth-wrapper">
  <div class="auth-card slide-up">

    <div class="auth-card__header">
      <div class="auth-card__icon">⚡</div>
      <h1 class="auth-card__title">Connexion</h1>
      <p class="auth-card__subtitle">Content de vous revoir sur ChallengeHub !</p>
    </div>

    <form class="auth-card__form" method="POST" action="<?= BASE_URL ?>/index.php?page=login" id="login-form" novalidate>
      <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= htmlspecialchars($csrf) ?>">

      <div class="form-group">
        <label class="form-label" for="login-email">Adresse email</label>
        <input
          type="email"
          id="login-email"
          name="email"
          class="form-control"
          placeholder="vous@exemple.com"
          required
          autocomplete="email"
          value="<?= htmlspecialchars($_SESSION['form_data']['email'] ?? '') ?>"
        >
      </div>

      <div class="form-group">
        <label class="form-label" for="login-password">Mot de passe</label>
        <input
          type="password"
          id="login-password"
          name="password"
          class="form-control"
          placeholder="••••••••"
          required
          autocomplete="current-password"
        >
      </div>

      <button type="submit" class="btn btn-primary" id="login-submit" style="width:100%; justify-content:center; padding:.85rem;">
        Se connecter
      </button>

      <div class="auth-divider">ou</div>

      <p class="text-center" style="font-size:.875rem; color:var(--clr-text-muted);">
        Pas encore de compte ?
        <a href="<?= BASE_URL ?>/index.php?page=register" style="color:var(--clr-primary-l); font-weight:600;" id="login-register-link">
          Créer un compte →
        </a>
      </p>
    </form>

  </div>
</div>
<?php unset($_SESSION['form_data']); ?>
