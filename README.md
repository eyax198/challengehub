# ChallengeHub ⚡

**Plateforme collaborative de défis créatifs**  
Projet — Programmation Web 2 · 2ème année E-Business  
PHP Orienté Objet · MySQL · Architecture MVC · Sans framework

---

## 📦 Installation

### Prérequis
- XAMPP (ou WAMPServer / EasyPHP)
- PHP ≥ 8.1
- MySQL ≥ 5.7

### Étapes

1. **Copier le projet** dans le répertoire `htdocs` de XAMPP :
   ```
   C:\xampp\htdocs\challengehub_db\
   ```

2. **Démarrer XAMPP** — ouvrez le **XAMPP Control Panel** et démarrez **Apache** et **MySQL**

3. **Créer la base de données** — ouvrez phpMyAdmin : [http://localhost/phpmyadmin](http://localhost/phpmyadmin) et exécutez :
   ```
   database/challengehub_db.sql
   ```

4. **Configurer la connexion** si besoin dans `config/database.php` :
   ```php
   private string $host     = 'localhost';
   private string $dbName   = 'challengehub_db';
   private string $username = 'root';
   private string $password = '';
   ```

5. **Vérifier `BASE_URL`** dans `config/config.php` (déjà correct pour XAMPP) :
   ```php
   define('BASE_URL', 'http://localhost/challengehub_db');
   ```

5. **Ouvrir le navigateur** : [http://localhost/challengehub_db](http://localhost/challengehub_db)

---

## 🗂️ Structure du projet

```
challengehub_db/
├── app/
│   ├── controllers/
│   │   ├── Controller.php          ← Contrôleur de base (abstrait)
│   │   ├── AuthController.php      ← Connexion / Inscription / Déconnexion
│   │   ├── UserController.php      ← Profil / Édition / Suppression
│   │   ├── ChallengeController.php ← CRUD des défis
│   │   ├── SubmissionController.php← CRUD participations + votes + commentaires
│   │   └── HomeController.php      ← Page d'accueil
│   ├── models/
│   │   ├── Model.php               ← Modèle de base (abstrait)
│   │   ├── User.php
│   │   ├── Challenge.php
│   │   ├── Submission.php
│   │   ├── Comment.php
│   │   └── Vote.php
│   └── views/
│       ├── layout/
│       │   ├── header.php
│       │   └── footer.php
│       ├── home/index.php
│       ├── auth/{login,register}.php
│       ├── challenge/{index,show,create,edit}.php
│       ├── submission/{show,edit,leaderboard}.php
│       ├── user/{profile,edit_profile}.php
│       └── errors/404.php
├── config/
│   ├── config.php                  ← Constantes de l'application
│   └── database.php                ← Singleton PDO
├── database/
│   └── challengehub_db.sql            ← Script SQL complet
├── public/
│   ├── css/style.css               ← Feuille de styles complète
│   ├── js/app.js                   ← JavaScript client
│   └── images/uploads/             ← Uploads utilisateurs
├── .htaccess
└── index.php                       ← Front Controller (routeur)
```

---

## ✅ Fonctionnalités implémentées

| Fonctionnalité | Statut |
|---|---|
| Inscription / Connexion / Déconnexion | ✅ |
| Hashage `password_hash` (bcrypt cost 12) | ✅ |
| Gestion de session sécurisée | ✅ |
| Protection CSRF (token en session) | ✅ |
| Protection XSS (`htmlspecialchars`) | ✅ |
| Protection injection SQL (PDO + requêtes préparées) | ✅ |
| CRUD défis (avec image) | ✅ |
| CRUD participations (image + lien) | ✅ |
| Commentaires avec auteur et date | ✅ |
| Système de vote (1 vote/participation) | ✅ |
| Vote AJAX (sans rechargement) | ✅ |
| Commentaires AJAX | ✅ |
| Recherche par mot-clé | ✅ |
| Filtrage par catégorie | ✅ |
| Tri popularité / date | ✅ |
| Pagination | ✅ |
| Classement (leaderboard) | ✅ |
| Profil utilisateur avec stats | ✅ |
| Modification du profil + avatar | ✅ |
| Suppression de compte (confirmée) | ✅ |
| Architecture MVC propre | ✅ |
| Design responsive et moderne | ✅ |

---

## 🔐 Sécurité

- **PDO** avec requêtes préparées (anti-injection SQL)
- **`htmlspecialchars()`** sur toutes les sorties (anti-XSS)
- **Tokens CSRF** vérifiés sur chaque formulaire POST
- **`password_hash()`** avec bcrypt cost 12
- **`session_regenerate_id()`** après la connexion (anti-session fixation)
- **`session.cookie_httponly`** et **`session.use_strict_mode`**
- Vérification de propriété pour les modifications/suppressions

---

## 👤 Comptes de démonstration

| Email | Mot de passe |
|---|---|
| alice@demo.com | `password` |
| bob@demo.com   | `password` |
| carol@demo.com | `password` |

---

## 📝 Licence

Projet académique — Tous droits réservés.
