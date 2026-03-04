<?php

require_once ROOT_PATH . '/config/database.php';

abstract class Model {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Execute a prepared statement and return the statement object.
     */
    protected function query(string $sql, array $params = []): PDOStatement {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    protected function findById(string $table, int $id): array|false {
        return $this->query("SELECT * FROM `{$table}` WHERE id = ?", [$id])->fetch();
    }

    protected function deleteById(string $table, int $id): bool {
        return $this->query("DELETE FROM `{$table}` WHERE id = ?", [$id])->rowCount() > 0;
    }

    protected function lastInsertId(): string {
        return $this->db->lastInsertId();
    }
}
