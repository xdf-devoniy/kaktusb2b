<?php

class Settings
{
    public function __construct(private Database $db)
    {
    }

    public function all(): array
    {
        $stmt = $this->db->getConnection()->query('SELECT key, value FROM settings');
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function save(array $data): void
    {
        $stmt = $this->db->getConnection()->prepare('INSERT INTO settings(key, value) VALUES(:key, :value)
            ON CONFLICT(key) DO UPDATE SET value = excluded.value');
        foreach ($data as $key => $value) {
            $stmt->execute(['key' => $key, 'value' => $value]);
        }
    }
}
