<?php

require_once __DIR__ . '/Model.php';

// MODÈLE VOTE
// C'est ici qu'on gère les likes
class Vote extends Model {

    // On bascule le vote (si il existe on l'enlève, sinon on l'ajoute)
    public function cast($submissionId, $userId) {
        $existing = $this->getByUserAndSubmission($userId, $submissionId);

        if ($existing) {
            // Si le vote existe déjà, on le supprime (Retirer le vote)
            $this->deleteById('votes', (int)$existing['id']);
            return ['action' => 'removed'];
        }

        // Sinon, on ajoute un nouveau vote
        $sql = "INSERT INTO votes (submission_id, user_id, value, created_at)
                VALUES (?, ?, 1, NOW())";
        
        $this->query($sql, [$submissionId, $userId]);
        
        return ['action' => 'added'];
    }

    /**
     * Trouve un vote spécifique
     */
    public function getByUserAndSubmission($userId, $submissionId) {
        $sql = "SELECT * FROM votes WHERE user_id = ? AND submission_id = ?";
        return $this->query($sql, [$userId, $submissionId])->fetch();
    }

    // Compter les votes d'un projet
    public function countBySubmission($submissionId) {
        $sql = "SELECT COUNT(*) FROM votes WHERE submission_id = ?";
        return (int) $this->query($sql, [$submissionId])->fetchColumn();
    }

    /**
     * Vérifie si l'utilisateur a déjà voté (pour l'affichage du bouton)
     */
    public function hasVoted($userId, $submissionId) {
        $vote = $this->getByUserAndSubmission($userId, $submissionId);
        return $vote !== false;
    }
}
