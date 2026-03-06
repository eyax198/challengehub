<?php

// Gestion de la connexion PDO avec le pattern Singleton
// On utilise cette classe pour avoir une seule connexion ouverte
class Database {
    // On garde l'instance ici pour la réutiliser
    private static $instance = null;

    // Paramètres de connexion
    private $host     = 'localhost';
    private $dbName   = 'challengehub_db';
    private $username = 'root';
    private $password = '';

    // On bloque le "new Database()" pour forcer l'usage du Singleton
    private function __construct() { }

    /**
     * Empêche le clonage de l'objet
     */
    private function __clone() {
        // Vide
    }

    // Fonction pour récupérer la connexion (crée l'instance si elle n'existe pas)
    public static function getInstance() {
        if (self::$instance === null) {
            try {
                // Paramètres de connexion (DSN)
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
