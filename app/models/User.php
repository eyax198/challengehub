<?php

require_once __DIR__ . '/Model.php';

class User extends Model {

    // ── Create ────────────────────────────────────────────────
    public function create(array $data): int|false {
        $hashed = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);

        $this->query(
            "INSERT INTO users (username, email, password, avatar, bio, created_at)
             VALUES (?, ?, ?, ?, ?, NOW())",
            [
                $data['username'],
                $data['email'],
                $hashed,
                $data['avatar']  ?? null,
                $data['bio']     ?? null,
            ]
        );

        return (int) $this->lastInsertId();
    }

    // ── Read ─────────────────────────────────────────────────
    public function findById(string $table = 'users', int $id = 0): array|false {
        return $this->query("SELECT * FROM users WHERE id = ?", [$id])->fetch();
    }

    public function findByEmail(string $email): array|false {
        return $this->query("SELECT * FROM users WHERE email = ?", [$email])->fetch();
    }

    public function findByUsername(string $username): array|false {
        return $this->query("SELECT * FROM users WHERE username = ?", [$username])->fetch();
    }

    public function getAll(): array {
        return $this->query("SELECT id, username, email, avatar, bio, created_at FROM users ORDER BY created_at DESC")->fetchAll();
    }

    public function getStats(int $userId): array {
        $challenges   = $this->query("SELECT COUNT(*) FROM challenges WHERE user_id = ?",   [$userId])->fetchColumn();
        $submissions  = $this->query("SELECT COUNT(*) FROM submissions WHERE user_id = ?",  [$userId])->fetchColumn();
        $votesReceived = $this->query(
            "SELECT COUNT(*) FROM votes v
             JOIN submissions s ON v.submission_id = s.id
             WHERE s.user_id = ?",
            [$userId]
        )->fetchColumn();

        return [
            'challenges'    => (int) $challenges,
            'submissions'   => (int) $submissions,
            'votes_received' => (int) $votesReceived,
        ];
    }

    // ── Authenticate ─────────────────────────────────────────
    public function authenticate(string $email, string $password): array|false {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    // ── Update ────────────────────────────────────────────────
    public function update(int $id, array $data): bool {
        $fields = [];
        $params = [];

        if (!empty($data['username'])) { $fields[] = 'username = ?'; $params[] = $data['username']; }
        if (!empty($data['email']))    { $fields[] = 'email = ?';    $params[] = $data['email'];    }
        if (!empty($data['bio']))      { $fields[] = 'bio = ?';      $params[] = $data['bio'];      }
        if (!empty($data['avatar']))   { $fields[] = 'avatar = ?';   $params[] = $data['avatar'];   }
        if (!empty($data['password'])) {
            $fields[] = 'password = ?';
            $params[] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        }

        if (empty($fields)) return false;

        $params[] = $id;
        return $this->query(
            "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?",
            $params
        )->rowCount() > 0;
    }

    // ── Delete ────────────────────────────────────────────────
    public function delete(int $id): bool {
        return $this->deleteById('users', $id);
    }
}
