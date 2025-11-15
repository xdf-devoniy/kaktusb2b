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
}
