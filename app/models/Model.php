<?php

// NOTRE MODÈLE PARENT
// Il contient toutes les fonctions de base pour parler à la base de données
abstract class Model {
    // Stocke la connexion PDO
    protected $db;

    public function __construct() {
        // On récupère la connexion unique ici
        $this->db = Database::getInstance();
    }

    // On exécute une requête SQL (prévient les injections SQL)
    protected function query($sql, $params = []) {
        // Préparation de la requête (Protection contre les injections SQL)
        $stmt = $this->db->prepare($sql);
        
        // Exécution avec les paramètres
        $stmt->execute($params);
        
        return $stmt;
    }

    // Pour trouver un élément par son ID plus facilement
    protected function findInTable($table, $id) {
        $stmt = $this->query("SELECT * FROM {$table} WHERE id = ?", [$id]);
        return $stmt->fetch();
    }

    /**
     * Supprime un enregistrement par son ID
     */
    protected function deleteById($table, $id) {
        $stmt = $this->query("DELETE FROM {$table} WHERE id = ?", [$id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Récupère le dernier ID généré (auto-incrément)
     */
    protected function lastInsertId() {
        return $this->db->lastInsertId();
    }
}
