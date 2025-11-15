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
}
