<?php

require_once __DIR__ . '/Model.php';

class Vote extends Model {

    // ── Cast / Toggle ─────────────────────────────────────────
    public function cast(int $submissionId, int $userId): array {
        $existing = $this->getByUserAndSubmission($userId, $submissionId);

        if ($existing) {
            // Remove the vote (toggle off)
            $this->deleteById('votes', (int)$existing['id']);
            return ['action' => 'removed'];
        }

        $this->query(
            "INSERT INTO votes (submission_id, user_id, value, created_at)
             VALUES (?, ?, 1, NOW())",
            [$submissionId, $userId]
        );
        return ['action' => 'added'];
    }

    // ── Read ─────────────────────────────────────────────────
    public function getByUserAndSubmission(int $userId, int $submissionId): array|false {
        return $this->query(
            "SELECT * FROM votes WHERE user_id = ? AND submission_id = ?",
            [$userId, $submissionId]
        )->fetch();
    }

    public function countBySubmission(int $submissionId): int {
        return (int) $this->query(
            "SELECT COALESCE(SUM(value), 0) FROM votes WHERE submission_id = ?",
            [$submissionId]
        )->fetchColumn();
    }

    public function hasVoted(int $userId, int $submissionId): bool {
        return (bool) $this->getByUserAndSubmission($userId, $submissionId);
    }
}
