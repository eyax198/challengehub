<?php

require_once __DIR__ . '/Model.php';

/**
 * Modèle Comment - Gère les commentaires sur les projets
 */
class Comment extends Model {

    /**
     * Crée un nouveau commentaire
     */
    public function create($data) {
        $sql = "INSERT INTO comments (submission_id, user_id, content, created_at)
                VALUES (?, ?, ?, NOW())";
        
        $params = [
            $data['submission_id'],
            $data['user_id'],
            $data['content'],
        ];

        $this->query($sql, $params);
        return $this->lastInsertId();
    }

    /**
     * Récupère un commentaire par son ID (avec auteur)
     */
    public function findById($id) {
        $sql = "SELECT cm.*, u.username, u.avatar
                FROM comments cm
                JOIN users u ON cm.user_id = u.id
                WHERE cm.id = ?";
        
        return $this->query($sql, [$id])->fetch();
    }

    /**
     * Récupère tous les commentaires d'une participation spécifique
     */
    public function getBySubmission($submissionId) {
        $sql = "SELECT cm.*, u.username, u.avatar
                FROM comments cm
                JOIN users u ON cm.user_id = u.id
                WHERE cm.submission_id = ?
                ORDER BY cm.created_at ASC";
        
        return $this->query($sql, [$submissionId])->fetchAll();
    }

    /**
     * Supprime un commentaire
     */
    public function delete($id) {
        return $this->deleteById('comments', $id);
    }
}
