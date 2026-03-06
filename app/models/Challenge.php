<?php

require_once __DIR__ . '/Model.php';

// MODÈLE CHALLENGE
// Ici on gère toutes les données liées aux défis
class Challenge extends Model {

    // Insertion d'un défi dans la table
    public function create($data) {
        $sql = "INSERT INTO challenges (user_id, title, description, category, deadline, image, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        $params = [
            $data['user_id'],
            $data['title'],
            $data['description'],
            $data['category'],
            $data['deadline'],
            $data['image'] ?? null,
        ];

        $this->query($sql, $params);
        return $this->lastInsertId();
    }

    // Récupère un défi avec le pseudo de celui qui l'a créé
    public function findById($id) {
        $sql = "SELECT c.*, u.username, u.avatar
                FROM challenges c
                JOIN users u ON c.user_id = u.id
                WHERE c.id = ?";
        
        return $this->query($sql, [$id])->fetch();
    }

    // Récupération de la liste avec filtres (keyword, catégorie, ...)
    public function getAll($filters = [], $page = 1) {
        // Construction dynamique de la clause WHERE
        $where = "1=1";
        $params = [];

        if (!empty($filters['keyword'])) {
            $where .= " AND (c.title LIKE ? OR c.description LIKE ?)";
            $params[] = "%" . $filters['keyword'] . "%";
            $params[] = "%" . $filters['keyword'] . "%";
        }

        if (!empty($filters['category'])) {
            $where .= " AND c.category = ?";
            $params[] = $filters['category'];
        }

        // Tri (par défaut : plus récent)
        $orderBy = "c.created_at DESC";
        if (isset($filters['sort'])) {
            if ($filters['sort'] === 'oldest') $orderBy = "c.created_at ASC";
            if ($filters['sort'] === 'popular') $orderBy = "submissions_count DESC";
        }

        // Pagination
        $perPage = PER_PAGE;
        $offset = ($page - 1) * $perPage;

        // Requête complète avec jointure pour avoir l'auteur et compte des participations
        $sql = "SELECT c.*, u.username, u.avatar,
                       (SELECT COUNT(*) FROM submissions WHERE challenge_id = c.id) AS submissions_count
                FROM challenges c
                JOIN users u ON c.user_id = u.id
                WHERE $where
                ORDER BY $orderBy
                LIMIT $perPage OFFSET $offset";

        return $this->query($sql, $params)->fetchAll();
    }

    // Pour savoir combien il y a de défis au total (utile pour les pages)
    public function countAll($filters = []) {
        $where = "1=1";
        $params = [];

        if (!empty($filters['keyword'])) {
            $where .= " AND (title LIKE ? OR description LIKE ?)";
            $params[] = "%" . $filters['keyword'] . "%";
            $params[] = "%" . $filters['keyword'] . "%";
        }

        if (!empty($filters['category'])) {
            $where .= " AND category = ?";
            $params[] = $filters['category'];
        }

        $sql = "SELECT COUNT(*) FROM challenges WHERE $where";
        return (int) $this->query($sql, $params)->fetchColumn();
    }

    /**
     * Récupère les défis créés par un utilisateur spécifique
     */
    public function getByUser($userId) {
        $sql = "SELECT c.*, 
                (SELECT COUNT(*) FROM submissions WHERE challenge_id = c.id) AS submissions_count
                FROM challenges c
                WHERE c.user_id = ?
                ORDER BY c.created_at DESC";
        
        return $this->query($sql, [$userId])->fetchAll();
    }

    /**
     * Récupère la liste des catégories existantes
     */
    public function getCategories() {
        $sql = "SELECT DISTINCT category FROM challenges ORDER BY category";
        return $this->query($sql)->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Récupère les défis récents (pour la page d'accueil)
     */
    public function getRecent($limit = 6) {
        $sql = "SELECT c.*, u.username, u.avatar
                FROM challenges c
                JOIN users u ON c.user_id = u.id
                ORDER BY c.created_at DESC
                LIMIT $limit";
        
        return $this->query($sql)->fetchAll();
    }

    /**
     * Met à jour un défi
     */
    public function update($id, $data) {
        $fields = [];
        $params = [];

        if (isset($data['title']))       { $fields[] = 'title = ?';       $params[] = $data['title'];       }
        if (isset($data['description'])) { $fields[] = 'description = ?'; $params[] = $data['description']; }
        if (isset($data['category']))    { $fields[] = 'category = ?';    $params[] = $data['category'];    }
        if (isset($data['deadline']))    { $fields[] = 'deadline = ?';    $params[] = $data['deadline'];    }
        if (isset($data['image']))       { $fields[] = 'image = ?';       $params[] = $data['image'];       }

        if (empty($fields)) return false;

        $params[] = $id;
        $sql = "UPDATE challenges SET " . implode(', ', $fields) . " WHERE id = ?";
        return $this->query($sql, $params)->rowCount() > 0;
    }

    /**
     * Supprime un défi
     */
    public function delete($id) {
        return $this->deleteById('challenges', $id);
    }
}
