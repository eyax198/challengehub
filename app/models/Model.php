<?php

/**
 * Classe Model (Classe parente)
 * Contient les outils de base pour communiquer avec la base de données.
 */
abstract class Model {
    // Stocke la connexion PDO
    protected $db;

    public function __construct() {
        // On récupère la connexion via le Singleton
        $this->db = Database::getInstance();
    }

    /**
     * Exécute une requête SQL préparée
     * @param string $sql La requête (ex: SELECT * FROM table WHERE id = ?)
     * @param array $params Les valeurs à injecter dans les points d'interrogation
     * @return PDOStatement L'objet résultat de PDO
     */
    protected function query($sql, $params = []) {
        // Préparation de la requête (Protection contre les injections SQL)
        $stmt = $this->db->prepare($sql);
        
        // Exécution avec les paramètres
        $stmt->execute($params);
        
        return $stmt;
    }

    /**
     * Récupère un enregistrement par son ID (Outil générique)
     */
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
