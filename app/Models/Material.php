<?php

class Material
{
    public function __construct(private Database $db)
    {
    }

    public function all(): array
    {
        $stmt = $this->db->getConnection()->query('SELECT * FROM materials ORDER BY code');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
