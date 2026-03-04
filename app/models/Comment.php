<?php

require_once __DIR__ . '/Model.php';

class Comment extends Model {

    // ── Create ────────────────────────────────────────────────
    public function create(array $data): int|false {
        $this->query(
            "INSERT INTO comments (submission_id, user_id, content, created_at)
             VALUES (?, ?, ?, NOW())",
            [
                $data['submission_id'],
                $data['user_id'],
                $data['content'],
            ]
        );
        return (int) $this->lastInsertId();
    }

    // ── Read ─────────────────────────────────────────────────
    public function findById(string $table = 'comments', int $id = 0): array|false {
        return $this->query(
            "SELECT cm.*, u.username, u.avatar
             FROM comments cm
             JOIN users u ON cm.user_id = u.id
             WHERE cm.id = ?",
            [$id]
        )->fetch();
    }

    public function getBySubmission(int $submissionId): array {
        return $this->query(
            "SELECT cm.*, u.username, u.avatar
             FROM comments cm
             JOIN users u ON cm.user_id = u.id
             WHERE cm.submission_id = ?
             ORDER BY cm.created_at ASC",
            [$submissionId]
        )->fetchAll();
    }

    // ── Delete ────────────────────────────────────────────────
    public function delete(int $id): bool {
        return $this->deleteById('comments', $id);
    }
}
