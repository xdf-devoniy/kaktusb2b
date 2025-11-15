<?php

class User
{
    public function __construct(private Database $db)
    {
    }

    public function all(): array
    {
        $stmt = $this->db->getConnection()->query('SELECT * FROM users ORDER BY name');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->getConnection()->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO users (name, username, password_hash, role, phone, aklad_monthly, rate_per_m2, rate_per_meter, sales_percent, is_active) ' .
            'VALUES (:name, :username, :password_hash, :role, :phone, :aklad_monthly, :rate_per_m2, :rate_per_meter, :sales_percent, :is_active)';

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([
            'name' => $data['name'],
            'username' => $data['username'],
            'password_hash' => $data['password_hash'],
            'role' => $data['role'],
            'phone' => $data['phone'] ?? null,
            'aklad_monthly' => $data['aklad_monthly'] ?? 0,
            'rate_per_m2' => $data['rate_per_m2'] ?? 0,
            'rate_per_meter' => $data['rate_per_meter'] ?? 0,
            'sales_percent' => $data['sales_percent'] ?? 0,
            'is_active' => $data['is_active'] ?? 1,
        ]);

        return (int)$this->db->getConnection()->lastInsertId();
    }
}
