<?php

require_once __DIR__ . '/Model.php';

// MODÈLE USER
// On gère les comptes des utilisateurs
class User extends Model {

    // Création d'un compte (Inscription)
    public function create($data) {
        // On hache le mot de passe pour la sécurité
        $hashed = password_hash($data['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, email, password, avatar, bio, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())";
        
        $params = [
            $data['username'],
            $data['email'],
            $hashed,
            $data['avatar']  ?? null,
            $data['bio']     ?? null,
        ];

        $this->query($sql, $params);

        return $this->lastInsertId();
    }

    // Trouver un membre avec son ID
    public function findById($id) {
        $stmt = $this->query("SELECT * FROM users WHERE id = ?", [$id]);
        return $stmt->fetch();
    }

    /**
     * Récupère tous les utilisateurs (utile pour les statistiques)
     */
    public function getAll() {
        return $this->query("SELECT * FROM users")->fetchAll();
    }

    /**
     * Trouve un utilisateur par son email (utile pour la connexion)
     */
    public function findByEmail($email) {
        $stmt = $this->query("SELECT * FROM users WHERE email = ?", [$email]);
        return $stmt->fetch();
    }

    // Pour vérifier si l'email et le mot de passe sont corrects
    public function authenticate($email, $password) {
        $user = $this->findByEmail($email);
        
        // On vérifie si l'utilisateur existe ET si le mot de passe correspond au hachage
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }

    // On récupère les chiffres de l'user (combien de défis, participations...)
    public function getStats($userId) {
        $challenges    = $this->query("SELECT COUNT(*) FROM challenges WHERE user_id = ?", [$userId])->fetchColumn();
        $submissions   = $this->query("SELECT COUNT(*) FROM submissions WHERE user_id = ?", [$userId])->fetchColumn();
        $votesReceived = $this->query(
            "SELECT COUNT(*) FROM votes v
             JOIN submissions s ON v.submission_id = s.id
             WHERE s.user_id = ?",
            [$userId]
        )->fetchColumn();

        return [
            'challenges'     => (int) $challenges,
            'submissions'    => (int) $submissions,
            'votes_received' => (int) $votesReceived,
        ];
    }

    /**
     * Met à jour les informations du profil
     */
    public function update($id, $data) {
        $fields = [];
        $params = [];

        // On construit la requête dynamiquement selon les champs remplis
        if (!empty($data['username'])) { $fields[] = 'username = ?'; $params[] = $data['username']; }
        if (!empty($data['email']))    { $fields[] = 'email = ?';    $params[] = $data['email'];    }
        if (!empty($data['bio']))      { $fields[] = 'bio = ?';      $params[] = $data['bio'];      }
        if (!empty($data['avatar']))   { $fields[] = 'avatar = ?';   $params[] = $data['avatar'];   }
        
        if (!empty($data['password'])) {
            $fields[] = 'password = ?';
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (empty($fields)) return false;

        $params[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        
        return $this->query($sql, $params)->rowCount() > 0;
    }

    /**
     * Supprime un compte utilisateur
     */
    public function delete($id) {
        return $this->deleteById('users', $id);
    }
}
