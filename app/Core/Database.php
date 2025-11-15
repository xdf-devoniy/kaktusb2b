<?php

class Database
{
    private PDO $pdo;

    public function __construct(string $path)
    {
        $needsInit = !file_exists($path);
        $this->pdo = new PDO('sqlite:' . $path);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($needsInit) {
            $this->initializeSchema();
        }

        $this->ensureDefaultAdmin();
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    private function initializeSchema(): void
    {
        $schema = file_get_contents(__DIR__ . '/../../database/schema.sql');
        if ($schema) {
            $this->pdo->exec($schema);
        }
    }

    private function ensureDefaultAdmin(): void
    {
        $tableExists = $this->pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users' LIMIT 1")
            ->fetchColumn();

        if (!$tableExists) {
            return;
        }

        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM users WHERE username = :username');
        $stmt->execute(['username' => 'adminkaktus']);

        if ($stmt->fetchColumn() > 0) {
            return;
        }

        $insert = $this->pdo->prepare('INSERT INTO users (name, username, password_hash, role, phone, is_active) ' .
            'VALUES (:name, :username, :password_hash, :role, :phone, :is_active)');

        $insert->execute([
            'name' => 'Asosiy admin',
            'username' => 'adminkaktus',
            'password_hash' => password_hash('adminkaktus24', PASSWORD_BCRYPT),
            'role' => 'admin',
            'phone' => null,
            'is_active' => 1,
        ]);
    }
}
