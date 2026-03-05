<?php

/**
 * Classe Database - Gestion de la connexion PDO
 * Utilise le Design Pattern "Singleton" pour garantir une seule connexion.
 */
class Database {
    // Variable statique pour stocker l'unique instance de la connexion
    private static $instance = null;

    // Paramètres de connexion
    private $host     = 'localhost';
    private $dbName   = 'challengehub_db';
    private $username = 'root';
    private $password = '';

    /**
     * Le constructeur est privé pour empêcher l'instanciation directe (via new Database())
     */
    private function __construct() {
        // Vide
    }

    /**
     * Empêche le clonage de l'objet
     */
    private function __clone() {
        // Vide
    }

    /**
     * Méthode statique pour récupérer l'instance de PDO
     * @return PDO
     */
    public static function getInstance() {
        if (self::$instance === null) {
            try {
                // Création d'une instance temporaire pour accéder aux propriétés privées
                $db = new self();
                $dsn = "mysql:host=" . $db->host . ";dbname=" . $db->dbName . ";charset=utf8mb4";

                // Configuration de PDO
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Génère des erreurs si SQL échoue
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retourne les résultats en tableaux associatifs
                    PDO::ATTR_EMULATE_PREPARES   => false,                 // Utilise les vraies requêtes préparées de MySQL
                ];

                // Création de l'objet PDO unique
                self::$instance = new PDO($dsn, $db->username, $db->password, $options);

            } catch (PDOException $e) {
                // En cas d'erreur de connexion
                die("Erreur de connexion à la base de données : " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
