<?php

require_once __DIR__ . '/Model.php';

class Submission extends Model {

    // ── Create ────────────────────────────────────────────────
    public function create(array $data): int|false {
        $this->query(
            "INSERT INTO submissions (challenge_id, user_id, description, image, link, created_at)
             VALUES (?, ?, ?, ?, ?, NOW())",
            [
                $data['challenge_id'],
                $data['user_id'],
                $data['description'],
                $data['image'] ?? null,
                $data['link']  ?? null,
            ]
        );
        return (int) $this->lastInsertId();
    }

    // ── Read ─────────────────────────────────────────────────
    public function findById(string $table = 'submissions', int $id = 0): array|false {
        return $this->query(
            "SELECT s.*, u.username, u.avatar,
                    c.title AS challenge_title,
                    COALESCE(SUM(v.value), 0) AS vote_count
             FROM submissions s
             JOIN users u ON s.user_id = u.id
             JOIN challenges c ON s.challenge_id = c.id
             LEFT JOIN votes v ON v.submission_id = s.id
             WHERE s.id = ?
             GROUP BY s.id",
            [$id]
        )->fetch();
    }

    public function getByChallenge(int $challengeId, string $sort = 'newest'): array {
        $orderBy = match($sort) {
            'top'    => 'vote_count DESC',
            'oldest' => 's.created_at ASC',
            default  => 's.created_at DESC',
        };

        return $this->query(
            "SELECT s.*, u.username, u.avatar,
                    COALESCE(SUM(v.value), 0) AS vote_count,
                    COUNT(DISTINCT cm.id) AS comment_count
             FROM submissions s
             JOIN users u ON s.user_id = u.id
             LEFT JOIN votes v ON v.submission_id = s.id
             LEFT JOIN comments cm ON cm.submission_id = s.id
             WHERE s.challenge_id = ?
             GROUP BY s.id
             ORDER BY {$orderBy}",
            [$challengeId]
        )->fetchAll();
    }

    public function getByUser(int $userId): array {
        return $this->query(
            "SELECT s.*, c.title AS challenge_title,
                    COALESCE(SUM(v.value), 0) AS vote_count
             FROM submissions s
             JOIN challenges c ON s.challenge_id = c.id
             LEFT JOIN votes v ON v.submission_id = s.id
             WHERE s.user_id = ?
             GROUP BY s.id
             ORDER BY s.created_at DESC",
            [$userId]
        )->fetchAll();
    }

    public function getTopSubmissions(int $limit = 10): array {
        return $this->query(
            "SELECT s.*, u.username, u.avatar,
                    c.title AS challenge_title,
                    COALESCE(SUM(v.value), 0) AS vote_count
             FROM submissions s
             JOIN users u ON s.user_id = u.id
             JOIN challenges c ON s.challenge_id = c.id
             LEFT JOIN votes v ON v.submission_id = s.id
             GROUP BY s.id
             ORDER BY vote_count DESC
             LIMIT ?",
            [$limit]
        )->fetchAll();
    }

    public function existsByUserAndChallenge(int $userId, int $challengeId): bool {
        return (bool) $this->query(
            "SELECT COUNT(*) FROM submissions WHERE user_id = ? AND challenge_id = ?",
            [$userId, $challengeId]
        )->fetchColumn();
    }

    // ── Update ────────────────────────────────────────────────
    public function update(int $id, array $data): bool {
        $fields = [];
        $params = [];

        if (isset($data['description'])) { $fields[] = 'description = ?'; $params[] = $data['description']; }
        if (isset($data['image']))       { $fields[] = 'image = ?';       $params[] = $data['image'];       }
        if (isset($data['link']))        { $fields[] = 'link = ?';        $params[] = $data['link'];        }

        if (empty($fields)) return false;

        $params[] = $id;
        return $this->query(
            "UPDATE submissions SET " . implode(', ', $fields) . " WHERE id = ?",
            $params
        )->rowCount() > 0;
    }

    // ── Delete ────────────────────────────────────────────────
    public function delete(int $id): bool {
        return $this->deleteById('submissions', $id);
    }
}
