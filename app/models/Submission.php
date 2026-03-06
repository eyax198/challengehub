<?php

require_once __DIR__ . '/Model.php';

// MODÈLE SUBMISSION
// Gestion des projets envoyés par les membres
class Submission extends Model {

    // On enregistre une nouvelle participation
    public function create($data) {
        $sql = "INSERT INTO submissions (challenge_id, user_id, description, image, link, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())";
        
        $params = [
            $data['challenge_id'],
            $data['user_id'],
            $data['description'],
            $data['image'] ?? null,
            $data['link']  ?? null,
        ];

        $this->query($sql, $params);
        return $this->lastInsertId();
    }

    // On cherche un projet par son ID avec les infos de l'auteur et du défi
    public function findById($id) {
        $sql = "SELECT s.*, u.username, u.avatar,
                       c.title AS challenge_title,
                       (SELECT COUNT(*) FROM votes WHERE submission_id = s.id) AS vote_count
                FROM submissions s
                JOIN users u ON s.user_id = u.id
                JOIN challenges c ON s.challenge_id = c.id
                WHERE s.id = ?";
        
        return $this->query($sql, [$id])->fetch();
    }

    // Récupère les projets d'un défi (on peut trier par top votes)
    public function getByChallenge($challengeId, $sort = 'newest') {
        // Définition du tri
        $orderBy = "s.created_at DESC";
        if ($sort === 'top')    $orderBy = "vote_count DESC";
        if ($sort === 'oldest') $orderBy = "s.created_at ASC";

        // Requête avec compteurs de votes et commentaires
        $sql = "SELECT s.*, u.username, u.avatar,
                       (SELECT COUNT(*) FROM votes WHERE submission_id = s.id) AS vote_count,
                       (SELECT COUNT(*) FROM comments WHERE submission_id = s.id) AS comment_count
                FROM submissions s
                JOIN users u ON s.user_id = u.id
                WHERE s.challenge_id = ?
                ORDER BY $orderBy";

        return $this->query($sql, [$challengeId])->fetchAll();
    }

    /**
     * Récupère les participations d'un utilisateur spécifique
     */
    public function getByUser($userId) {
        $sql = "SELECT s.*, c.title AS challenge_title,
                       (SELECT COUNT(*) FROM votes WHERE submission_id = s.id) AS vote_count
                FROM submissions s
                JOIN challenges c ON s.challenge_id = c.id
                WHERE s.user_id = ?
                ORDER BY s.created_at DESC";
        
        return $this->query($sql, [$userId])->fetchAll();
    }

    // On fait le classement des meilleurs projets
    public function getTopSubmissions($limit = 10) {
        $sql = "SELECT s.*, u.username, u.avatar,
                       c.title AS challenge_title,
                       (SELECT COUNT(*) FROM votes WHERE submission_id = s.id) AS vote_count
                FROM submissions s
                JOIN users u ON s.user_id = u.id
                JOIN challenges c ON s.challenge_id = c.id
                ORDER BY vote_count DESC
                LIMIT $limit";
        
        return $this->query($sql)->fetchAll();
    }

    /**
     * Vérifie si un utilisateur a déjà participé à un défi
     */
    public function existsByUserAndChallenge($userId, $challengeId) {
        $sql = "SELECT COUNT(*) FROM submissions WHERE user_id = ? AND challenge_id = ?";
        $count = $this->query($sql, [$userId, $challengeId])->fetchColumn();
        return (int)$count > 0;
    }

    /**
     * Met à jour une participation
     */
    public function update($id, $data) {
        $fields = [];
        $params = [];

        if (isset($data['description'])) { $fields[] = 'description = ?'; $params[] = $data['description']; }
        if (isset($data['image']))       { $fields[] = 'image = ?';       $params[] = $data['image'];       }
        if (isset($data['link']))        { $fields[] = 'link = ?';        $params[] = $data['link'];        }

        if (empty($fields)) return false;

        $params[] = $id;
        $sql = "UPDATE submissions SET " . implode(', ', $fields) . " WHERE id = ?";
        return $this->query($sql, $params)->rowCount() > 0;
    }

    /**
     * Supprime une participation
     */
    public function delete($id) {
        return $this->deleteById('submissions', $id);
    }
}
