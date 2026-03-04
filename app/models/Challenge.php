<?php

require_once __DIR__ . '/Model.php';

class Challenge extends Model {

    // ── Create ────────────────────────────────────────────────
    public function create(array $data): int|false {
        $this->query(
            "INSERT INTO challenges (user_id, title, description, category, deadline, image, created_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW())",
            [
                $data['user_id'],
                $data['title'],
                $data['description'],
                $data['category'],
                $data['deadline'],
                $data['image'] ?? null,
            ]
        );
        return (int) $this->lastInsertId();
    }

    // ── Read ─────────────────────────────────────────────────
    public function findById(string $table = 'challenges', int $id = 0): array|false {
        return $this->query(
            "SELECT c.*, u.username, u.avatar
             FROM challenges c
             JOIN users u ON c.user_id = u.id
             WHERE c.id = ?",
            [$id]
        )->fetch();
    }

    public function getAll(array $filters = [], int $page = 1): array {
        $where  = ['1=1'];
        $params = [];

        if (!empty($filters['keyword'])) {
            $where[]  = "(c.title LIKE ? OR c.description LIKE ?)";
            $kw       = '%' . $filters['keyword'] . '%';
            $params[] = $kw;
            $params[] = $kw;
        }

        if (!empty($filters['category'])) {
            $where[]  = "c.category = ?";
            $params[] = $filters['category'];
        }

        $orderBy = match($filters['sort'] ?? 'newest') {
            'popular' => 'submissions_count DESC',
            'oldest'  => 'c.created_at ASC',
            default   => 'c.created_at DESC',
        };

        $perPage = PER_PAGE;
        $offset  = ($page - 1) * $perPage;

        $sql = "SELECT c.*, u.username, u.avatar,
                       COUNT(DISTINCT s.id) AS submissions_count
                FROM challenges c
                JOIN users u ON c.user_id = u.id
                LEFT JOIN submissions s ON s.challenge_id = c.id
                WHERE " . implode(' AND ', $where) . "
                GROUP BY c.id
                ORDER BY {$orderBy}
                LIMIT {$perPage} OFFSET {$offset}";

        return $this->query($sql, $params)->fetchAll();
    }

    public function countAll(array $filters = []): int {
        $where  = ['1=1'];
        $params = [];

        if (!empty($filters['keyword'])) {
            $where[]  = "(title LIKE ? OR description LIKE ?)";
            $kw       = '%' . $filters['keyword'] . '%';
            $params[] = $kw;
            $params[] = $kw;
        }

        if (!empty($filters['category'])) {
            $where[]  = "category = ?";
            $params[] = $filters['category'];
        }

        return (int) $this->query(
            "SELECT COUNT(*) FROM challenges WHERE " . implode(' AND ', $where),
            $params
        )->fetchColumn();
    }

    public function getByUser(int $userId): array {
        return $this->query(
            "SELECT c.*, COUNT(DISTINCT s.id) AS submissions_count
             FROM challenges c
             LEFT JOIN submissions s ON s.challenge_id = c.id
             WHERE c.user_id = ?
             GROUP BY c.id
             ORDER BY c.created_at DESC",
            [$userId]
        )->fetchAll();
    }

    public function getCategories(): array {
        return $this->query("SELECT DISTINCT category FROM challenges ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getRecent(int $limit = 6): array {
        return $this->query(
            "SELECT c.*, u.username, u.avatar
             FROM challenges c
             JOIN users u ON c.user_id = u.id
             ORDER BY c.created_at DESC
             LIMIT ?",
            [$limit]
        )->fetchAll();
    }

    // ── Update ────────────────────────────────────────────────
    public function update(int $id, array $data): bool {
        $fields = [];
        $params = [];

        if (isset($data['title']))       { $fields[] = 'title = ?';       $params[] = $data['title'];       }
        if (isset($data['description'])) { $fields[] = 'description = ?'; $params[] = $data['description']; }
        if (isset($data['category']))    { $fields[] = 'category = ?';    $params[] = $data['category'];    }
        if (isset($data['deadline']))    { $fields[] = 'deadline = ?';    $params[] = $data['deadline'];    }
        if (isset($data['image']))       { $fields[] = 'image = ?';       $params[] = $data['image'];       }

        if (empty($fields)) return false;

        $params[] = $id;
        return $this->query(
            "UPDATE challenges SET " . implode(', ', $fields) . " WHERE id = ?",
            $params
        )->rowCount() > 0;
    }

    // ── Delete ────────────────────────────────────────────────
    public function delete(int $id): bool {
        return $this->deleteById('challenges', $id);
    }
}
