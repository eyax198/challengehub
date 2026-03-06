<!-- Formulaire de login -->

<div class="auth-wrapper">
  <div class="auth-card slide-up">

    <div class="auth-card__header">
      <div class="auth-card__icon">🔑</div>
      <h1 class="auth-card__title">Connexion</h1>
      <p class="auth-card__subtitle">Content de vous revoir sur ChallengeHub !</p>
    </div>

    <!-- Formulaire de Connexion -->
    <form class="auth-card__form" method="POST" action="index.php?page=login" id="login-form">
    <!-- Sécurité CSRF pour le formulaire -->
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

      <!-- Email -->
      <div class="form-group">
        <label class="form-label" for="login-email">Adresse email</label>
        <input
          type="email"
          id="login-email"
          name="email"
          class="form-control"
          placeholder="votre@email.com"
          required
        >
      </div>

      <!-- Mot de passe -->
      <div class="form-group">
        <label class="form-label" for="login-password">Mot de passe</label>
        <input
          type="password"
          id="login-password"
          name="password"
          class="form-control"
          placeholder="••••••••"
          required
        >
      </div>

      <!-- Bouton de validation -->
      <button type="submit" class="btn btn-primary" id="login-submit" style="width:100%; padding:.85rem; justify-content:center;">
        Se connecter
      </button>

      <div class="auth-divider">ou</div>

      <p class="text-center" style="font-size:.875rem; color:var(--clr-text-muted);">
        Pas encore de compte ?
        <a href="index.php?page=register" style="color:var(--clr-primary-l); font-weight:600;">
          Créer un compte →
        </a>
      </p>
    </form>

  </div>
</div>
